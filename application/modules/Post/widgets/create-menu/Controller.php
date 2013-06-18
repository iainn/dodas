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
 
class Post_Widget_CreateMenuController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $router = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
    
    // we don't want to show up on the post create landing page
    if ($router == 'post_general' && $params['action'] == 'create' && empty($params['media'])) {
      return $this->setNoRender();
    }
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_create');

      
      
  }

}
