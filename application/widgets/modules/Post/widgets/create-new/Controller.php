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
 
 
 
class Post_Widget_CreateNewController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('post', $viewer, 'create');
    
    if (!$this->view->can_create) {
      return $this->setNoRender();
    }
    
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_quick'); 

    if (empty($quickNavigation)) {
      return $this->setNoRender();
    }
    
    $this->view->submenu = $submenu = $this->_getParam('submenu', 1);
    
    if ($submenu)
    {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('post_create');    
  
      $create = $quickNavigation->findOneBy('action', 'create');
      if ($create) {
        $pages = $navigation->getPages();
        $create->setPages($pages);
      }
    }

  }

}