<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: EventController.php 9747 2012-07-26 02:08:08Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mobi_EventController extends Core_Controller_Action_Standard
{
  public function profileAction()
  {
      
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('event', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }
    
    $this->_helper->requireSubject('event');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams($subject, Engine_Api::_()->user()->getViewer(), 'view');

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    // Check block
    if( $viewer->isBlockedBy($subject) )
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}