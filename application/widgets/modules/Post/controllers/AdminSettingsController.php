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
 

class Post_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_admin_main', array(), 'post_admin_main_settings');

    $this->view->form = $form = new Post_Form_Admin_Global();

    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'post', 'controller'=>'settings'),'admin_default',true));
    
    $this->view->notice = $this->_getParam('notice');
    
    $timefactor = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.timefactor', 30);
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
    
      $new_timefactor = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.timefactor', 30);
      if ($timefactor != $new_timefactor) {
        Engine_Api::_()->getItemTable('post')->updateHotness();
      }
    
    
      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
    }
  }
  
}