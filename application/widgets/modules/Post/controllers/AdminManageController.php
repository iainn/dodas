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
 
 
class Post_AdminManageController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    if (!Engine_Api::_()->post()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'post', 'controller'=>'settings', 'notice' => 'license'));
    }     
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
          null !== ($post = Engine_Api::_()->getItem('post', $post_id)) )
      {
        Engine_Api::_()->core()->setSubject($post);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'featured' => 'post',
      'sponsored' => 'post',
      'delete' => 'post',
    ));
  }
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_admin_main', array(), 'post_admin_main_manage');

      
    $this->view->formFilter = $formFilter = new Post_Form_Admin_Manage_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->getItemTable('post')->getPostPaginator($values);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;
  }
  
  

  
  public function featuredAction()
  {
    // In smoothbox
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('post');
    
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {       
        $post->featured = $this->_getParam('featured') == 'yes' ? 1 : 0;
        $post->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  public function sponsoredAction()
  {
    // In smoothbox
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('post');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $post->sponsored = $this->_getParam('sponsored') == 'yes' ? 1 : 0;
        $post->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  
  public function deleteAction()
  {
    // In smoothbox
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('post');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $post->delete();
        $db->commit();
        
        Engine_Api::_()->core()->clearSubject();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
  }

  public function deleteselectedAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('post_admin_main', array(), 'post_admin_main_manage');
          
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $post = Engine_Api::_()->getItem('post', $id);
        if( $post ) $post->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }  
  
  
  public function updateStatusAction()
  {
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('post');
    
    $this->view->form = $form = new Post_Form_Admin_Post_Status();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      
      $values = array(
        'status' => $post->status,
      );
      
      $form->populate($values);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }    
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    { 
      $current_post_status = $post->status;
      $values = $form->getValues();
      
      $post->updateStatus($values['status']);
      $post->save();

      if ($post->isApprovedStatus())
      {
        $post->updateHotness();
        Engine_Api::_()->post()->pushNewPostActivity($post);
      }
      
      if ($current_post_status != $post->status)
      {
        Engine_Api::_()->post()->pushStatusUpdateNotification($post);
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
    ));

  }  
}