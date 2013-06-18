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
 
class Post_Widget_MainMenuController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_main');

    /*
    
    $pages = array();
    $categories = Engine_Api::_()->getItemTable('post_category')->getTopLevelCategories();
    foreach ($categories as $category) {
      $page = array(
        'uri' => $category->getHref(),
        'label' => $category->getTitle(),
      );
      $pages[] = $page;
    }
    
    if (!empty($pages)) {
      $browse = $navigation->findOneBy('action', 'browse');
      if ($browse) {
        $browse->setPages($pages);
      }
    }
    */
  }

}
