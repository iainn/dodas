<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_Invite extends Core_Api_Abstract {

   //THIS FUNCTION IS USED TO SAVE THE FRIEND REQUEST, AND PERFORM ALLIED ACTIONS FOR NOTIFICATION UPDATES, ETC.
  public function addAction($id) {
	  $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

  	// Get id of friend to add
  	$user_id = $id;
  	if (null == $user_id) {
  	  $view->status = false;
  	  $view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
  	  return;
  	}
  
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$user = Engine_Api::_()->user()->getUser($user_id);
  
  	// check that user is not trying to befriend 'self'
  	if ($viewer->isSelf($user)) {
  	  return;
  	}
  
  	// check that user is already friends with the member
  	if ($user->membership()->isMember($viewer)) {
  	  return;
  	}
  
  	// check that user has not blocked the member
  	if ($viewer->isBlocked($user)) {
  	  return;
  	}

  	// Process
  	$db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
  	$db->beginTransaction();

	 try {
  	  // check friendship verification settings
  	  // add membership if allowed to have unverified friendships
  	  //$user->membership()->setUserApproved($viewer);
  	  // else send request
  	  $user->membership()->addMember($viewer)->setUserApproved($viewer);
  
  
  	  // send out different notification depending on what kind of friendship setting admin has set
  	  /* ('friend_accepted', 'user', 'You and {item:$subject} are now friends.', 0, ''),
  	    ('friend_request', 'user', '{item:$subject} has requested to be your friend.', 1, 'user.friends.request-friend'),
  	    ('friend_follow_request', 'user', '{item:$subject} has requested to add you as a friend.', 1, 'user.friends.request-friend'),
  	    ('friend_follow', 'user', '{item:$subject} has added you as a friend.', 1, 'user.friends.request-friend'),
  	   */


  	  // if one way friendship and verification not required
  	  if (!$user->membership()->isUserApprovalRequired() && !$user->membership()->isReciprocal()) {
  		// Add activity
  		Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends_follow', '{item:$object} is now following {item:$subject}.');
  
  		// Add notification
  		Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');
  
  		$message = "You are now following this member.";
  	  }
  
  	  // if two way friendship and verification not required
  	  else if (!$user->membership()->isUserApprovalRequired() && $user->membership()->isReciprocal()) {
  		// Add activity
  		Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
  		Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
  
  		// Add notification
  		Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
  	  }
  
  	  // if one way friendship and verification required
  	  else if (!$user->membership()->isReciprocal()) {
  		// Add notification
  		Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
  	  }
  
  	  // if two way friendship and verification required
  	  else if ($user->membership()->isReciprocal()) {
  		// Add notification
  		Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
  	  }
  	  $view->status = true;
  	  $db->commit();
  	} catch (Exception $e) {
  	  $db->rollBack();
  	  $view->status = false;
  	  $view->exception = $e->__toString();
  	}
  }
  
  
  public function sendInvites($recipients, $service, $module = '') { 
      set_time_limit(0);
      //FINDING THE INVITE TYPE WHICH IS GOING TO BE SENT EITHER USER INVITE OR PAGE INVITE OR SOMETHING ELSE.
      $invite_type = 'user_invite';      
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    	$user = Engine_Api::_()->user()->getViewer();
    	$settings = Engine_Api::_()->getApi('settings', 'core');
    	$translate = Zend_Registry::get('Zend_Translate');
    	$message = $view->translate(Engine_Api::_()->getApi('settings', 'core')->invite_message);
    	$message = trim($message);
    	$inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
    	if (is_array($recipients) && !empty($recipients)) {
    	  // Initiate objects to be used below
    	  $table = Engine_Api::_()->getDbtable('invites', 'invite');
    	  $db = $table->getAdapter();
    	  // Iterate through each recipient
    	  //$already_members       = Engine_Api::_()->invite()->findIdsByEmail($recipients);
    	  //$this->already_members = Engine_Api::_()->user()->getUserMulti($already_members);
    	  $emailsSent = 0;
    
    	  foreach ($recipients as $recipient => $recipient_name) {
    		// perform tests on each recipient before sending invite
    		$recipient = trim($recipient);
    		// watch out for poorly formatted emails
    		if (!empty($recipient)) { 
          //CHECK IF THE USER IS ALREADY INVITED. IF ALREADY INVITED THEN UPDATE THE ROW:
           $row = $table->fetchRow(array('recipient = ?' => $recipient));
           
           if (!empty($row)) { 
              $inviteCode = $row->code;
              $table->update(array(
                'timestamp' => new Zend_Db_Expr('NOW()'),
                 
                    ), array(
                'recipient = ?' => $recipient,
            ));
           }
           else {
    		  // Passed the tests, lets start inserting database entry
    		  // generate unique invite code and confirm it truly is unique
    		  do {
    			$inviteCode = substr(md5(rand(0, 999) . $recipient), 10, 7);
    		  } while (null !== $table->fetchRow(array('code = ?' => $inviteCode)));
    		  // Friend Inviter: Functions starts from here.
        
    		  $row = $table->createRow();
    		  $row->user_id = $user->getIdentity();
    		  $row->recipient = $recipient;
    		  $row->code = $inviteCode;
    		  $row->timestamp = new Zend_Db_Expr('NOW()');
    		  $row->message = $message;
          if (isset($row->service))
            $row->service = $service;
          if (isset($row->invite_type))
            $row->invite_type = $invite_type;
          if (isset($row->displayname))
            $row->displayname = $recipient_name;
          
    		  $row->save();
           }
    		  try {
    			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    			$coreversion = $coremodule->version;
    			if ($coreversion < '4.1.8') {
    			  $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
    					  . $_SERVER['HTTP_HOST']
    					  . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
    						  'module' => 'invite',
    						  'controller' => 'signup',
    							  ), 'default', true)
    					  . '?'
    					  . http_build_query(array('code' => $inviteCode, 'email' => $recipient))
    			  ;
    			} else {
    			  $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
    						  'module' => 'invite',
    						  'controller' => 'signup',
    							  ), 'default', true)
    					  . '?'
    					  . http_build_query(array('code' => $inviteCode, 'email' => $recipient))
    			  ;
    			}
    
    			$message = str_replace('%invite_url%', $inviteUrl, $message);
    
    			// Send mail
    			$mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );
    			$mailParams = array(
    				'host' => $_SERVER['HTTP_HOST'],
    				'email' => $recipient,
    				'date' => time(),
    				'sender_email' => $user->email,
    				'sender_title' => $user->getTitle(),
    				'sender_link' => $user->getHref(),
    				'sender_photo' => $user->getPhotoUrl('thumb.icon'),
    				'message' => $message,
    				'object_link' => $inviteUrl,
    				'code' => $inviteCode,
    			);
    
    			Engine_Api::_()->getApi('mail', 'core')->sendSystem(
    					$recipient,
    					$mailType,
    					$mailParams
    			);
    			$db->commit();
    		  } catch (Exception $e) {
    			// Silence
    			if (APPLICATION_ENV == 'development') {
    			  throw $e;
    			}
    			continue;
    		  }
    		  $emailsSent++;
    		} // end if (!array_key_exists($recipient, $already_members))
    	  } // end foreach ($recipients as $recipient)
    	} // end if (is_array($recipients) && !empty($recipients))
    
    	if (isset($user->invites_used)) {
    	  $user->invites_used += $emailsSent;
    	}
    	$user->save();
    	return;
  }
  
  public function sendPageInvites($recipients, $pageinvite_id = null, $moduletype = null, $invite_message=null) {  
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
    $message = $view->translate(Engine_Api::_()->getApi('settings', 'core')->invite_message);
    $message = trim($message);

    $template_header = '';
    $template_footer = '';
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $site__template_title = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduletype . '.site.title', $site_title);
    $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduletype . '.title.color', "#ffffff");
    $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduletype . '.font.color', "#79b4d4");
    $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='#f7f7f7' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
    $template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site__template_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";
    $inviter_name = $viewer->getTitle();
    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    if ($pageinvite_id) {
      $sitepage = Engine_Api::_()->getItem($moduletype . '_page', $pageinvite_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }
    $sitepage = Engine_Api::_()->core()->getSubject($moduletype . '_page');
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id),
                    ), $moduletype . '_entry_view', true);

    //GETTING THE PAGE PHOTO.
     $file = $sitepage->getPhotoUrl('thumb.icon');
    if (empty($file)) {
      $pagephoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Sitepage/externals/images/nophoto_list_thumb_normal.png';
    } else {
      $pagephoto_path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $file;
    }

    $inviteUrl_link = '<table><tr valign="top"><td style="color:#999999;font-size:11px;padding-right:15px;"><a href = ' . $inviteUrl . ' target="_blank">' . '<img src="' . $pagephoto_path . '" style="width:100px;"/>' . '</a>';
    //GETTING NO OF LIKES TO THIS PAGE.
    if ($moduletype == 'sitepage')
      $num_of_like = Engine_Api::_()->sitepage()->numberOfLike($moduletype . '_page', $sitepage->page_id);
    else 
       $num_of_like = Engine_Api::_()->sitebusiness()->numberOfLike($moduletype . '_page', $sitepage->page_id); 
    $body_message = $inviteUrl_link . $sitepage->title . '<br /> ' . $view->translate(array('%s Person Likes This', '%s People Like This', $num_of_like), $view->locale()->toNumber($num_of_like));

    $recepients_array = array();

    $site_title_linked = '<a href="' . $base_url . '" target="_blank" >' . $site_title . '</a>';
    $page_title_linked = '<a href="' . $inviteUrl . '" target="_blank" >' . $sitepage->title . '</a>';
    $page_link = '<a href="' . $inviteUrl . '" target="_blank" >' . $inviteUrl . '</a>';
    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduletype . 'invite.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($moduletype . 'invite.utility.type', convert_uuencode($sitepageModHostName));
    }

    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
    if (is_array($recipients) && !empty($recipients) && !empty($pageinvite_id) ) {

      $table_message = Engine_Api::_()->getDbtable('messages', 'messages');
      $tableName_message = $table_message->info('name');

      $table_user = Engine_Api::_()->getitemtable('user');
      $tableName_user = $table_user->info('name');

      $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');
      $tableName_user_memberships = $table_user_memberships->info('name');

      foreach ($recipients as $recipient => $recipient_name) {
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);
        $recipient_name_array = explode("-", $recipient_name); 
        // watch out for poorly formatted emails
        if (!empty($recipient_name_array[0])) {
          //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
          $select = $table_user->select()
                  ->setIntegrityCheck(false)
                  ->from($tableName_user, array('user_id'))
                  ->where('email = ?', $recipient_name_array[0]);
          $is_site_members = $table_user->fetchAll($select);

          //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
          if (!empty($is_site_members[0]->user_id) && $is_site_members[0]->user_id != $viewer->user_id) {
            $contact = Engine_Api::_()->user()->getUser($is_site_members[0]->user_id);

            // check that user has not blocked the member
            if (!$viewer->isBlocked($contact)) {
              $recepients_array[] = $is_site_members[0]->user_id;
              $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');

              // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND
              if ($is_suggenabled) {
                Engine_Api::_()->sitepageinvite()->sendSuggestion($is_site_members[0], $viewer, $sitepage->page_id);
              }
              // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND
              else {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members[0], $viewer, $sitepage, $moduletype . '_suggested');
              }
            }
          }
          // BODY OF PAGE COMPRISING LIKES	
          $body = $inviteUrl_link . '<br/>' . $view->translate(array('%s person likes this', '%s people like this', $num_of_like), $view->locale()->toNumber($num_of_like)) . '</td><td>';

          if (!empty($invite_message)) {
            $body .= $invite_message . "<br />";
          }
          $link = '<a href="' . $base_url . '">' . $base_url . '</a>';
          $template_footer.= "</td></tr></table></td></tr></table></td></tr></td></table></td></tr></table>";
         
          // IF THE PERSON IS NOT THE SITE MEMBER
          if (empty($is_site_members[0]->user_id)) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient_name_array[0], strtoupper($moduletype) . 'INVITE_USER_INVITE', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'site_title_linked' => $site_title_linked,
                'page_title_linked' => $page_title_linked,
                'page_link' => $page_link,
                'site_title' => $site_title,
                'body' => $body,
                'page_title' => $sitepage->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
          // IF THE PERSON IS A SITE MEMBER
          else {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, strtoupper($moduletype) . 'INVITE_MEMBER_INVITE', array(
                'template_header' => $template_header,
                'template_footer' => $template_footer,
                'inviter_name' => $inviter_name,
                'page_title_linked' => $page_title_linked,
                'page_link' => $page_link,
                'site_title' => $site_title,
                'body' => $body,
                'page_title' => $sitepage->title,
                'link' => $link,
                'host' => $host,
                'email' => $viewer->email,
                'queue' => true
            ));
          }
        }
      } // end foreach
    } // end if (is_array($recipients) && !empty($recipients))
    return;
  }
  
  //GET THE INVITE STATISTICS OF THE INVITER.
  public function getInviteStatisticInfo ($service = 'all', $task= 'pending', $invite_type = 'user_invite', $page = 1) { 
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    
    $inviteTableName = $inviteTable->info('name');
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $select = $inviteTable->select()
              ->where('user_id = ?', $viewer_id);
     if ($task == 0)
        $select->where('new_user_id = ?', 0);
    else 
     $select->where('new_user_id <> ?', '');
     
    if ($service != 'all')
      $select->where('service = ?', $service);
     $select->group("recipient");
     $select->order('timestamp ASC');
     
     $paginator = $fullMembers = Zend_Paginator::factory($select);
     // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);   
    return $paginator;
    
  }
  
  public function getInviteStatisticSearchInfo ($params = array()) { 
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    
    $inviteTableName = $inviteTable->info('name');
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $select = $inviteTable->select()
              ->where('user_id = ?', $viewer_id);
     if ($params['invite_statistics'] == 0)
        $select->where('new_user_id = ?', 0);
    else 
     $select->where('new_user_id <> ?', '');
     
    if ($params['invite_service'] != 'all')
      $select->where('service = ?', $params['invite_service']);
    if (!empty($params['displayname'])) {
      $select->where("(`{$inviteTableName}`.`displayname` LIKE ?)", "%{$params['displayname']}%");
    }
    if (!empty($params['email'])) {
      $select->where("(`{$inviteTableName}`.`recipient` = ?)", "{$params['email']}");
    }
    if (!empty($params['startdate']['date'])) { 
      $from = date("Y-m-d H:i:s", strtotime($params['startdate']['date']));  
      $select->where("(`{$inviteTableName}`.`timestamp` >= ?)", "{$from}");
    }
    if (!empty($params['endtime']['date'])) { 
      $to = date("Y-m-d H:i:s", strtotime($params['endtime']['date']));
      $select->where("(`{$inviteTableName}`.`timestamp` <= ?)", "{$to}");
    }    
     $select->group("recipient");
     $select->order('timestamp ASC');  
     $paginator = $fullMembers = Zend_Paginator::factory($select);
     // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);   
    return $paginator;
    
  }
  
  //CHECK IF ONLY ADMIN CAN INVITE THE USERS.
  
  public function canInvite()
  {
    // Check if admins only
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.inviteonly') == 1 ) {
      return (bool) Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');
    } else {
      return (bool) Engine_Api::_()->user()->getViewer()->getIdentity();
    }
  }
  
  //GET USER TOTAL INVITES
  
  public function inviteCounts($user_id, $invite_type) {
    //IF INVITE TYPE IS PENDING..
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $inviteTableName = $inviteTable->info('name');
    $select = $inviteTable->select();
    if ($invite_type == 'pending') {
      
      $select->from($inviteTableName , array('COUNT(new_user_id) as pendingInvites'))
             ->where("(`{$inviteTableName}`.`new_user_id` = ?)", 0)
             ->where("(`{$inviteTableName}`.`user_id` = ?)", $user_id);
      $pendingInvites =  $select->query()->fetchColumn();
      return $pendingInvites;
    }
    else if ($invite_type == 'signedup') {
      $select->from($inviteTableName , array('COUNT(new_user_id) as pendingInvites'))
             ->where("(`{$inviteTableName}`.`new_user_id` <> ?)", 0)
             ->where("(`{$inviteTableName}`.`user_id` = ?)", $user_id);
      $signupInvites =  $select->query()->fetchColumn();
      return $signupInvites;
    }
    
  }
  
  //GET USER TOTAL INVITES
  
  public function referredInvites($prev_date, $next_date) {
    //IF INVITE TYPE IS PENDING..
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $inviteTableName = $inviteTable->info('name');
    $select = $inviteTable->select();     
    $select->from($inviteTableName , array('COUNT(new_user_id) as referred'))
           ->where("(`{$inviteTableName}`.`new_user_id` <> ?)", 0)
           ->where($inviteTableName . '.timestamp > ?', gmdate('Y-m-d H:i:s', $prev_date))
           ->where($inviteTableName . '.timestamp <= ?', gmdate('Y-m-d H:i:s', $next_date)); 
    $referredInvites =  $select->query()->fetchColumn();
    return $referredInvites;
    
  }
  

}

?>