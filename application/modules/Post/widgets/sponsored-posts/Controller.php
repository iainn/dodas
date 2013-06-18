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
 
 
 
class Post_Widget_SponsoredPostsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $params = array(
      'live' => true,
      'search' => 1,
      'sponsored' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'random'),
      'period' => $this->_getParam('period'),
      'user' => $this->_getParam('user'),
      'keyword' => $this->_getParam('keyword'),
      'media' => $this->_getParam('media'),  
    );
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($params);
        
    if ($paginator->getTotalItemCount() == 0) {
      return $this->setNoRender();
    }

    $this->view->widget_name = 'post_sponsoredposts_'.$this->getElement()->getIdentity();
    $this->view->use_slideshow = $paginator->getCurrentItemCount() > 1;
  }

}