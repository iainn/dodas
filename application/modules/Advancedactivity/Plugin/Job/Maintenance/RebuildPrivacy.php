<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: RebuildPrivacy.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Plugin_Job_Maintenance_RebuildPrivacy extends Core_Plugin_Job_Abstract {

  protected function _execute() {
    // Prepare
    $position = $this->getParam('position', 0);
    $progress = $this->getParam('progress', 0);
    $total = $this->getParam('total');
    $limit = $this->getParam('limit', 100);
    $isComplete = false;
    $break = false;


    // Prepare tables
    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');


    // Populate total
    if (null === $total) {
      $total = $actionTable->select()
              ->from($actionTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
              ->query()
              ->fetchColumn(0)
      ;
      $this->setParam('total', $total);
      if (!$progress) {
        $this->setParam('progress', 0);
      }
      if (!$position) {
        $this->setParam('position', 0);
      }
    }

    // Complete if nothing to do
    if ($total <= 0) {
      $this->_setWasIdle();
      $this->_setIsComplete(true);
      return;
    }


    // Don't run yet if there are any rebuild privacy plugins running
    /*
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');
      $rebuildPrivacyTaskCount = $tasksTable->select()
      ->from($tasksTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
      ->where('plugin != ?', 'Activity_Plugin_Task_Maintenance_RebuildPrivacy')
      ->where('category = ?', 'rebuild_privacy')
      ->where('state != ?', 'dormant')
      ->query()
      ->fetchColumn(0)
      ;

      if( $rebuildPrivacyTaskCount > 0 ) {
      $this->_setWasIdle();
      return;
      }
     */


    // Execute
    $count = 0;

    while (!$break && $count <= $limit) {

      $action = $actionTable->fetchRow($actionTable->select()
                      ->where('action_id >= ?', (int) $position + 1)->order('action_id ASC')->limit(1));

      // Nothing left
      if (!$action) {
        $break = true;
        $isComplete = true;
      }

      // Main
      else {
        $position = $action->getIdentity();
        $count++;
        $progress++;

        $actionTable->resetActivityBindings($action);
        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        if (Engine_Api::_()->hasItemType('sitepage_page') && $action->object_type == 'sitepage_page' && $settingsCoreApi->sitepage_feed_type) {
          Engine_Api::_()->getApi('subCore', 'Sitepage')->deleteFeedStream($action);
        }else if (Engine_Api::_()->hasItemType('sitebusiness_business') && $action->object_type == 'sitebusiness_business' && $settingsCoreApi->sitebusiness_feed_type) {
          Engine_Api::_()->getApi('subCore', 'Sitebusiness')->deleteFeedStream($action);
        }
        unset($action);
      }
    }


    // Cleanup
    $this->setParam('position', $position);
    $this->setParam('progress', $progress);
    $this->_setIsComplete($isComplete);
  }

}