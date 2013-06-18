<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityController.php 2011-09-26 20:37:57Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_ActivityController extends Core_Controller_Action_Standard {

  public function postAction() {
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid())
      return;

    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if ($subject_guid) {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if (null === $subject) {
      $subject = $viewer;
    }

    // Make form
    $form = $this->view->form = new Activity_Form_Post();

    // Check auth
    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
        $pageSubject = $subject;
        if ($subject->getType() == 'sitepageevent_event')
          $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
        if (empty($isManageAdmin)) {
          return $this->_helper->requireAuth()->forward();
        }
      } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
        $businessSubject = $subject;
        if ($subject->getType() == 'sitebusinessevent_event')
          $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
        if (empty($isManageAdmin)) {
          return $this->_helper->requireAuth()->forward();
        }
      } else if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
        return $this->_helper->requireAuth()->forward();
      }
    }


    // Check if post
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }
    if (!Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
      // Check token
      if (!($token = $this->_getParam('token'))) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
        return;
      }
      $session = new Zend_Session_Namespace('ActivityFormToken');
      if ($token != $session->token) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid token, please try again');
        return;
      }
      $session->unsetAll();
    }
    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$postData['body'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;

    if (!$form->isValid($postData)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check one more thing
    if ($form->body->getValue() === '' && $form->getValue('attachment_type') === '') {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // set up action variable
    $action = null;

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');

      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $type_temp = $attachmentData['type'];

        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {

          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
          $config = null;
        }

        if ($config) {
          $typeExplode = explode("-", $type);
          for ($i = 1; $i < count($typeExplode); $i++)
            $typeExplode[$i] = ucfirst($typeExplode[$i]);
          $type = implode("", $typeExplode);
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
        }
      }


      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // Is double encoded because of design mode
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      // Special case: status
      $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
      if (!$attachment && $viewer->isSelf($subject)) {
        if ($body != '') {
          $viewer->status = $body;
          $viewer->status_date = date('Y-m-d H:i:s');
          $viewer->save();

          $viewer->status()->setStatus($body);
        }

        $action = $activityTable->addActivity($viewer, $subject, 'status', $body);
      } else { // General post
        $type = 'post';
        if ($viewer->isSelf($subject)) {
          $type = 'post_self';
        }

        // Add notification for <del>owner</del> user
        $subjectOwner = $subject->getOwner();

        if (!$viewer->isSelf($subject) &&
                $subject instanceof User_Model_User) {
          $notificationType = 'post_' . $subject->getType();
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
              'url1' => $subject->getHref(),
          ));
        }

        // Add activity
        if ($subject->getType() == "sitepage_page") {
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepage_post_self';
          elseif ($subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($subject))
            $activityFeedType = 'sitepage_post';

          if ($activityFeedType) {
            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }
        } else if ($subject->getType() == "sitebusiness_business") {
          $activityFeedType = null;
          if (Engine_Api::_()->sitebusiness()->isBusinessOwner($subject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
            $activityFeedType = 'sitebusiness_post_self';
          elseif ($subject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($subject))
            $activityFeedType = 'sitebusiness_post';

          if ($activityFeedType) {
            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body);
            Engine_Api::_()->getApi('subCore', 'sitebusiness')->deleteFeedStream($action);
          }
        } else {
          $action = $activityTable->addActivity($viewer, $subject, $type, $body);
        }
        // Try to attach if necessary
        if ($action && $attachment) {
          $activityTable->attachActivity($action, $attachment);
        }
      }

      // Publish to facebook, if checked & enabled
      if ($this->_getParam('post_to_facebook', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
        try {

          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookApi = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

          if ($fb_uid &&
                  $fb_uid->facebook_uid &&
                  $facebookApi &&
                  $facebookApi->getUser() &&
                  $facebookApi->getUser() == $fb_uid->facebook_uid) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->getFrontController()->getBaseUrl();
            $name = 'Activity Feed';
            $desc = '';
            $picUrl = $viewer->getPhotoUrl('thumb.icon');
            if ($attachment) {
              $url = $attachment->getHref();
              $desc = $attachment->getDescription();
              $name = $attachment->getTitle();
              if (empty($name)) {
                $name = ucwords($attachment->getShortType());
              }
              $tmpPicUrl = $attachment->getPhotoUrl();
              if ($tmpPicUrl) {
                $picUrl = $tmpPicUrl;
              }
              // prevents OAuthException: (#100) FBCDN image is not allowed in stream
              if (preg_match('/fbcdn.net$/i', parse_url($picUrl, PHP_URL_HOST))) {
                $picUrl = $viewer->getPhotoUrl('thumb.icon');
              }
            }

            // Check stuff
            if (false === stripos($url, 'http://')) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if (false === stripos($picUrl, 'http://')) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // include the site name with the post:
            $name = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                    . ": $name";

            $fb_data = array(
                'message' => html_entity_decode($form->getValue('body')),
                'link' => $url,
                'name' => $name,
                'description' => $desc,
            );

            if ($picUrl) {
              $fb_data = array_merge($fb_data, array('picture' => $picUrl));
            }

            if ($type_temp == 'music' && !empty($attachment) && !empty($attachment->file_id)) {
              $source = 'http://' . $_SERVER['HTTP_HOST'] . '/' . Engine_Api::_()->getItem('storage_file', $attachment->file_id)->storage_path;
              $fb_data['source'] = $source;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
          }
        } catch (Exception $e) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if ($this->_getParam('post_to_twitter', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($twitterTable->isConnected()) {
            // @todo truncation?
            // @todo attachment
            $twitter = $twitterTable->getApi();
            $twitter->statuses->update(html_entity_decode($form->getValue('body')));
          }
        } catch (Exception $e) {
          // Silence
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }



    // If we're here, we're done
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');

    // Check if action was created
    $post_fail = "";
    if (!$action) {
      $post_fail = "?pf=1";
    }

    // Redirect if in normal context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $return_url = $form->getValue('return_url', false);
      if ($return_url) {
        return $this->_helper->redirector->gotoUrl($return_url . $post_fail, array('prependBase' => false));
      }
    }
  }

  /**
   * Handles HTTP POST request to share an activity feed item
   *
   * Uses the default route and can be accessed from
   *  - /activity/index/share
   *
   * @return void
   */
  public function shareAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $type = $this->_getParam('type');
    $id = $this->_getParam('id');
    $parent_action_id = $this->_getparam('action_id', null);
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    $this->view->form = $form = new Activity_Form_Share();

    if (!$attachment) {
      // tell smoothbox to close
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }


     // hide facebook and twitter option if not logged in
     $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
     $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
     $fb_uid = $facebookTable->find($viewer->getIdentity())->current();
     if (!$facebook || !Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebook)) {    
      $form->removeElement('post_to_facebook');
    }
    
    if (class_exists('User_Model_DbTable_Twitter')) {
      $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    }
    if (!isset($twitterTable) || !$twitterTable->isConnected()) {
      $form->removeElement('post_to_twitter');
    }



    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process

    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      // Get body
      $body = $form->getValue('body');
// Set Params for Attachment

      if (method_exists($attachment, 'getMediaType')) {
        $lable = $attachment->getMediaType();
      } else {
        $lable = $this->getMediaType($attachment);
      }
      
      if(empty ($lable))
        $lable = $attachment->getShortType();
      $params = array(
          'type' => '<a href="' . $attachment->getHref() . '" class="sea_add_tooltip_link feed_' . $attachment->getType() . '_title"  rel="'.$attachment->getType().' '.$attachment->getIdentity().'" >' . $lable . '</a>',
      );
      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      // $action = $api->addActivity($viewer, $viewer, 'post_self', $body);
      $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
      if ($action) {
        $api->attachActivity($action, $attachment);
        if (!empty($parent_action_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
          $shareTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity');
          $shareTable->insert(array(
              'resource_type' => (string) $type,
              'resource_id' => (int) $id,
              'parent_action_id' => $parent_action_id,
              'action_id' => $action->action_id,
          ));
        }
      }
      $db->commit();
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      // Add notification for owner of activity (if user and not viewer)
      if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
            'label' => $lable,
        ));
      }

      // Preprocess attachment parameters
      $publishMessage = html_entity_decode($form->getValue('body'));
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if ($attachment) {
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if (empty($publishName)) {
          $publishName = ucwords($attachment->getShortType());
        }
        if (($tmpPicUrl = $attachment->getPhotoUrl())) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if ($publishPicUrl &&
                preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
          $publishPicUrl = null;
        }
      } else {
        $publishUrl = $action->getHref();
      }
      // Check to ensure proto/host
      if ($publishUrl &&
              false === stripos($publishUrl, 'http://') &&
              false === stripos($publishUrl, 'https://')) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if ($publishPicUrl &&
              false === stripos($publishPicUrl, 'http://') &&
              false === stripos($publishPicUrl, 'https://')) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if ($publishName) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }


      // Publish to facebook, if checked & enabled
      if ($this->_getParam('post_to_facebook', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
        try {
          $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();         
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');          
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

          if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)
                  ) {
            $fb_data = array(
                'message' => $publishMessage,
            );
            if ($publishUrl) {
              $fb_data['link'] = $publishUrl;
            }
            if ($publishName) {
              $fb_data['name'] = $publishName;
            }
            if ($publishDesc) {
              $fb_data['description'] = strip_tags($publishDesc);
            }
            if ($publishPicUrl) {
              $fb_data['picture'] = $publishPicUrl;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
            if ($subject && isset($subject->fbpage_id) && !empty($subject->fbpage_id)) {
              $manages_pages = $facebookApi->api('/me/accounts', 'GET');
              //NOW GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED:

              foreach ($manages_pages['data'] as $page) {
                if ($page['id'] == $subject->fbpage_id) {
                  $fb_data['access_token'] = $page['access_token'];
                  $res = $facebookApi->api('/' . $subject->fbpage_id . '/feed', 'POST', $fb_data);
                  break;
                }
              }
            }
          }
        } catch (Exception $e) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if ($this->_getParam('post_to_twitter', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($twitterTable->isConnected()) {

            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();

            // Check stuff
            if ($url && false === stripos($url, 'http://')) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if ($picUrl && false === stripos($picUrl, 'http://')) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
              if ($picUrl) {
                $message .= ' - ' . $picUrl;
              }
            } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            } else {
              if (strlen($title) > 24) {
                $title = Engine_String::substr($title, 0, 21) . '...';
              }
              // Sigh truncate I guess
              if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
              }
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            }

            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          }
        } catch (Exception $e) {
          // Silence
        }
      }


      // Publish to janrain
      if (//$this->_getParam('post_to_janrain', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
        try {
          $session = new Zend_Session_Namespace('JanrainActivity');
          $session->unsetAll();

          $session->message = $publishMessage;
          $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
          $session->name = $publishName;
          $session->desc = $publishDesc;
          $session->picture = $publishPicUrl;
        } catch (Exception $e) {
          // Silence
        }
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }

    // If we're here, we're done
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');
    // Redirect if in normal context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $return_url = $form->getValue('return_url', false);
      if (!$return_url) {
        $return_url = $this->view->url(array(), 'default', true);
      }
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext() && $this->_getParam('not_parent_refresh', 0)) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          //  'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Share successfully.'))
      ));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
  }

  /**
   * Get a generic media type. Values:
   * audio, image, video, news, blog
   *
   * @return string
   */
  public function getMediaType($item) {
    $type = $item->getType();
    if (strpos($type, 'photo') !== false) {
      return 'image';
    } else if (strpos($type, 'video') !== false) {
      return 'video';
    } else if (strpos($type, 'blog') !== false) {
      return 'blog';
    } else if (strpos($type, 'link') !== false) {
      return 'link';
    } else if (strpos($type, 'activity_action') !== false) {
      return 'post';
    } else {
      return '';
    }
  }

}