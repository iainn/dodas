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
 
 
 
class Post_Widget_FeaturedPostsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
 
    $params = array(
      'live' => true,
      'search' => 1,
      'featured' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'random'),
      'period' => $this->_getParam('period'),
      'media' => $this->_getParam('media'), 
      'category' => $this->_getParam('category'),  
      'user' => $this->_getParam('user'),
      'keyword' => $this->_getParam('keyword'),
    );
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($params);
    
    if ($paginator->getTotalItemCount() == 0) {
      return $this->setNoRender();
    }

    $this->view->widget_name = 'post_featuredposts_'.$this->getElement()->getIdentity();
    $this->view->use_slideshow = $paginator->getCurrentItemCount() > 1;
  }

}