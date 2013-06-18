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

class Post_Plugin_Menus
{

  public function onMenuInitialize_PostQuickCreate($row)
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create posts
    if( !Engine_Api::_()->authorization()->isAllowed('post', $viewer, 'create') ) {
      return false;
    }

   // print_r($row);
    
    $request = Zend_Controller_Front::getInstance()->getRequest();    
    if (strlen($term = trim($request->getParam('term')))) {
      // Modify params
      
      $row->label = 'Add Your Definition';
      $params = $row->params;
      $params['params']['term'] = $term;
      return $params;
    }
    

    
    return true;
  }
  
  public function canCreatePosts()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create posts
    if( !Engine_Api::_()->authorization()->isAllowed('post', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewPosts()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view posts
    if( !Engine_Api::_()->authorization()->isAllowed('post', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  public function canSavePosts()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }    
    
    // Must be able to view posts
    if( !Engine_Api::_()->authorization()->isAllowed('post', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  

  public function onMenuInitialize_PostMainHot($row)
  {
    return $this->canViewPosts();
  }

  public function onMenuInitialize_PostMainNew($row)
  {
    return $this->canViewPosts();
  }

  public function onMenuInitialize_PostMainBrowse($row)
  {
    return $this->canViewPosts();
  }

  public function onMenuInitialize_PostMainTop($row)
  {
    return $this->canViewPosts();
  }  
  
  public function onMenuInitialize_PostMainManage($row)
  {
    return $this->canCreatePosts();
  }  
  
  public function onMenuInitialize_PostMainFavorites($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    
    return true;
  } 
  
  public function onMenuInitialize_PostMainCreate($row)
  {
    return $this->canCreatePosts();
  }  
  
  
  
  public function onMenuInitialize_PostCreateTopic($row)
  {
    return $this->canCreatePosts();
  }    
  
  public function onMenuInitialize_PostCreateLink($row)
  {
    return $this->canCreatePosts();
  }
  
  public function onMenuInitialize_PostCreatePhoto($row)
  {
    return $this->canCreatePosts();
  }

  public function onMenuInitialize_PostCreateVideo($row)
  {
    return $this->canCreatePosts();
  }

  
  public function onMenuInitialize_PostDashboardView($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $post = Engine_Api::_()->core()->getSubject('post');

    if( !($post instanceof Post_Model_Post) ) {
      return false;
    }
    
    if( !$post->authorization()->isAllowed($viewer, 'view') ) {
      return false;
    }    
    
    // Modify params
    $params = $row->params;
    $params['params']['post_id'] = $post->getIdentity();
    $params['params']['slug'] = $post->getSlug();
    return $params;
  }  
  
  public function onMenuInitialize_PostDashboardEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $post = Engine_Api::_()->core()->getSubject('post');

    if( !($post instanceof Post_Model_Post) ) {
      return false;
    }
    
    if( !$post->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }    
    
    // Modify params
    $params = $row->params;
    $params['params']['post_id'] = $post->getIdentity();
    return $params;
  }


  public function onMenuInitialize_PostDashboardDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $post = Engine_Api::_()->core()->getSubject('post');

    if( !($post instanceof Post_Model_Post) ) {
      return false;
    }
    
    if( !$post->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }    
    
    // Modify params
    $params = $row->params;
    $params['params']['post_id'] = $post->getIdentity();
    return $params;
  }  
  

  
  
}