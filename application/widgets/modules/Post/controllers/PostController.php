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

class Post_PostController extends Core_Controller_Action_Standard
{

  public function init()
  {    
    if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
        null !== ($post = Engine_Api::_()->getItem('post', $post_id)) ) {
      Engine_Api::_()->core()->setSubject($post);
    }

    //$this->_helper->requireUser();
    
    $this->_helper->requireUser->addActionRequires(array(
      'delete',
      'edit',
      'vote',
      'success'
    ));
    
    $this->_helper->requireSubject('post');

  }


  public function successAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->post = $post = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }    
    
    $this->_initContentPage();
  }  
  
  public function _initContentPage()
  {
    // Render
    $this->_helper->content
        ->setContentName('post_post_edit')
        ->setEnabled()
        ; 
  }
  
  // tested
  public function editAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }

    $this->_initContentPage();
     
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->post = $post = Engine_Api::_()->core()->getSubject();

    $media = $post->media;
    $form_class = 'Post_Form_Post_Edit_'.ucfirst($media);
    
    
    $this->view->form = $form = new $form_class(array(
      'item' => $post
    ));

    $form->populate($post->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
      'view' => 'everyone',
      'comment' => 'registered',
    );
    
    // Save post entry
    if( !$this->getRequest()->isPost() )
    {     
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;
        
        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($post, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }
      
      return;
    }
        
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      //$form->setHeaderFields();
      return;
    }
    //$form->setHeaderFields();


    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['keywords']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {

      $post->setFromArray($values);
      $post->modified_date = date('Y-m-d H:i:s');

      $post->tags()->setTagMaps($viewer, $tags);
      $post->save();

      // Set photo
      if( !empty($values['photo']) ) {
        $post->thumb = '';
        $post->setPhoto($form->photo);
      }      

      // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($post);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();
      
      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
          
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($post, $role, $auth_key, ($i <= $authMax));
        }
      }
      
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($post) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    if( $this->_getParam('ref') === 'profile' ) {
      $this->_redirectCustom($post);
    } else {
      //$this->_redirectCustom(array('route' => 'post_general', 'action' => 'manage'));
    }
  }


  
  // tested
  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('post');

    if( !$this->_helper->requireAuth()->setAuthParams($post, null, 'delete')->isValid()) {
      return;
    }

    $this->_initContentPage();
    
    $this->view->form = $form = new Post_Form_Post_Delete();
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }
          
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $post->delete();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    } 
    
    return $this->_redirectCustom(array('route' => 'post_general', 'action'=>'manage'));
    
  }


  public function permalinkAction()
  {
    
    $this->view->post = $post = Engine_Api::_()->core()->getSubject();
    
    
    $serverUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
    
    $this->view->permalink = $serverUrl . $post->getHref();
  }
  
  public function voteAction()
  {
    $post = Engine_Api::_()->core()->getSubject('post');
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->helpful = $helpful = (int) $this->_getParam('helpful', 1);
    
    $vote = $post->votes()->getVote($viewer);
    if ($vote)
    {
      if ($helpful == $vote->helpful) {
        $this->view->same = 1;
      }
      else {
        $this->view->same = 0;
        $vote->helpful = $helpful;
        $vote->save();
        
        $post->votes()->updatePreferenceCountKeys();
      }      
      $this->view->new = 0;
    }
    else {
      $post->votes()->addVote($viewer, $helpful);
      $this->view->new = 1;
    }
    
    $this->view->post = $post;
    
    $this->view->success = true;
  }


}