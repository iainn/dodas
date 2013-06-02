<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminSettingsController extends Core_Controller_Action_Admin {

  public function upgradeAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_upgrade');
  }

  public function newsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_news');
  }
  
  public function helpInviteAction () {
    
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_helpInvite');
  }
  
  public function mapGuidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_map');
  }
  
  public function mapAction () {
  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_map');

    $this->view->form = $form = new Seaocore_Form_Admin_Map();
    if (!$this->getRequest()->isPost()) { return; }
    if (!$form->isValid($this->getRequest()->getPost())) {  return; }
    
    // Process
    $values = $_POST; //$form->getValues(); print_r($_POST);die;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Save settings
    foreach ($values as $key => $value) {
      if($settings->hasSetting($key))
      $settings->removeSetting($key);
      $settings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }
  
  public function informationAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_info');
  }

  public function lightboxAction() {

    $coreTable = Engine_Api::_()->getDbtable('pages', 'core');
		$page_id = $this->view->page_id = 
				$coreTable->select()->from($coreTable->info('name'), 'page_id')
				->where('name = ?', 'header')
				->query()
        ->fetchColumn();
    $content_id = 0;

    if(!empty($page_id)) {
     $contentTable = Engine_Api::_()->getDbtable('content', 'core');
		 $content_id = $contentTable->select()
				->from($contentTable->info('name'), 'page_id')
				->where('page_id = ?', $page_id)
				->where('type = ?', 'widget')
				->where('name = ?', 'seaocore.seaocores-lightbox')
				->query()
        ->fetchColumn();
    }
		$this->view->content_id = $content_id;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_main_lightbox');
    $this->view->form = $form = new Seaocore_Form_Admin_Lightbox();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $activityTextPath_Original = APPLICATION_PATH
              . '/application/modules/Activity/views/scripts/_activityText.tpl';
      if (file_exists($activityTextPath_Original) && !is_writable($activityTextPath_Original)) {
        $form->addError('Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.');
      } else {
        $activityTextPath_New = APPLICATION_PATH
                . '/application/modules/Seaocore/externals/Activity_activityText/_activityText.tpl';
        @chmod($activityTextPath_Original, 0755);
        $pos = false;
        $posSitepage = false;
        if (is_file($activityTextPath_Original)) {
          @chmod($activityTextPath_Original, 0777);
          $fileData = file($activityTextPath_Original);
          foreach ($fileData as $key => $value) {
            $pos = strpos($value, 'if ($sitealbumEnable && Engine_Api::_()->sitealbum()->showLightBoxPhoto()):');
            $posSitepage = strpos($value, 'if ($sitepageAlbumEnable && Engine_Api::_()->sitepage()->canShowPhotoLightBox()):');

            if ($pos !== false || $posSitepage !== false) {
              if (!@copy($activityTextPath_New, $activityTextPath_Original)) {
                //Do Nothing.....
              }
              break;
            }
          }
        }

        $values = $form->getValues();
        foreach ($values as $key => $value) {
          if ($key == 'seaocore_lightbox_option_display') {
            Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
          }
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
      }
    }
  }

  public Function guidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_main_lightbox');
  }

  public function upgradePluginAction() {
    $this->view->title = $title = str_replace("_", "/",  $this->_getParam("title"));
    $this->view->key = $key = trim($this->_getParam("key"));
    $this->view->ptype = $ptype = $this->_getParam("ptype");
    $this->view->name = $name = $this->_getParam("name");
    $this->view->version = $version = $this->_getParam("version");
    $this->view->calling = $calling = $this->_getParam("calling");
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check auth
    if( !$viewer || !$viewer->getIdentity() ) {
      $this->view->error = TRUE;
      $this->view->error_flag = 1;
    }
    $viewerLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->find($viewer->level_id)->current();
    if( null === $viewerLevel || $viewerLevel->flag != 'superadmin' ) {
      $this->view->error = TRUE;
      $this->view->error_flag = 1;
    }

    // Check plugin auth
    if( empty($ptype) || empty($name) || empty($version) ) {
      $this->view->error = TRUE;
    }

    $getXmlPath = $this->getXmlPath($calling);

    if( $this->getRequest()->isPost() ){
      include_once APPLICATION_PATH . '/application/modules/Seaocore/controllers/license/request.php';
      $this->view->setUpgradeUrl = TRUE;
    }
  }

  public function setUpgradeUrlAction() {
    // Build package url
    $authKeyRow = Engine_Api::_()->getDbtable('auth', 'core')->getKey(Engine_Api::_()->user()->getViewer(), 'package');
    $this->view->authKey = $authKey = $authKeyRow->id;

    //$installUrl = rtrim($this->view->baseUrl(), '/') . '/install/manage/select';

    $installUrl = rtrim($this->view->baseUrl(), '/') . '/install';
    if( strpos($this->view->url(), 'index.php') !== false ) {
      $installUrl .= '/index.php';
    }

   // $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . Engine_Api::_()->user()->getViewer()->getIdentity() . '&return=http://'. $_SERVER['HTTP_HOST'] .'/install/manage/select';
    $http = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
    $return_url = $http . $_SERVER['HTTP_HOST'] . $installUrl;
    $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . Engine_Api::_()->user()->getViewer()->getIdentity() . '&return=' . $return_url . '/manage/select';

    $this->view->installUrl = $installUrl;

    return $this->_helper->redirector->gotoUrl($installUrl, array('prependBase' => false));
  }

  public function getXmlPath($_calling) {
    switch($_calling) {
      case 'sitepage':
	return 'http://www.socialengineaddons.com/extensions/feed';
      break;
      case 'sitebusiness':
	return 'http://www.socialengineaddons.com/bizextensions/feed';
      break;
      default:
	return 'http://www.socialengineaddons.com/plugins/feed';
      break;
    }
  }
    
  //delete for 
  public function deleteAction()
  { 
    $this->_helper->layout->setLayout('admin-simple');
    $modules_mame = $this->_getParam('modules');
    
    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $version = $module_table->select()
            ->from($module_name, 'version')
            ->where($module_name . '.name =?', $modules_mame)
            ->limit(1)
            ->query()->fetchColumn();
    if (!empty($version)) {
				$fileName = "module-socialengineaddon-" . $version .".json" ;
		}
		
		if( !is_writeable(APPLICATION_PATH . '/application/modules/Socialengineaddon') ) {
      $this->view->meassge = 1; 
      //exit();
    }

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    if ($this->getRequest()->isPost()) {

			//calling function for remove the socialengineaddon directory.
			$pathlanguages = APPLICATION_PATH. "/application/modules/Socialengineaddon/";
      $this->rrmdir($pathlanguages);

			//Delete socialengineaddons package file.
			$pathpackgae = APPLICATION_PATH. "/application/packages/$fileName";
			if (@is_file($pathpackgae)) {
				@chmod($pathpackgae, 0777);
				unlink($pathpackgae);
			}

			//delete languagesfile
			$pathlanguages = APPLICATION_PATH. "/application/languages/en/socialengineaddon.csv";
			if (@is_file($pathlanguages)) {
				@chmod($pathlanguages, 0777);
				unlink($pathlanguages);
			}
			
			//delete table of socialengineaddon.
		  $db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`menu` = "socialengineaddon_admin_main";');
			$db->query('DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = "core_admin_plugins_Socialengineaddon";');
			$db->query('DROP TABLE IF EXISTS `engine4_socialengineaddons`;');
			$db->query('DROP TABLE `engine4_socialengineaddon_locations`;');
			$db->query('DROP TABLE `engine4_socialengineaddon_tabs`;');
			
			
	    $db->query('DELETE FROM `engine4_core_modules` WHERE `engine4_core_modules`.`name` = "socialengineaddon" LIMIT 1;');

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully removed the old version of “SocialEngineAddOns Core Plugin” from your site.'))
      ));
    }
  }
  
  //Removes directory
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$fp = opendir($dir);
			
			if ( $fp ) {
				while ($f = readdir($fp)) {
					$file = $dir . "/" . $f;
					if ($f == "." || $f == "..") {
						continue;
					}
					else if (is_dir($file) && !is_link($file)) {
						@chmod($file, 0777);
						$this->rrmdir($file);
					}
					else {
						@chmod($file, 0777);
						unlink($file);
					}
				}
				closedir($fp);
				rmdir($dir);
			}
		}
	}
}