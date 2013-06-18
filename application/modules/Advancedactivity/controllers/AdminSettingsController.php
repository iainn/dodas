<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $onactive_disabled = array('advancedactivity_sitetabtitle','advancedactivity_post_canedit', 'advancedactivity_tabtype','advancedactivity_maxautoload',
        'advancedactivity_icon', 'logo_photo_preview', 'advancedactivity_info_tooltips',
        'advancedactivity_scroll_autoload', 'advancedactivity_composer_options', 'thirdparty_settings',
        'advancedactivity_update_frequency', 'advancedactivity_icon1', 'logo_photo_preview1', 'submit', "advancedactivity_post_searchable","advancedactivity_comment_show_bottom_post","aaf_tagging_module", "linkedin_settings_temp", "seaocore_google_map_key", "linkedin_enable","aaf_largephoto_enable","advancedactivity_networklist_privacy");
    $this->view->googleapikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key', '');
    $socialDNApublish = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdnapublisher');
    if ('publish' != Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable && empty($socialDNApublish)) {
      $onactive_disabled[] = "advancedactivity_post_byajax";
    }
    $afteractive_disabled = array('environment_mode', 'submit_lsetting');
    include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license1.php';
  }

  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_faq');
  }

  public function guidelinesAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_settings');
  }
  
  public function readmeAction() {
    
  }

  // This is the main action which are showing when click on "Welcome Settings" tab from the admin area.
  public function welcomeSettingsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_welcomesettings');

    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_welcome_manage', array(), 'advancedactivity_admin_welcome_manage');

    $this->view->form = $form = new Advancedactivity_Form_Admin_WelcomeSettings();
    $this->view->isWelcomePageCorrect = Engine_Api::_()->advancedactivity()->isWelcomePageCorrect();

    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageTableName = $pageTable->info('name');

    $selectPage = $pageTable->select()
            ->from($pageTableName, array('page_id'))
            ->where('name =?', 'advancedactivity_index_welcometab')
            ->limit(1);
    $this->view->pageId = $selectPage->query()->fetch();


    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license2.php';
  }

  // This is the main action which are showing when click on "Welcome Settings" tab from the admin area.
  public function manageCustomBlockAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_welcomesettings');

    $this->view->sub_navigation = $sub_navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_welcome_manage', array(), 'advancedactivity_admin_customblock_manage');

    $page = $this->_getParam('page', 1);
    $sortingColumnName = $this->_getParam('idSorting', 0);
    include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license2.php';

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      if (!empty($values['customblock_id']) && !array_key_exists('delete', $values)) {
        $getOrder = $values['customblock_id'];
        $tableObject = Engine_Api::_()->getDbTable('customblocks', 'advancedactivity');
        $orderId = 1;
        foreach ($getOrder as $id) {
          $tableObject->update(array("order" => $orderId), array("customblock_id =?" => $id));
          $orderId++;
        }
      } else {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $tableObject = Engine_Api::_()->getItem('advancedactivity_customblock', $value);
            if (!empty($tableObject->custom)) {
              $tableObject->delete();
            }
          }
        }
      }
      $this->_helper->redirector->gotoRoute(array('module' => 'advancedactivity', 'controller' => 'settings', 'action' => 'manage-custom-block'), 'admin_default', true);
    }
  }

  public function customBlockCreateAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_welcomesettings');

    $this->view->customblock_id = $is_edit = $this->_getParam('customblock_id', 0);
    $textFlag = $this->_getParam('textFlag', 0);
    $limitation = $limitation_value = 0;
    $this->view->form = $form = new Advancedactivity_Form_Admin_CustomBlockCreate();

    // Only in the case of Edit.
    if (!empty($is_edit)) {
      $getContent = Engine_Api::_()->getItem('advancedactivity_customblock', $is_edit);
      if (!empty($getContent)) {
        $limitation = $getContent->limitation;
        $limitation_value = $getContent->limitation_value;
        $form->title->setValue($getContent->title);
        $form->limitation->setValue($getContent->limitation);
        $form->limitation_value->setValue($getContent->limitation_value);

        if (empty($textFlag)) {
          $form->description->setValue($getContent->description);
        } else {
          $form->text_description->setValue($getContent->description);
        }

        //SHOW PREFIELD NETWORKS
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
        if ($networks) {
          if ($custom_networks = $form->getElement('networks')) {
            $custom_networks->setValue(Zend_Json_Decoder::decode($getContent->networks));
          }
        }

        //SHOW PREFIELD LEVELS
        if ($levels = $form->getElement('levels')) {
          $levels->setValue(Zend_Json_Decoder::decode($getContent->levels));
        }
      }
    }

    $form->temp_limitation->setValue($limitation);
    $form->temp_limitation_value->setValue($limitation_value);

    // Here we are change the "Description" of the form because If there are multiple language then "textFlag" create problem in language conversion.
    if (empty($textFlag)) {
      $form->removeElement('text_description');
      $textLinkFlag = $this->view->url(array('module' => 'advancedactivity', 'controller' => 'settings',
          'action' => 'custom-block-create', 'textFlag' => 1, 'page_id' => $page_id, 'customblock_id' => $is_edit), 'admin_default', true);
      $clickHere = "<a href='" . $textLinkFlag . "'> click here </a>";
      $textDescription = $this->view->translate("If your site supports multiple laguage then %s for the compatible Text input box.", $clickHere);
    } else {
      $form->removeElement('description');
      $textLinkFlag = $this->view->url(array('module' => 'advancedactivity', 'controller' => 'settings', 'action' => 'custom-block-create', 'textFlag' => 0, 'page_id' => $page_id, 'customblock_id' => $is_edit), 'admin_default', true);
      $textDescription = Zend_Registry::get('Zend_Translate')->_("If your site supports only one laguage then %s for the compatible Text input box.", $clickHere);
    }
    $form->text_flag->setDescription($textDescription);
    $form->text_flag->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    if (!empty($values['limitation']) && empty($values['limitation_value'])) {
      $error = Zend_Registry::get('Zend_Translate')->_('Limitation value could not be empty.');
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    if (!empty($values['levels'])) {
      $values['levels'] = Zend_Json_Encoder::encode($values['levels']);
    }

    if (!empty($values['networks'])) {
      $values['networks'] = Zend_Json_Encoder::encode($values['networks']);
    }

    if (!empty($values['text_description'])) {
      $values['description'] = $values['text_description'];
    }
    unset($values['text_description']);
    unset($values['text_flag']);
    unset($values['temp_limitation']);
    unset($values['temp_limitation_value']);
    unset($values['flag']);

    include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license2.php';
    $this->_helper->redirector->gotoRoute(array('module' => 'advancedactivity', 'controller' => 'settings', 'action' => 'manage-custom-block'), 'admin_default', true);
  }

  // Function: When approved or disapproved page (Help & Learn more page).
  public function enabledAction() {
    $this->view->enabled = $enabled = $this->_getParam('enabled');
    $this->view->id = $id = $this->_getParam('id');

    // Check post
    if ($this->getRequest()->isPost()) {
      $pagesettingsTable = Engine_Api::_()->getDbTable('customblocks', 'advancedactivity')->setUpdate(array('enabled' => $enabled), $id);

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Successfully done.')
      ));
    }
  }

  public function customBlockDeleteAction() {
    $customblock_id = $this->_getParam('customblock_id');
    // $this->view->customblock_id = $customblock_id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $table = Engine_Api::_()->getItem('advancedactivity_customblock', $customblock_id);
      if (!empty($table)) {
        $table->delete();
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Deleted Successsfully.')
      ));
    }
  }

  public function notificationAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advancedactivity_admin_main', array(), 'advancedactivity_admin_main_notificationsettings');
    $form = new Advancedactivity_Form_Admin_NotificationSettings();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      include APPLICATION_PATH . '/application/modules/Advancedactivity/controllers/license/license2.php';
    }
    $this->view->form = $form;
  }

}
