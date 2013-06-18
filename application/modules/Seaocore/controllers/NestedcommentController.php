<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CommentController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_NestedcommentController extends Core_Controller_Action_Standard {

  public function init() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    if ($type && $identity) {
      $item = Engine_Api::_()->getItem($type, $identity);
      if ($item instanceof Core_Model_Item_Abstract &&
              (method_exists($item, 'comments') || method_exists($item, 'likes'))) {
        if (!Engine_Api::_()->core()->hasSubject()) {
          Engine_Api::_()->core()->setSubject($item);
        }
      }
    }
  }

  public function listAction() {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->subject = $subject = $this->getSubjectItem();
    $subjectParent = $subject;
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }
    $baseOnContentOwner = Engine_Api::_()->seaocore()->baseOnContentOwner($viewer, $subjectParent);

    // Perms
    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
    $autorizationApi = Engine_Api::_()->authorization();
    if (strpos($subject->getType(), "sitepage") !== false) {
      if ($subject->getType() == 'sitepage_page') {
        $pageSubject = $subject;
      } elseif ($subject->getType() == 'sitepagemusic_playlist') {
        $pageSubject = $subject->getParentType();
      } elseif ($subject->getType() == 'sitepagenote_photo') {
        $pageSubject = $subject->getParent()->getParent()->getParent();
      } else {
        $pageSubject = $subject->getParent();
      }
      $pageApi = Engine_Api::_()->sitepage();

      $this->view->canComment = $canComment = $pageApi->isManageAdmin($pageSubject, 'comment');
      $this->view->canDelete = $pageApi->isManageAdmin($pageSubject, 'edit');
    } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
      if ($subject->getType() == 'sitebusiness_business') {
        $businessSubject = $subject;
      } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
        $businessSubject = $subject->getParentType();
      } elseif ($subject->getType() == 'sitebusinessnote_photo') {
        $businessSubject = $subject->getParent()->getParent()->getParent();
      } else {
        $businessSubject = $subject->getParent();
      }
      $businessApi = Engine_Api::_()->sitebusiness();

      $this->view->canComment = $canComment = $businessApi->isManageAdmin($businessSubject, 'comment');
      $this->view->canDelete = $businessApi->isManageAdmin($businessSubject, 'edit');
    } elseif ($subject->getType() == 'sitereview_review') {
      $listingtype_id = $subject->getParent()->listingtype_id;
      $this->view->canComment = $canComment =  $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
      $this->view->canDelete = $autorizationApi->getPermission($level_id, 'sitereview_listing', "edit_listtype_$listingtype_id");
    } elseif($subject->getType() == 'sitereview_listing') {
			$listingtype_id = $subject->listingtype_id;
      $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
      if(!empty($canComment))
        $canComment = $subject->authorization()->isAllowed($viewer,  "comment_listtype_$listingtype_id");
			$this->view->canComment = $canComment ; 
			$this->view->canDelete = $subject->authorization()->isAllowed($viewer,  "edit_listtype_$listingtype_id");
    } 
    
    // Likes
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();
		$this->view->parent_comment_id= $parent_comment_id = $this->_getParam('parent_comment_id', 0);

    $this->view->parent_div= $parent_div = $this->_getParam('parent_div', 0);
    $this->view->format = $this->_getParam('format');
    $this->view->order = $order = $this->_getParam('order', 'DESC');
    if(empty($parent_comment_id)) {
       $commentCountSelect = $subject->comments()->getCommentSelect($order);
       $this->view->commentsCount =  $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
    }

    // Comments
    // If has a page, display oldest to newest
    if (0 !== ( $page = $this->_getParam('page',0))) {
      $commentSelect = $subject->comments()->getCommentSelect($order);
      $commentSelect->where('parent_comment_id =?', $parent_comment_id);
      $commentSelect->order("comment_id $order");
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }
    // If not has a page, show the
    else {
      $commentSelect = $subject->comments()->getCommentSelect($order);
      $commentSelect->where('parent_comment_id =?', $parent_comment_id);
      $commentSelect->order("comment_id $order");
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    $this->view->nested_comment_id = $subject->getGuid() . "_" . $parent_comment_id; 
   
    if ($viewer->getIdentity() && $canComment) {
      $this->view->formComment = $form = new Seaocore_Form_Comment_Create();

      if ($parent_comment_id) {
        $form->getElement('submit')->setLabel('Post Reply');
      }

      $form->populate(array(
          'identity' => $subject->getIdentity(),
          'type' => $subject->getType(),
          'format' => 'html',
          'parent_comment_id' => $parent_comment_id
      ));
    }
  }

  public function createAction() {

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $subjectParent = $subject;

    $viewer_id = $viewer->getIdentity();
    $listingtypeName ="";
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }
    $autorizationApi = Engine_Api::_()->authorization();
    if (strpos($subject->getType(), "sitepage") !== false) {
      if ($subject->getType() == 'sitepage_page') {
        $subjectParent = $subject;
      } elseif ($subject->getType() == 'sitepagemusic_playlist') {
        $subjectParent = $subject->getParentType();
      } elseif ($subject->getType() == 'sitepagenote_photo') {
        $subjectParent = $subject->getParent()->getParent()->getParent();
      } else {
        $subjectParent = $subject->getParent();
      }
      $pageApi = Engine_Api::_()->sitepage();
      $canComment = $pageApi->isManageAdmin($subjectParent, 'comment');
      if (empty($canComment)) {
        $this->view->status = false;
        return;
      }
    } elseif (strpos($subject->getType(), "sitebusiness") !== false) {
      if ($subject->getType() == 'sitebusiness_business') {
        $subjectParent = $subject;
      } elseif ($subject->getType() == 'sitebusinessnote_photo') {
        $subjectParent = $subject->getParent()->getParent()->getParent();
      } elseif ($subject->getType() == 'sitebusinessmusic_playlist') {
        $subjectParent = $subject->getParentType();
      } else {
        $subjectParent = $subject->getParent();
      }
      $businessApi = Engine_Api::_()->sitebusiness();
      $canComment = $businessApi->isManageAdmin($subjectParent, 'comment');
      if (empty($canComment)) {
        $this->view->status = false;
        return;
      }
    } elseif ($subject->getType() == 'sitereview_review') {
      $listingtype_id = $subject->getParent()->listingtype_id;
      $canComment =  $autorizationApi->getPermission($level_id, 'sitereview_listing', "review_reply_listtype_$listingtype_id");
      if (empty($canComment)) {
         $this->view->status = false;
         return;
      }
    } elseif($subject->getType() == 'sitereview_listing') {
			$listingtype_id = $subject->listingtype_id;
      $listingtypeName = strtolower(Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingtype_id, 'title_singular'));
      $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
      if(!empty($canComment))
        $canComment = $subject->authorization()->isAllowed($viewer,  "comment_listtype_$listingtype_id");
			if (empty($canComment)) {
         $this->view->status = false;
         return;
      }
    } elseif (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $this->view->form = $form = new Seaocore_Form_Comment_Create();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
      return;
    }

    // Process
    // Filter HTML
    $filter = new Zend_Filter();
    $filter->addFilter(new Engine_Filter_Censor());
    $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

    $body = $form->getValue('body');
    $body = $filter->filter($body);


    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try {

      //For comment work owner or second user
      $baseOnContentOwner = Engine_Api::_()->seaocore()->baseOnContentOwner($viewer, $subjectParent);
      if ($baseOnContentOwner) {
        $comment = $subject->comments()->addComment($subjectParent, $body);
      } else {
        $comment = $subject->comments()->addComment($viewer, $body);
      }
      $comment->parent_comment_id = $form->getValue('parent_comment_id');
      $comment->save();
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $subjectOwner = $subject->getOwner('user');

      // Activity
      if(empty($comment->parent_comment_id)) {
				$action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
						'owner' => $subjectOwner->getGuid(),
						'body' => $body,
            'listingtype' => $listingtypeName
								));
      } else {
        $action = $activityApi->addActivity($viewer, $subject, 'nestedcomment_' . $subject->getType(), '', array(
						'owner' => $subjectOwner->getGuid(),
						'body' => $body,
'listingtype' => $listingtypeName
								));
      }

