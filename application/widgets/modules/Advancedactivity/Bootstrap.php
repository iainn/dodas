<?php

class Advancedactivity_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license.php';
  }

  protected function _initFrontController() {

    $this->initViewHelperPath();
    $this->initActionHelperPath();
    //Initialize helper
     Zend_Controller_Action_HelperBroker::addHelper(new
Advancedactivity_Controller_Action_Helper_Advancedactivitys());
    $headScript = new Zend_View_Helper_HeadScript();
    $notificationIsEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.isenable.notification', 1);
    if ($notificationIsEnable) {
      if (Zend_Registry::isRegistered('StaticBaseUrl')) {
        $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
              . 'application/modules/Advancedactivity/externals/scripts/notification.js');
      } else {
        $headScript->appendFile('application/modules/Advancedactivity/externals/scripts/notification.js');
      }
     
    }
  }

}