<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Actions.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Model_DbTable_Actions extends Activity_Model_DbTable_Actions {

  protected $_rowClass = 'Activity_Model_Action';
  protected $_name = 'activity_actions';
  protected $_serializedColumns = array('params');
  protected $_actionTypes;

  public function addActivity(Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type, $body = null, $privacy = null, array $params = null) {
    // Disabled or missing type
    $typeInfo = $this->getActionType($type);
    if (!$typeInfo || !$typeInfo->enabled) {
      return;
    }

    // User disabled publishing of this type
    $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
    if ($subject->getType() == "user") {
      if (!$actionSettingsTable->checkEnabledAction($subject, $type)) {
        return;
      }
    } else {
      if (!$actionSettingsTable->checkEnabledAction($object, $type)) {
        return;
      }
    }
    $postByAJAX = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.post.byajax', 1);
    $socialDNApublish = 0;
    if (empty($postByAJAX)) {
      $socialDNApublish = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdnapublisher');
    }
    $activityPoints = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('activitypoints');
    if ((empty($postByAJAX) && $socialDNApublish) || ($activityPoints && empty($socialDNApublish))) {
      // To make compatebile with "Social DNA Publisher" Plugin And "Activity Points" PLugin
      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivity', array(
          'subject' => $subject,
          'object' => $object,
          'type' => $type,
          'body' => $body,
          'params' => $params,
              ));
    }
    // Create action
    $action = $this->createRow();
    $action->setFromArray(array(
        'type' => $type,
        'subject_type' => $subject->getType(),
        'subject_id' => $subject->getIdentity(),
        'object_type' => $object->getType(),
        'object_id' => $object->getIdentity(),
        'body' => (string) $body,
        'params' => (array) $params,
        'date' => date('Y-m-d H:i:s'),
        'privacy' => $privacy,
        'commentable' => ($typeInfo->commentable > 0 ) ? 1 : 0,
        'shareable' => ($typeInfo->shareable > 0) ? 1 : 0
    ));
    $action->save();
    // Add bindings
    $this->addActivityBindings($action, $type, $subject, $object);

    // We want to update the subject
    if (isset($subject->modified_date)) {
      $subject->modified_date = date('Y-m-d H:i:s');
      $subject->save();
    }

    if (empty($postByAJAX) && $socialDNApublish) {
      // To make compatebile with "Social DNA Publisher" Plugin And "Activity Points" PLugin
      $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onSemodsAddActivityAfter', array(
          'subject' => $subject,
          'object' => $object,
          'type' => $type,
          'body' => $body,
          'params' => $params,
          'action' => $action,
              ));
    }
    return $action;
  }

  public function getActivity(User_Model_User $user, array $params = array()) {
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id
    $getAafPostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('getaaf.post.type', 0);
    if (in_array($actionTypeGroup, array('only_network'))) {
      $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
      $ids = $networkTable->getMembershipsOfIds($user);
      if (count($ids) <= 0) {
        return;
      }
    }
    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $mainActionTypes = array();
    $birthday_post_enable = false;
    //CHECK IF SITEMOBILE PLUGIN IS ENABLED AND SITE IS IN MOBILE MODE:
    $enable_sitemobilemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if ($enable_sitemobilemodule) 
      $checkMobMode = Engine_Api::_()->sitemobile()->checkMode ('mobile-mode');
    else 
      $checkMobMode = 0;
    // Filter out types set as not displayable
    foreach ($masterActionTypes as $type) {
      
      //IF SITE MOBILE MODULE IS ENABLED AND SITE IS IN MOBILE MODE THEN WE WILL ONLY SHOW THE MODULES FEED WHICH ARE COMPATIBLE WITH OUR SITE MOBILE PLUGIN.
      if ($checkMobMode && isset($type->module)) {
        $supportedModules = Engine_Api::_()->sitemobile()->isSupportedModule ($type->module);
        if (!$supportedModules) continue;
      }
      if ($type->displayable & 4) {
        $mainActionTypes[] = $type->type;
      } elseif ($type->type == 'birthday_post') {
        $birthday_post_enable = (bool) $type->enabled;
      }
    }
    $showPost = in_array("post", $mainActionTypes);
    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $mainActionTypes = array_intersect($mainActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $mainActionTypes = array_diff($mainActionTypes, $hideTypes);
    }
    $mainActionTypesArray = $mainActionTypes;
    // Nothing to show
    if (empty($mainActionTypes) || empty($getAafPostType)) {
      return null;
    }
    // Show everything
    else if (count($mainActionTypes) == count($masterActionTypes)) {
      $mainActionTypes = true;
    }
    // Build where clause
    else {
      $mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
            ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }
    $friendsFlage = false;
    $action_ids = array();
    if ($actionTypeGroup == 'user_saved') {
      $action_ids = Engine_Api::_()->getDbtable('saveFeeds', 'advancedactivity')->getSaveFeeds($user, $mainActionTypesArray, array('limit' => $limit, 'max_id' => $max_id));
      if (empty($action_ids))
        return null;
    }

    foreach ($responses as $response) {
      if (empty($response))
        continue;

      if ($membership && !in_array($response['type'], array('members', 'members_list'))) {
        continue;
      }

      if (in_array($actionTypeGroup, array('membership', 'member_list')) && !in_array($response['type'], array('members', 'members_list'))) {
        continue;
      } elseif (in_array($actionTypeGroup, array('membership', 'member_list'))) {
        if ($response['type'] == 'members') {
          $friendsFlage = true;
        }
      }
      if (in_array($actionTypeGroup, array('only_network', 'network_list')) && !in_array($response['type'], array('network'))) {
        continue;
      }

      if ($actionTypeGroup == 'network_list' && !empty($listTypeFilter)) {
        $response['data'] = $listTypeFilter;
      }
      if ($actionTypeGroup == 'member_list' && !empty($listTypeFilter)) {
        if ($response['type'] == 'members') {
          $response['data'] = $listTypeFilter['member_list']['value'];
        } elseif ($response['type'] == 'members_list') {
          $response['data'] = $listTypeFilter['member_list']['list_ids'];
        }
      }

      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      if ($mainActionTypes !== true) {
        if ($showPost && !empty($actionTypeGroup) && !in_array($actionTypeGroup, array('all', 'members', 'members_list', 'sitepage', 'sitebusiness', 'custom_list', 'like', 'posts', 'photo', 'music', 'video'))) {
          if ($actionTypeGroup == "list") {
            $object_type = "list_listing";
          } else {
            $object_type = $actionTypeGroup;
          }
          $select->where('(type IN(' . $mainActionTypes . ') OR (type = "post" and object_type ="' . $object_type . '") )');
        } else {
          $select->where('type IN(' . $mainActionTypes . ')');
        }
      }

      if ($actionTypeGroup == 'member_list' && !empty($listTypeFilter) && $response['type'] == 'members_list') {
        $select->where('type IN(' . $mainActionTypes . ')');
      }
      // Add order/limit
      $select
              ->order('action_id DESC')
              ->limit($limit);
      if (!empty($action_ids)) {
        $select->where('action_id IN(?)', (array) $action_ids);
      }
      if (in_array($actionTypeGroup, array('membership', 'member_list')) && $response['type'] == 'members') {
        $ids = $user->membership()->getMembershipsOfIds();
        if (!empty($ids)) {
          $select
                  ->where('subject_type = ?', 'user')
                  ->where('subject_id IN (?)', (array) $ids);
        }
      }
      if ($actionTypeGroup == 'custom_list' && !empty($listTypeFilter)) {
        foreach ($listTypeFilter as $resource) {
          $selectSubject = clone $select;
          // Add subject to main query
          $selectSubject
                  ->where('subject_type = ?', $resource->child_type)
                  ->where('subject_id = ?', $resource->child_id);
          $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
          // Add object to main query
          $selectObject = clone $select;

          $selectObject
                  ->where('object_type = ?', $resource->child_type)
                  ->where('object_id = ?', $resource->child_id);
          $union->union(array('(' . $selectObject->__toString() . ')')); // (string) not work before PHP 5.2.0
        }
      } else {
        // Add to main query
        $union->union(array('(' . $select->__toString() . ')')); // (string) not work before PHP 5.2.0
      }
    }
    // Finish main query
    $union
            ->order('action_id DESC')
            ->limit($limit);
    if (in_array($actionTypeGroup, array('membership', 'member_list')) && !$friendsFlage) {
      return;
    }
    // Get actions
    $actions = $db->fetchAll($union);

    // Process ids
    $ids = array();
    if (in_array($actionTypeGroup, array('all', 'posts'))) {
      $ids = $this->getTaggedBaseActionIds($user, array('min' => $min_id, 'max' => $max_id));
    }
    $birthDayPluginEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday');
    if ($actionTypeGroup == 'all' && $birthday_post_enable && $birthDayPluginEnable && $user->getIdentity()) {
      $ids = array_merge($ids, $this->getIncludeBirthdayWishFeed($min_id, $max_id));
    }

    // No visible actions and ids
    if (empty($actions) && empty($ids)) {
      return null;
    }

    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $this->fetchAll(
                    $this->select()
                            ->where('action_id IN(' . join(',', $ids) . ')')
                            ->order('action_id DESC')
                            ->limit($limit)
    );
  }

  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user, array $params = array()) {
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id
    $getAafMembershipType = Engine_Api::_()->getApi('settings', 'core')->getSetting('getaaf.membership.type', 0);
    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);
    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $subjectActionTypes = array();
    $objectActionTypes = array();
    // Filter types based on displayable
    foreach ($masterActionTypes as $type) {
      if (($about->getType() == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page') && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) || ($about->getType() == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business') && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())) {
        if ($actionTypeGroup == 'owner' && isset($type->is_object_thumb) && !$type->is_object_thumb)
          continue;
        if ($actionTypeGroup == 'membership' && isset($type->is_object_thumb) && $type->is_object_thumb)
          continue;
      }
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
      $objectActionTypes = array_intersect($objectActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
      $objectActionTypes = array_diff($objectActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($getAafMembershipType) || (empty($subjectActionTypes) && empty($objectActionTypes))) {
      return null;
    }

    if (empty($subjectActionTypes)) {
      $subjectActionTypes = null;
    } else if (count($subjectActionTypes) == count($masterActionTypes)) {
      $subjectActionTypes = true;
    } else {
      $subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
    }

    if (empty($objectActionTypes)) {
      $objectActionTypes = null;
    } else if (count($objectActionTypes) == count($masterActionTypes)) {
      $objectActionTypes = true;
    } else {
      $objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
        'about' => $about,
            ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }
    $member_ids = array();
    if ($actionTypeGroup == 'owner') {
      if ($about instanceof Sitepage_Model_Page) {
        if (Engine_Api::_()->hasItemType('sitepage_page') && !Engine_Api::_()->sitepage()->isFeedTypePageEnable()) {
          $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
          $manageadminTableName = $manageadminTable->info('name');
          $select = $manageadminTable->select()
                  ->from($manageadminTableName, 'user_id')
                  ->where('page_id = ?', $about->getIdentity());
          $member_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        }
      } elseif ($about instanceof Sitebusiness_Model_Business) {
        if (Engine_Api::_()->hasItemType('sitebusiness_business') && !Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable()) {
          $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitebusiness');
          $manageadminTableName = $manageadminTable->info('name');
          $select = $manageadminTable->select()
                  ->from($manageadminTableName, 'user_id')
                  ->where('business_id = ?', $about->getIdentity());
          $member_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        }
      } else {
        if ($about instanceof User_Model_User) {
          $member_ids[] = $about->getIdentity();
        } elseif ($about instanceof Group_Model_Group) {
          $objectParent = $about->getParent('user');
          if ($objectParent instanceof User_Model_User) {
            $member_ids[] = $objectParent->getIdentity();
          }
          foreach ($about->getOfficerList()->getAll() as $value) {
            $member_ids[] = $value->child_id;
          }
        } else {
          $objectParent = $about->getParent('user');
          if ($objectParent instanceof User_Model_User) {
            $member_ids[] = $objectParent->getIdentity();
          }
        }
      }
    } elseif ($actionTypeGroup == 'membership') {
      $member_ids = $user->membership()->getMembershipsOfIds();
      if (empty($member_ids))
        return;
    }
    foreach ($responses as $response) {
      if (empty($response))
        continue;

      // Target info
      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      // Add order/limit
      $select
              ->order('action_id DESC')
              ->limit($limit);


      // Add subject to main query
      $selectSubject = clone $select;
      if ($subjectActionTypes !== null) {
        if ($subjectActionTypes !== true) {
          $selectSubject->where('type IN(' . $subjectActionTypes . ')');
        }
        $selectSubject
                ->where('subject_type = ?', $about->getType())
                ->where('subject_id = ?', $about->getIdentity());

        if (!empty($member_ids)) {
          $selectSubject
                  ->where('object_type = ?', 'user')
                  ->where('object_id  In(?)', (array) $member_ids);
        }

        $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }

      // Add object to main query
      $selectObject = clone $select;
      if ($objectActionTypes !== null) {
        if ($objectActionTypes !== true) {
          $selectObject->where('type IN(' . $objectActionTypes . ')');
        }
        $selectObject
                ->where('object_type = ?', $about->getType())
                ->where('object_id = ?', $about->getIdentity());

        if (!empty($member_ids)) {
          $selectObject
                  ->where('subject_type = ?', 'user')
                  ->where('subject_id   IN(?)', (array) $member_ids);
        }
        $union->union(array('(' . $selectObject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }
    }

    // Finish main query
    $union
            ->order('action_id DESC')
            ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // Process ids
    $ids = array();
    if ($actionTypeGroup == 'all') {
      if (($about->getType() == 'sitepageevent_event')) {
        $ids = Engine_Api::_()->getApi('subCore', 'sitepage')->getEveryonePageProfileFeeds($about, $this->_getInfo($params));
      }
      if (($about->getType() == 'sitebusinessevent_event')) {
        $ids = Engine_Api::_()->getApi('subCore', 'sitebusiness')->getEveryoneBusinessProfileFeeds($about, $this->_getInfo($params));
      }
    }
    // No visible actions and ids
    if (empty($actions) && empty($ids)) {
      return null;
    }

    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $this->fetchAll(
                    $this->select()
                            ->where('action_id IN(' . join(',', $ids) . ')')
                            ->order('action_id DESC')
                            ->limit($limit)
    );
  }

  // Utility

  /**
   * Add an action-privacy binding
   *
   * @param int $action_id
   * @param string $type
   * @param Core_Model_Item_Abstract $subject
   * @param Core_Model_Item_Abstract $object
   * @return int The insert id
   */
  public function addActivityBindings($action) {
    // Get privacy bindings
    $privacy = $action->privacy;
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('addActivity', array(
        'subject' => $action->getSubject(),
        'object' => $action->getObject(),
        'type' => $action->type,
        'content' => $privacy
            ));

    $notInclude = false;
    if (!empty($privacy) && !in_array($privacy, array('everyone', 'networks', 'friends')) && !Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($privacy)) {
      $notInclude = true;
    }
    // Add privacy bindings
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $include_type = array();
    foreach ((array) $event->getResponses() as $response) {
      if (isset($response['target'])) {
        $target_type = $response['target'];
        $target_id = 0;
      } else if (isset($response['type']) && isset($response['identity'])) {
        $target_type = $response['type'];
        $target_id = $response['identity'];
      } else {
        continue;
      }

      if (!empty($privacy) && $privacy == 'friends' && !in_array($target_type, array('members_list', 'owner', 'parent', 'members'))) {
        continue;
      }

      if (!empty($privacy) && $privacy == 'networks' && !in_array($target_type, array('members_list', 'owner', 'parent', 'members', 'network'))) {
        continue;
      }

      if (Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($privacy) && !in_array($target_type, array('network_list', 'owner', 'parent'))) {
        continue;
      } elseif ($target_type == 'network_list') {
        $target_type = 'network';
      }

      if ($notInclude && !in_array($target_type, array('members_list', 'owner', 'parent'))) {
        continue;
      }

//      if ($target_type == 'sitepage_page' && $action->type == 'like_sitepage_page') {
//        continue;
//      }

      if (isset($include_type[$target_type]) && in_array($target_id, $include_type[$target_type])) {
        continue;
      }
      $include_type[$target_type][] = $target_id;
      $streamTable->insert(array(
          'action_id' => $action->action_id,
          'type' => $action->type,
          'target_type' => (string) $target_type,
          'target_id' => (int) $target_id,
          'subject_type' => $action->subject_type,
          'subject_id' => $action->subject_id,
          'object_type' => $action->object_type,
          'object_id' => $action->object_id,
      ));
    }
    return $this;
  }

  // Utility

  protected function _getInfo(array $params) {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
        'limit' => $settings->getSetting('activity.length', 20),
        'action_id' => null,
        'max_id' => null,
        'min_id' => null,
        'showTypes' => null,
        'hideTypes' => null,
    );

    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
      if (isset($params[$arg]))
        unset($params[$arg]);
    }
    $newParams = array_merge($newParams, $params);
    return $newParams;
  }

  //---------

  public function getActivityOfShare(User_Model_User $user, array $params = array()) {
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id
    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $mainActionTypes = array();

    // Filter out types set as not displayable
    foreach ($masterActionTypes as $type) {
      if ($type->displayable & 4) {
        $mainActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $mainActionTypes = array_intersect($mainActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $mainActionTypes = array_diff($mainActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($mainActionTypes)) {
      return null;
    }
    // Show everything
    else if (count($mainActionTypes) == count($masterActionTypes)) {
      $mainActionTypes = true;
    }
    // Build where clause
    else {
      $mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
            ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response))
        continue;

      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_ids) {
        $select->where('action_id IN(?)', (array) $action_ids);
      }

      if ($mainActionTypes !== true) {
        $select->where('type IN(' . $mainActionTypes . ')');
      }

      // Add order/limit
      $select
              ->order('action_id DESC')
              ->limit($limit);

      // Add to main query
      $union->union(array('(' . $select->__toString() . ')')); // (string) not work before PHP 5.2.0
    }

    // Finish main query
    $union
            ->order('action_id DESC')
            ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // No visible actions
    if (empty($actions)) {
      return null;
    }

    // Process ids
    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $this->fetchAll(
                    $this->select()
                            ->where('action_id IN(' . join(',', $ids) . ')')
                            ->order('action_id DESC')
                            ->limit($limit)
    );
  }

  // get friends base birthday wish action id
  public function getBirthdayWishActionIds($params=array()) {

    $actionTableName = $this->info('name');
    $user = Engine_Api::_()->user()->getViewer();
    $memberIds = array();
    $memberIds = $user->membership()->getMembershipsOfIds();
    //  $memberIds[] = $user->getIdentity();
    $params['posterIds'] = array_merge($memberIds, array($user->getIdentity()));
    if (isset($params['memberIds'])) {
      $memberIds = $params['memberIds'];
    }
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($actionTableName, array('*', "DATE_FORMAT(" . "date, '%Y') AS year"))
            ->where('type = ?', 'birthday_post')
            ->where('object_type = ?', 'user')
            ->order('date DESC');

    if (isset($params['year']) && !empty($params['year'])) {
      $select->where("DATE_FORMAT(date, '%Y') = ?", $params['year']);
    }
    if (!empty($memberIds))
      $select->where('object_id in(?)', (array) $memberIds);

    $select->where('subject_id in(?)', (array) $params['posterIds'])
            ->where('subject_type = ?', 'user');
    return $this->fetchAll($select);
  }

  public function getIncludeBirthdayWishFeed($min=null, $max=null) {
    $results = $this->getBirthdayWishActionIds();
    $actionsIds = array();
    $includedMember = array();
    foreach ($results as $value) {
      if ((!empty($min) && $min > $value->action_id ) || (!empty($max) && $max < $value->action_id )) {
        continue;
      }
      if (isset($includedMember[$value->year]) && in_array($value->object_id, $includedMember[$value->year]))
        continue;

      $includedMember[$value->year][] = $value->object_id;
      $actionsIds[] = $value->action_id;
    }
    return $actionsIds;
  }

  public function getBirthdayFeedRelatedActions(Activity_Model_Action $action) {

    $posterIds = array();
    $year = date('Y', strtotime($action->date));
    return $this->getBirthdayWishActionIds(array('memberIds' => array($action->object_id), 'year' => $year));
  }

  public function getTaggedBaseActionIds($user, $params=array()) {

    $memberIds = $user->membership()->getMembershipsOfIds();
    $memberIds[] = $user->getIdentity();
    $table = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $select = $table->select()
            ->from($table->info('name'), "resource_id")
            ->where('resource_type = ?', 'activity_action')
            ->where('tag_type = ?', 'user')
            ->where('tag_id in(?)', (array) $memberIds)
            ->order('creation_date 	DESC');
    if (!empty($params['min'])) {
      $select->where('resource_id >= ?', $params['min']);
    } else if (!empty($params['max'])) {
      $select->where('resource_id <= ?', $params['max']);
    }

    return $select->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

}