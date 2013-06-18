<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FollowController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_FollowController extends Core_Controller_Action_Standard {

	  public function getFollowersAction() {

    //GET VALUES
    $follow_user_str = 0 ;
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $this->view->page = $page = $this->_getParam('page' , 1 );
    $this->view->search = $search = $this->_getParam('search' , '' );
    $this->view->is_ajax = $this->_getParam('is_ajax' , 0 );
    $this->view->call_status = $call_status = $this->_getParam('call_status');
    
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
    
    //HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NO OF FRIEND
    $this->view->paginator = $followTable->getFollowDetails($call_status, $resource_type, $resource_id, $viewer_id, $search);
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->paginator->setItemCountPerPage(10);

    //NUMBER OF FRIEND WHICH FOLLOWD THIS CONTENT.
    $this->view->totalFollowCount = $followTable->numberOfFollow($resource_type, $resource_id);

    //NUMBER OF MY FRIEND WHICH FOLLOWD THIS CONTENT.
    $this->view->totalFriendsFollow = $followTable->numberOfFriendsFollow($resource_type, $resource_id);

    //FIND OUT THE TITLE OF FOLLOWS.
    $this->view->resourceTitle = Engine_Api::_()->getItem($resource_type, $resource_id)->getTitle();
  }

  //ACTION FOR GLOBALLY FOLLOW THE LISTING
  public function globalFollowsAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET THE VALUE OF RESOURCE ID AND TYPE 
    $resource_id = $this->_getParam('resource_id');
    $resource_type = $this->_getParam('resource_type');
    $follow_id = $this->_getParam('follow_id');
    $status = $this->_getParam('smoothbox', 1);
    $this->view->status = true;

		if ($resource_type == 'sitepage_page') {
			$manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($resource_id, $viewer_id);
		}

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
    $follow_name = $followTable->info('name');

    //GET OBJECT
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
    if (empty($follow_id)) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $follow_id_temp = $resource->follows()->isFollow($viewer);
      if (empty($follow_id_temp)) {

        if (!empty($resource)) {
          $follow_id = $followTable->addFollow($resource, $viewer);
          if($viewer_id != $resource->getOwner()->getIdentity()) {

						if ($resource_type == 'sitepage_page') {
							foreach ($manageAdminsIds as $value) {
							  $action_notification = unserialize($value['action_notification']);
								$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
								//ADD NOTIFICATION
								if (!empty($value['notification']) && in_array('follow', $action_notification)) {
									Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $resource, 'follow_' . $resource_type, array());
								}
							}
						}
						elseif($resource_type == 'sitereview_wishlist') {
							//ADD NOTIFICATION
							Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($resource->getOwner(), $viewer, $resource, 'follow_' . $resource_type, array());
						}

						//ADD ACTIVITY FEED
						$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
						$action = $activityApi->addActivity($viewer, $resource, 'follow_' . $resource_type, '', array(
							'owner' => $resource->getOwner()->getGuid(),
						));

						$activityApi->attachActivity($action, $resource);
					}
        }

        $this->view->follow_id = $follow_id;

        $follow_msg = $this->view->translate('Successfully Followd.');
      }
    } else {
      if (!empty($resource)) {
        $followTable->removeFollow($resource, $viewer);

				if($viewer_id != $resource->getOwner()->getIdentity()) {

					if ($resource_type == 'sitepage_page') {
						Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type = ?' => "sitepage_page", 'object_id = ?' => $resource_id, 'subject_id = ?' => $resource_id, 'subject_type = ?' => "sitepage_page", 'user_id = ?' => $viewer_id));
						foreach ($manageAdminsIds as $value) {
							$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
							//DELETE NOTIFICATION
							$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($user_subject, $resource, 'follow_' . $resource_type);
							if($notification) {
								$notification->delete();
							}
						}
					}
					elseif($resource_type == 'sitereview_wishlist') {
						//DELETE NOTIFICATION
						$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($resource->getOwner(), $resource, 'follow_' . $resource_type);
						if($notification) {
							$notification->delete();
						}
					}

					//DELETE ACTIVITY FEED
					$action_id = Engine_Api::_()->getDbtable('actions', 'activity')
										->select()
										->from('engine4_activity_actions', 'action_id')
										->where('type = ?', "follow_$resource_type")
										->where('subject_id = ?', $viewer_id)
										->where('subject_type = ?', 'user')
										->where('object_type = ?', $resource_type)
										->where('object_id = ?', $resource->getIdentity())    
										->query()
										->fetchColumn();

					if(!empty($action_id)) {
						$activity = Engine_Api::_()->getItem('activity_action', $action_id);
						if(!empty($activity)) {
							$activity->delete();
						}
				  }	
				}
      }
      $follow_msg = $this->view->translate('Successfully Unfollowd.');
    }

    if (empty($status)) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array($follow_msg))
      );
    }
    
    //HERE THE CONTENT TYPE MEANS MODULE NAME
    $follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow( $resource_type , $resource_id);
    
    $followers = $this->view->translate(array('%s follower', '%s followers', $follow_count),$this->view->locale()->toNumber($follow_count));

    $this->view->follow_count = "<a href='javascript:void(0);' onclick='showSmoothBox(); return false;' >".$followers."</a>";
  }
  
  
}
