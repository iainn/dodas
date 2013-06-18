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
class Post_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {

    
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('post_id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('post', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('post');
    
    if (Engine_Api::_()->core()->hasSubject())
    {    
      //$this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $this->_helper->requireAuth()->setAuthParams(
        $subject,
        Engine_Api::_()->user()->getViewer(),
        'view'
      );
    }
  }

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $error = null;
    if (!$subject->isApprovedStatus()) {
      $error = 'This post has not been approved by administrator yet.';
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
    
    if ($error && !$is_admin && !$viewer->isSelf($subject->getOwner())) 
    {
      $this->view->error = $error;
      return $this->_forward('requireauth', 'error', 'core');
    }
    
    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}