<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Widget_FeedController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if (!in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitebusiness_business', 'sitebusinessevent_event'))) {
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event'))) {
        $pageSubject = $subject;
        if ($subject->getType() == 'sitepageevent_event')
          $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitebusiness_business', 'sitebusinessevent_event'))) {
        $businessSubject = $subject;
        if ($subject->getType() == 'sitebusinessevent_event')
          $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      }
    }

    $listLimit = 0;
    $composerLimit = 1;
    $request = Zend_Controller_Front::getInstance()->getRequest();

    // Get some options
    $this->view->homefeed = $homefeed = $request->getParam('homefeed', false);
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);
    $getComposerValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.composer.value', $listLimit);

    $this->view->curr_url = $curr_url = $request->getRequestUri(); // Return the current URL.
    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $getListViewValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.list.view.value', $composerLimit);
    $getPublishValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.publish.str.value', $composerLimit);
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int) $request->getParam('action_id');
    $this->view->post_failed = (int) $request->getParam('pf');

    if ($feedOnly || $homefeed) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if ($length > 50) {
      $this->view->length = $length = 50;
    }

    // Get all activity feed types for custom view?
    $actionTypeFilters = array();
    $listTypeFilter = array();

    $this->view->isFromTab = $request->getParam('isFromTab', false);
    $this->view->actionFilter = $actionTypeGroup = $request->getParam('actionFilter', 'all');
    if ($actionTypeGroup && !in_array($actionTypeGroup, array('membership', 'owner', 'all', 'network_list', 'member_list', 'custom_list'))) {
      $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'advancedactivity');
      $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
      if (isset($groupedActionTypes[$actionTypeGroup])) {
        $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
        if ($actionTypeGroup == 'sitepage') {
          $actionTypeGroupSubModules = Engine_Api::_()->advancedactivity()->getSubModules($actionTypeGroup);
          foreach ($actionTypeGroupSubModules as $actionTypeGroupSubModule) {
            if (isset($groupedActionTypes[$actionTypeGroupSubModule])) {
              $actionTypeFilters = array_merge($actionTypeFilters, $groupedActionTypes[$actionTypeGroupSubModule]);
            }
          }
        } else if ($actionTypeGroup == 'sitebusiness') {
          $actionTypeGroupSubModules = Engine_Api::_()->advancedactivity()->getSubModules($actionTypeGroup);
          foreach ($actionTypeGroupSubModules as $actionTypeGroupSubModule) {
            if (isset($groupedActionTypes[$actionTypeGroupSubModule])) {
              $actionTypeFilters = array_merge($actionTypeFilters, $groupedActionTypes[$actionTypeGroupSubModule]);
            }
          }
        }
      }
    } else if (in_array($actionTypeGroup, array('member_list', 'custom_list')) && ($list_id = $this->_getParam('list_id')) != null) {
      $listTypeFilter = Engine_Api::_()->advancedactivity()->getListBaseContent($actionTypeGroup, array('list_id' => $list_id));
    } else if ($actionTypeGroup == 'network_list' && ($list_id = $this->_getParam('list_id') != null)) {
      $this->view->list_id = $list_id = $this->_getParam('list_id');
      $listTypeFilter = array($list_id);
    }
    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => (int) $request->getParam('maxid'),
        'min_id' => (int) $request->getParam('minid'),
        'limit' => (int) $length,
        'showTypes' => $actionTypeFilters,
        'membership' => $actionTypeGroup == 'membership' ? true : false,
        'listTypeFilter' => $listTypeFilter,
        'actionTypeGroup' => $actionTypeGroup
    );


    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    // $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $hideItems = array();
    if (empty($subject)) {
      if ($viewer->getIdentity())
        $hideItems = Engine_Api::_()->getDbtable('hide', 'advancedactivity')->getHideItemByMember($viewer);
    }

    $grouped_actions = array();

    do {
      // Get current batch
      $actions = null;
      if (!empty($subject)) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;

      // Are we at the end?
      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      // Pre-process
      if (count($actions) > 0) {
        foreach ($actions as $action) {
          // get next id
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }

          // skip disabled actions
          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
            continue;
          // skip items with missing items
          if (!$action->getSubject() || !$action->getSubject()->getIdentity())
            continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity())
            continue;

          // skip the hide actions and content        
          if (!empty($hideItems)) {
            if (isset($hideItems[$action->getType()]) && in_array($action->getIdentity(), $hideItems[$action->getType()])) {
              continue;
            }
            if (!$action->getTypeInfo()->is_object_thumb && isset($hideItems[$action->getSubject()->getType()]) && in_array($action->getSubject()->getIdentity(), $hideItems[$action->getSubject()->getType()])) {
              continue;
            }
            if (($action->getTypeInfo()->is_object_thumb || $action->getObject()->getType() == 'user' ) && isset($hideItems[$action->getObject()->getType()]) && in_array($action->getObject()->getIdentity(), $hideItems[$action->getObject()->getType()])) {
              continue;
            }
          }

          // track/remove users who do too much (but only in the main feed)
          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          // remove duplicate friend requests
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          /* Start Working group feed. */
          if (!empty($action->getTypeInfo()->is_grouped) && isset($action->getTypeInfo()->is_grouped)) {
            if ($action->type == 'friends') {
              $object_guid = $action->getSubject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getObject();
            } elseif ($action->type == 'tagged') {
              foreach ($action->getAttachments() as $attachment) {
                $object_guid = $attachment->item->getGuid();
                $Subject_guid = $action->getSubject()->getGuid();
                $total_guid = $action->type . '_' . $object_guid . '_' . $Subject_guid;
              }
              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][$action->getObject()->getGuid()] = $action->getObject();
            } else {
              $object_guid = $action->getObject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getSubject();
            }

            if (count($grouped_actions[$total_guid]) > 1) {
              continue;
            }
          }
          /* End Working group feed. */

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          // add to list
          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      // Set next tmp max_id
      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }
    } while (count($activity) < $length && $selectCount <= 5 && !$endOfFeed);

    if (count($activity) < $length || count($activity) <= 0) {
      $endOfFeed = true;
    }

    $this->view->groupedFeeds = $grouped_actions;
    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    // Get some other info
    if (!empty($subject)) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
      if (!$subject || $subject->authorization()->isAllowed($viewer, 'comment')) {
        $this->view->enableComposer = true;
      }
      if (!empty($subject)) {
        // Get subject

        if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
          $pageSubject = $subject;
          if ($subject->getType() == 'sitepageevent_event')
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
        }
      }
    }
    $this->view->settingsApi = $settings = Engine_Api::_()->getApi('settings', 'core');
    // Assign the composing values
    if (empty($homefeed)) {
      $composePartials = array();
      foreach (Zend_Registry::get('Engine_Manifest') as $data) {
        if (empty($data['composer']) || !empty($data['composer']['facebook']) || !empty($data['composer']['twitter'])) {
          continue;
        }
        foreach ($data['composer'] as $type => $config) {
          if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
            continue;
          }
          $composePartials[] = $config['script'];
        }
      }

      $this->view->composePartials = $composePartials;
    }
    /*  Customization Start */
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Advancedactivity/View/Helper', 'Advancedactivity_View_Helper');
    if (($getListViewValue + $getPublishValue) != $getComposerValue)
      Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedactivity.post.active', $composerLimit);
    // Get lists if viewing own profile
    // if( $viewer->isSelf($subject) ) {
    // Get lists
    $this->view->tabtype = $settings->getSetting('advancedactivity.tabtype', 3);
    if (empty($subject) || $viewer->isSelf($subject)) {
      $this->view->enableList = $userFriendListEnable = $settings->getSetting('user.friends.lists');
      $viewer_id = $viewer->getIdentity();
      if ($userFriendListEnable && !empty($viewer_id)) {
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
        $this->view->countList = $countList = @count($lists);
      } else {
        $userFriendListEnable = 0;
      }
      $this->view->enableList = $userFriendListEnable;
    }
    if (empty($subject)) {
      if (!empty($viewer_id)) {
        $this->view->enableFriendListFilter = $enableFriendListFilter = $userFriendListEnable && $settings->getSetting('advancedactivity.friendlist.filtering', 1);
      } else {
        $this->view->enableFriendListFilter = $enableFriendListFilter = 0;
      }
      $enableContentTabs = 0;
      if (empty($subject)) {
        $this->view->contentTabs = $contentTabs = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1));
        $this->view->contentTabMax = $settings->getSetting('advancedactivity.defaultvisible', 7);
        $countContentTabs = @count($this->view->contentTabs);
        if ($countContentTabs)
          $enableContentTabs = 1;
      }
      $this->view->enableContentTabs = $enableContentTabs;
      $filterTabs = array();
      $i = 0;
      foreach ($contentTabs as $value) {
        if (empty($viewer_id) && in_array($value->filter_type, array('membership')))
          continue;
        $filterTabs[$i]['filter_type'] = $value->filter_type;
        $filterTabs[$i]['tab_title'] = $value->resource_title;
        $filterTabs[$i]['list_id'] = $value->content_id;
        $i++;
      }

      $enableNetworkListFilter = $settings->getSetting('advancedactivity.networklist.filtering', 0);
      if ($viewer_id && $enableNetworkListFilter) {
        $networkLists = Engine_Api::_()->advancedactivity()->getNetworks($enableNetworkListFilter, $viewer);
        $countNetworkLists = count($networkLists);
        if ($countNetworkLists) {
          if (count($filterTabs) > $this->view->contentTabMax)
            $filterTabs[$i]['filter_type'] = "separator";
          $i++;
          foreach ($networkLists as $value) {
            $filterTabs[$i]['filter_type'] = "network_list";
            $filterTabs[$i]['tab_title'] = $value->getTitle();
            $filterTabs[$i]['list_id'] = $value->getIdentity();
            $i++;
          }
        }
      }

      if ($enableFriendListFilter) {
        $countlistsLists = count($lists);
        if ($countlistsLists) {
          if (count($filterTabs) > $this->view->contentTabMax)
            $filterTabs[$i]['filter_type'] = "separator";
          $i++;
          foreach ($lists as $value) {
            $filterTabs[$i]['filter_type'] = "member_list";
            $filterTabs[$i]['tab_title'] = $value->title;
            $filterTabs[$i]['list_id'] = $value->list_id;
            $i++;
          }
        }
      }


      $this->view->canCreateCustomList = 0;
      if ($viewer_id) {
        $this->view->canCreateCustomList = $settings->getSetting('advancedactivity.customlist.filtering', 1);
        $customTypeLists = Engine_Api::_()->getDbtable('customtypes', 'advancedactivity')->getCustomTypeList(array('enabled' => 1));
        $count = count($customTypeLists);
        if (empty($count))
          $this->view->canCreateCustomList = 0;
        if ($this->view->canCreateCustomList) {
          $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer);
          $countCustomLists = count($customLists);
          if ($countCustomLists) {
            if (count($filterTabs) > $this->view->contentTabMax) {
              $filterTabs[$i]['filter_type'] = "separator";
              $i++;
            }
            foreach ($customLists as $value) {
              $filterTabs[$i]['filter_type'] = "custom_list";
              $filterTabs[$i]['tab_title'] = $value->title;
              $filterTabs[$i]['list_id'] = $value->list_id;
              $i++;
            }
          }
        }
      }
      $this->view->filterTabs = $filterTabs;
    }
    $front = Zend_Controller_Front::getInstance();
    $this->view->module_name = $front->getRequest()->getModuleName();
    $this->view->action_name = $front->getRequest()->getActionName();
    //  }
  }

}