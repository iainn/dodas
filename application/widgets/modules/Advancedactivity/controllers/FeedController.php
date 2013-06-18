<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FeedController.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_FeedController extends Core_Controller_Action_Standard {

  public function indexAction() {
    $this->view->someVar = 'someVal';
  }

  public function hideItemAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->type = $type = $this->_getParam('type', null);
    $this->view->id = $id = $this->_getParam('id', null);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $this->view->status = false;
    if (empty($type) || empty($id))
      return;

    Engine_Api::_()->getDbtable('hide', 'advancedactivity')->insert(array(
        'user_id' => $viewer_id,
        'hide_resource_type' => $type,
        'hide_resource_id' => $id
    ));

    exit();
  }

  public function editFeedPrivacyAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
    $privacy = $this->_getParam('privacy', 'null');
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $allowEdit = $coreSettingsApi->getSetting('advancedactivity.post.canedit', 1);
    if (empty($allowEdit)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This user is not allowed to change  the privacy on this feed.');
      return;
    }
    if (empty($action_id) || empty($privacy)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data.');
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
    $action = $actionTable->getActionById($action_id);
    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
    if (!$actionOwner->isSelf($viewer)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data.');
      return;
    }
    try {
      $db = $actionTable->getAdapter();
      $db->beginTransaction();
      $action->privacy = $privacy;
      $action->save();
      if ($action->attachment_count > 0) {
        if (!in_array($privacy, array('everyone', 'networks', 'friends', 'onlyme'))) {
          $privacy = 'onlyme';
        }
        foreach ($action->getAttachments() as $attachment) {
          Engine_Api::_()->advancedactivity()->editContentPrivacy($attachment->item, $viewer, $privacy);
        }
      }
      $actionTable->resetActivityBindings($action);
      $this->view->status = true;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    // Redirect if not json context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
      $helper = 'advancedActivity';
      $this->view->body = $this->view->$helper($action, array('noList' => true));
    }
  }

  public function editHideOptionsAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $hideTable = Engine_Api::_()->getDbtable('hide', 'advancedactivity');

    if (!$this->getRequest()->isPost()) {
      $this->view->hideItems = $hideItems = $hideTable->getHideItemByMember($viewer, array('not_activity_action' => 1));
      return;
    }
    $unhide_items = $_POST['unhide_items'];

    if (!empty($unhide_items)) {
      $unhide_items = explode(',', $unhide_items);
      foreach ($unhide_items as $value) {

        $resource = explode('-', $value);
        $hideTable->delete(array('user_id = ?' => $viewer_id,
            'hide_resource_type =? ' => $resource[0],
            'hide_resource_id =?' => $resource[1]));
      }
    }
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  public function unHideItemAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $hideTable = Engine_Api::_()->getDbtable('hide', 'advancedactivity');
    $type = $this->_getParam('type', null);
    $id = $this->_getParam('id', null);
    $this->view->status = false;
    if (empty($type) || empty($id))
      return;
    $hideTable->delete(array('user_id = ?' => $viewer_id,
        'hide_resource_type =? ' => $type,
        'hide_resource_id =?' => $id));
    exit(0);
  }

  public function tagFriendAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $id = $this->_getParam('id', null);
    if (empty($id))
      return $this->_forward('notfound', 'error', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $action = Engine_Api::_()->getItem('activity_action', $id);
    if ('user' !== $action->subject_type)
      return $this->_forward('notfound', 'error', 'core');
    $this->view->tagMembers = Engine_Api::_()->advancedactivity()->getTag($action);
    $this->view->tagCount = count($this->view->tagMembers);
    if (!$this->getRequest()->isPost()) {
      $this->view->members = $action->getSubject()->membership()->getMembers();
      $this->view->count = count($this->view->members);
      return;
    }

    $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
    $user_ids = array_values(array_unique(explode(",", $_POST['selected_resources'])));

    $users = array();
    if (!empty($user_ids)) {
      $users = Engine_Api::_()->getItemMulti('user', $user_ids);
    }

// 		if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
// 			$addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin'); 
//       $params = array();
//       $params['action_id'] = $id;
//       $params['checkowner'] = 0;
//       $params['owner_id'] = $viewer->getIdentity();
// 			$existingResults = $addLocationTable->getCheckinsDetails($params);
//       if(!empty($existingResults)) {
// 				if(empty($_POST['selected_resources'])) {
// 					foreach($existingResults as $values ) {
// 						$addLocationTable->delete(array(
// 								'owner_id = ?' => $values['owner_id'], 'action_id = ?' => $id ));
// 					}
// 				}  
// 				else {
// 					foreach($existingResults as $values ) {
// 						if(!in_array($values['owner_id'], $user_ids)) {
// 							$addLocationTable->delete(array(
// 									'owner_id = ?' => $values['owner_id'], 'action_id = ?' => $id ));
// 						}
// 					}          
// 				}
//       }
// 
//       $params['checkowner'] = 1;
// 			$rowsContent = $addLocationTable->getCheckinsDetails($params);
// 			foreach($users as $user) {
// 				unset($rowsContent['addlocation_id']);
//         $params['owner_id'] = $user->user_id;
// 				$rowADD = $addLocationTable->getCheckinsDetails($params);
// 				if(empty($rowADD)){   
// 					$content = $addLocationTable->createRow();
// 					$content->setFromArray($rowsContent);
// 					$content->owner_id = $user->user_id;
// 					$content->save();
// 				}
// 			}
// 		}

    $tagsAdded = $actionTag->setTagMaps($action->getSubject(), $users);

    // Add notification
    $type_name = $this->view->translate(str_replace('_', ' ', 'post'));
    $actionNotificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');    
		$params = (array) $action->params;
		if (!(is_array($params) && isset($params['checkin']))) {
			foreach ($tagsAdded as $value) {
				$tag = Engine_Api::_()->getItem($value->tag_type, $value->tag_id);
				if (($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
					$actionNotificationTable->addNotification(
									$tag, $action->getSubject(), $action, 'tagged', array(
							'object_type_name' => $type_name,
							'label' => $type_name,
									)
					);
				}
			}
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Friends in your post have been updated.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  public function removeTagAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $id = $this->_getParam('id', null);
    if (empty($id))
      return $this->_forward('notfound', 'error', 'core');
    $action = Engine_Api::_()->getItem('activity_action', $id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!$this->getRequest()->isPost()) {
      return;
    }
    $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
    $tagMap = $actionTag->getTagMap($viewer);
    if (!empty($tagMap))
      $tagMap->delete();

//     if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
//       //delete the all hide entry belong to user 
//       Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->delete(array(
//           'owner_id = ?' => $viewer_id, 'action_id = ?' => $id ));
//     }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your tag has been removed.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  public function viewMoreResultsAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
    $this->view->action_id = $action_id = $this->_getParam('action_id', null);
    if (empty($action_id))
      return;

    $action = Engine_Api::_()->getItem('activity_action', $action_id);
    if ('user' !== $action->subject_type)
      return;

    $table = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $select = $table->select()
            ->where('resource_type = ?', $action->getType())
            ->where('resource_id = ?', $action->getIdentity())
            ->order('creation_date 	DESC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(15);
    $paginator->setCurrentPageNumber($this->view->page);
    $this->view->count = $paginator->getTotalItemCount();
  }

  //For show to other fiend post.
  public function getOtherPostAction() {

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
    $this->view->object_id = $object_id = $this->_getParam('id');
    //$this->view->type = $type = $this->_getParam('type');
    if (empty($object_id)) {
      return;
    }
    //$temp_id = $this->_getParam('temp_id');
    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityName = $activityTable->info('name');

    $activity_select = $activityTable->select()
            ->where($activityName . '.type = ?', 'birthday_post')
            ->where($activityName . '.object_id = ?', $object_id)
            // ->where( $activityName . '.subject_id != ?' , $temp_id )
            ->group($activityName . '.subject_id')
            ->order($activityName . '.date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($activity_select);
    $this->view->page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->view->page);
    $this->view->count = $paginator->getTotalItemCount();
  }
  
  //For show to group feed of other post.
  public function groupfeedOtherPostAction() {

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    //GET THE BASE URL.
    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    
    $ids = $_GET['ids']; 
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);
    $this->view->object_id = $object_id = $this->_getParam('id');
    $this->view->type = $type = $this->_getParam('type');
    
    if (empty($object_id) && (empty($type))) {
      return;
    }
    
    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersName = $usersTable->info('name');
    $user_select = $usersTable->select()->from($usersName)
			           ->where($usersName . '.user_id IN (?)', (array) $ids);

    $this->view->paginator = $paginator = Zend_Paginator::factory($user_select);
    $this->view->page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->view->page);
    $this->view->count = $paginator->getTotalItemCount();
  }
}