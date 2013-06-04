<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9989 2013-03-20 01:13:58Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Sami
 */
class Event_IndexController extends Core_Controller_Action_Standard
{
  public function init() 
  {
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid() ) return;

    $id = $this->_getParam('event_id', $this->_getParam('id', null));
    if( $id ) {
      $event = Engine_Api::_()->getItem('event', $id);
      if( $event ) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }
  }

  public function browseAction()
  {
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
    
    
    $filter = $this->_getParam('filter', 'future');
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Create form
    $this->view->formFilter = $formFilter = new Event_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if( !$viewer || !$viewer->getIdentity() ) {
      $formFilter->removeElement('view');
    }

    // Populate options
    foreach( Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row ) {    
      $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
    }
    if (count($formFilter->category_id->getMultiOptions()) <= 1) {
      $formFilter->removeElement('category_id');
    }

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $viewer->getIdentity() && @$values['view'] == 1 ) {
      $values['users'] = array();
      foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    if( $filter == "past" ) {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

     // check to see if request is for specific user's listings
    if( ($user_id = $this->_getParam('user')) ) {
      $values['user_id'] = $user_id;
    }
    
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('event')
            ->getEventPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }

  public function manageAction()
  {
    // Create form
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('event_main');

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;

    $this->view->formFilter = $formFilter = new Event_Form_Filter_Manage();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('events', 'event');
    $tableName = $table->info('name');
    
    // Only mine
    if( @$values['view'] == 2 ) {
      $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity());
    }
    // All membership
    else {
      $membership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $membership->getMembershipsOfSelect($viewer);
    }
    
    if( !empty($values['search_text']) ) {
      $values['text'] = $values['search_text'];
    }
    if( !empty($values['text']) ) {
      $select->where("`{$tableName}`.title LIKE ?", '%'.$values['text'].'%');
    }

    $select->order('starttime ASC');
    //$select->where("endtime > FROM_UNIXTIME(?)", time());

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->text = $values['text'];
    $this->view->view = $values['view'];
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid() ) return;

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $parent_type = $this->_getParam('parent_type');
    $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));

    if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') ) {
      $this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
      if( !$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid() ) {
        return;
      }
    } else {
      $parent_type = 'user';
      $parent_id = $viewer->getIdentity();
    }

    // Create form
    $this->view->parent_type = $parent_type;
    $this->view->form = $form = new Event_Form_Create(array(
      'parent_type' => $parent_type,
      'parent_id' => $parent_id
    ));

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach( $categories as $k => $v ) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }

    
    // Not post/invalid
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $values = $form->getValues();
    
    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;
    $values['parent_id'] =  $parent_id;
    if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host']) ) {
      $values['host'] = $group->getTitle();
    }
    
    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
    $db->beginTransaction();
    
    try
    {
      // Create event
      $table = Engine_Api::_()->getDbtable('events', 'event');
      $event = $table->createRow();

      $event->setFromArray($values);
      $event->save();

      // Add owner as member
      $event->membership()->addMember($viewer)
        ->setUserApproved($viewer)
        ->setResourceApproved($viewer);

      // Add owner rsvp
      $event->membership()
        ->getMemberInfo($viewer)
        ->setFromArray(array('rsvp' => 2))
        ->save();
      
      // Add photo
      if( !empty($values['photo']) ) {
        $event->setPhoto($form->photo);
      }    

      // Set auth
      $auth = Engine_Api::_()->authorization()->context;
      
      if( $values['parent_type'] == 'group' ) {
        $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($event, $role, 'view',    ($i <= $viewMax));
        $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($event, $role, 'photo',   ($i <= $photoMax));
      }

      $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

      // Add an entry for member_requested
      $auth->setAllowed($event, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      $action = $activityApi->addActivity($viewer, $event, 'event_create');
      
      if( $action ) {
        $activityApi->attachActivity($action, $event);
      }
      // Commit
      $db->commit();

      // Redirect
      return $this->_helper->redirector->gotoRoute(array('id' => $event->getIdentity()), 'event_profile', true);
    }

    catch( Engine_Image_Exception $e )
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }
}
