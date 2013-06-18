<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_IndexController extends Core_Controller_Action_Standard
{
  // Testing SE custom built-in page .. not work yet :-(
  public function __call($methodName, $args)
  {
    $method404 = 'Method "%s" does not exist and was not trapped in __call()';
    $action404 = 'Action "%s" does not exist and was not trapped in __call()';
    
    // Not an action
    if( 'Action' != substr($methodName, -6) ) {
      throw new Zend_Controller_Action_Exception(sprintf($not_found_action, $methodName), 500);
    }

    // Get page
    $action = substr($methodName, 0, strlen($methodName) - 6);
    $method_name = $this->_getParam('method');
    $methodNameNormal = substr($method404, -6, 4);
    $params = $this->_getAllParams();
    // Have to un inflect
    if( is_string($action) ) {
      $module = $this->getRequest()->getModuleName();
      $controller = Engine_Api::_()->$module()->getRest($action);
      $actionNormal = strtolower(preg_replace('/([A-Z])/', '-\1', $action));
      // @todo This may be temporary
      $actionNormal = str_replace('-', '_', $actionNormal);
      $methodNameNormal .= substr($method404, 0, 6);
    }

    // Get page object
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageSelect = $pageTable->select();

    if( is_numeric($actionNormal) || !$method_name) {
      $pageSelect->where('page_id = ?', $actionNormal);
    } else {
      $pageSelect
        ->orWhere('name = ?', str_replace('-', '_', $actionNormal))
        ->orWhere('url = ?', str_replace('_', '-', $actionNormal));
      call_user_func(array($controller, $methodNameNormal), $method_name, $params);  
    }

    $pageObject = $pageTable->fetchRow($pageSelect);

    // Page found
    if( null !== $pageObject ) {
      // Check if the viewer can view this page
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $pageObject->custom && !$pageObject->allowedToView($viewer) ) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      // Render the page
      $this->_helper->content
        ->setContentName($pageObject->page_id)
        ->setNoRender()
        ->setEnabled();
      return;
    }


    // Missing page
    throw new Zend_Controller_Action_Exception(sprintf($action404, $action), 404);
  }  
  
  public function updatesAction()
  {

  }

  public function licenseAction()
  {
    $type = $this->_getParam('type');
    $this->view->module = $module = Engine_Api::_()->getDbtable('modules', 'core')->getModule($type);
    if (!$module) {
      return $this->_forward('notfound', 'error', 'core');
    }
  }
}