//       if (!empty($action)) {
//         $activityApi->attachActivity($action, $subject);
//       }

      // Notifications
      // Add notification for owner (if user and not viewer)
      $this->view->subject = $subject->getGuid();
      $this->view->owner = $subjectOwner->getGuid();
      if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
        //start check for page admin and page owner
        if ($baseOnContentOwner) {
          $notifyApi->addNotification($subjectOwner, $subjectParent, $subject, 'commented', array(
              'label' => $subject->getShortType()
          ));
        } else {
          $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
              'label' => $subject->getShortType()
          ));
        }
        //end check for page admin and page owner
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      $commentedUserNotifications = array();
      foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        $commentedUserNotifications[] = $notifyUser->getIdentity();

        //start check for page admin and page owner
        if ($baseOnContentOwner) {
          $notifyApi->addNotification($notifyUser, $subjectParent, $subject, 'commented_commented', array(
              'label' => $subject->getShortType()
          ));
        } else {
          $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
              'label' => $subject->getShortType()
          ));
        }
        //end check for page admin and page owner
      }

      // Add a notification for all users that liked
      // @todo we should probably limit this
      foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
        // Skip viewer and owner
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
          continue;

        //start check for page admin and page owner
        if ($baseOnContentOwner) {
          $notifyApi->addNotification($notifyUser, $subjectParent, $subject, 'liked_commented', array(
              'label' => $subject->getShortType()
          ));
        } else {
          $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
              'label' => $subject->getShortType()
          ));
        }
        //end check for page admin and page owner
      }

      // Increment comment count
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $commentCountSelect = $subject->comments()->getCommentSelect('DESC');
    $this->view->commentsCount =  $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
    $this->view->status = true;
    $this->view->message = 'Comment added';
    $this->view->body = $this->view->action('list', 'nestedcomment', 'seaocore', array(
        'type' => $this->_getParam('type'),
        'id' => $this->_getParam('id'),
        'format' => 'html',
        'page' => 0,
        'parent_div'=> 1,
        'parent_comment_id' => $comment->parent_comment_id 
        ));
    $this->_helper->contextSwitch->initContext();
  }

  public function deleteAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $autorizationApi = Engine_Api::_()->authorization();
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $subject = $this->getSubjectItem();
    // Comment id
    $comment_id = $this->_getParam('comment_id');
    if (!$comment_id) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
      return;
    }

    // Comment
    $comment = $subject->comments()->getComment($comment_id);
    if (!$comment) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
      return;
    }

    // Authorization
    if ($comment->poster_type == "sitepage_page") {
      $page = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
      $this->view->canDelete = $canDelete = Engine_Api::_()->sitepage()->isManageAdmin($page, 'edit');
      if (!$page->isOwner($viewer) && empty($canDelete)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
        return;
      }
    } elseif ($comment->poster_type == "sitebusiness_business") {
      $business = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
      $this->view->canDelete = $canDelete = Engine_Api::_()->sitebusiness()->isManageAdmin($business, 'edit');
      if (!$business->isOwner($viewer) && empty($canDelete)) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
        return;
      }
    } elseif ($comment->poster_type == 'sitereview_review') {
      $subject = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
      $listingtype_id = $subject->getParent()->listingtype_id;
     $this->view->canDelete = $canDelete = $autorizationApi->getPermission($level_id, 'sitereview_listing', "edit_listtype_$listingtype_id");
      if (empty($canDelete)) {
         $this->view->status = false;
				 $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
         return;
      }
    } elseif($comment->poster_type == 'sitereview_listing') {
			$subject = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
      $listingtype_id = $subject->listingtype_id;
     $this->view->canDelete = $canDelete = $subject->authorization()->isAllowed($viewer,  "edit_listtype_$listingtype_id");
      if (empty($canDelete)) {
         $this->view->status = false;
				 $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
         return;
      }
    } elseif (!$subject->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
            $comment->poster_id != $viewer->getIdentity())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
      return;
    }

    // Method
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // Process
    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try {
      $subject->comments()->removeComment($comment_id);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $commentCountSelect = $subject->comments()->getCommentSelect('DESC');
      
    $this->view->commentsCount =  $commentsCount = Zend_Paginator::factory($commentCountSelect)->getTotalItemCount();
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
  }

  public function likeAction() {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $comment_id = $this->_getParam('comment_id');
    $parent_comment_id =  $this->_getParam('parent_comment_id');
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {
      if(!$commentedItem->likes()->isLike($viewer))  {
      $commentedItem->likes()->addLike($viewer);

      // Add notification
      $owner = $commentedItem->getOwner();
      $this->view->owner = $owner->getGuid();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
            'label' => $commentedItem->getShortType()
        ));
      }

      $db->commit();
}
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if ($subject->getType() == 'core_comment') {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
    $this->view->body = $this->view->action('list', 'nestedcomment', 'seaocore', array(
        'type' => $type,
        'id' => $id,
        'format' => 'html',
        'parent_comment_id' => $parent_comment_id,
        'page' => 0,
        'parent_div'=> 1
            ));
    $this->_helper->contextSwitch->initContext();
  }

  public function unlikeAction() {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $comment_id = $this->_getParam('comment_id');
     $parent_comment_id =  $this->_getParam('parent_comment_id');
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {
      $commentedItem->likes()->removeLike($viewer);
      
      //LIKE NOTIFICATION DELETE
			if (empty($comment_id) && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitelike')) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?'  => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $subject->getType(), 'object_id = ?' => $subject->getIdentity()));
			}
			//LIKE NOTIFICATION DELETE
			
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if ($subject->getType() == 'core_comment') {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
    $this->view->body = $this->view->action('list', 'nestedcomment', 'seaocore', array(
        'type' => $type,
        'id' => $id,
        'format' => 'html',
				'parent_comment_id' => $parent_comment_id,
        'page' => 0,
        'parent_div'=> 1
            ));
    $this->_helper->contextSwitch->initContext();
  }

  public function getLikesAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = $this->getSubjectItem();
    $likes = $subject->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
        count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }

  public function getSubjectItem() {
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');

    if ($type && $identity)
      return $subject = Engine_Api::_()->getItem($type, $identity);
  }


}