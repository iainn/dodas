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
class Post_Widget_ProfileNoticeController extends Engine_Content_Widget_Abstract
{
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
    
      // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
    try
    {
    	$is_admin = $viewer->isAdmin();
    } 
    catch (Exception $ex)
    {
      $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');	
    }    
    
    if (!$is_admin && !$subject->getOwner()->isSelf($viewer))
    {
      return $this->setNoRender();
    }
    
    if ($subject->isApprovedStatus())
    {
      return $this->setNoRender();
    }
    
    
  }
}