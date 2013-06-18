<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Post
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Post_Widget_MemberProfilePostsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $user_type = $this->_getParam('user_type', 'owner');
    
    if ($user_type == 'viewer') {
      if (!$viewer->getIdentity()) {
        return $this->setNoRender();
      }
      else {
        $user = $viewer;
      }     
    }
    else {
      // owner mode
      if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
      }
      
      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();
      
      if (!($subject instanceof Core_Model_Item_Abstract)) {
        return $this->setNoRender();
      }
      
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }       
      
      if( !($subject instanceof User_Model_User) ) {
        $user = $subject->getOwner('user');
      }
      else {
        $user = $subject;
      }
    }
    
    if( !($user instanceof User_Model_User) || !$user->getIdentity()) {
      return $this->setNoRender();
    }
    
    $params = array(
      'live' => true,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('keyword'),
    );
    
    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }
    
    if ($this->_getParam('sponsored', 0)) {
      $params['sponsored'] = 1;
    }
    
    $params['user'] = $user;
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($params);

    $this->view->display_style = $this->_getParam('display_style', 'wide');
    
    $this->view->show_description = $this->_getParam('show_description', 1) == "0" ? false : true;
    $this->view->show_example = $this->_getParam('show_example', 1) == "0" ? false : true;
    $this->view->show_details = $this->_getParam('show_details', 0) == "0" ? false : true;
    $this->view->show_keywords = $this->_getParam('show_keywords', 1) == "0" ? false : true;
    $this->view->show_meta = $this->_getParam('show_meta', 1) == "0" ? false : true;
    $this->view->show_photo = $this->_getParam('show_photo', 1) == "0" ? false : true;
    
    $this->view->showmemberpostslink = $this->_getParam('showmemberpostslink', $this->view->display_style == 'narrow' ? 0 : 1); 
    
    $this->view->order = $params['order'];
    
    $this->view->user = $user;
    
    // Add count to title if configured
    $this->_childCount = $paginator->getTotalItemCount();
    
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}