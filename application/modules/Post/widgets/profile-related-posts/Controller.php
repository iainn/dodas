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
class Post_Widget_ProfileRelatedPostsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  	
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->post = $subject = Engine_Api::_()->core()->getSubject('post');
    
    if( !($subject instanceof Post_Model_Post) ) {
      return $this->setNoRender();
    }    
        
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
 
    $params = array(
      'search' => 1,
      'limit' => $this->_getParam('max', 10),
      'order' => $this->_getParam('order', 'random'),
      'live' => true,
    );    
    
    $this->view->paginator = $paginator = $subject->getRelatedPosts($params);
    
    if (empty($paginator)) {
    	return $this->setNoRender();
    }
    
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }     
    
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }   
}