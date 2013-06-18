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
 
 
 
class Post_Widget_TopVotersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
  

    $params = array(
      'live' => true,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'period' => $this->_getParam('period')
    );
    
    $this->view->voters = Engine_Api::_()->getItemTable('post_vote')->getTopVoters($params);
    
    if (empty($this->view->voters)) {
      return $this->setNoRender();
    }
  }

}