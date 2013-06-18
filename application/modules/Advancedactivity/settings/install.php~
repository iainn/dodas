<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Installer extends Engine_Package_Installer_Module {

  function onPreinstall() {
    $db = $this->getDb();
    $PRODUCT_TYPE = 'advancedactivity';
    $PLUGIN_TITLE = 'Advancedactivity';
    $PLUGIN_VERSION = '4.5.0p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Advanced Activity Feeds Wall Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.5.0';
    $PRODUCT_TITLE = 'Advanced Activity Feeds / Wall Plugin';
    $getErrorMsg = $this->getVersion();
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/Advancedactivity/controllers/license/license3.php";
    } else {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }
    parent::onPreinstall();
  }

  function onInstall() {
    $db = $this->getDb();

    //Start Group feed work
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
    if (!empty($table_exist)) {
      $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_grouped'")->fetch();
      if (empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_grouped` TINYINT( 1 ) NOT NULL DEFAULT '0'");
        $db->query("UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'tagged' LIMIT 1;");
        $db->query("UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'friends' LIMIT 1;");

        //For like feed work.
        $isMod = $db->query("SELECT * FROM `engine4_activity_actiontypes` WHERE `type` LIKE '%like_%' AND `is_grouped` = '0'")->fetchAll();
        if (!empty($isMod)) {
          foreach ($isMod as $modArray) {
            $db->query("UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` = '" . $modArray['type'] . "' LIMIT 1;");
          }
        }
      } 
      else {
				$db->query("UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'tagged' LIMIT 1;");
				$db->query("UPDATE `engine4_activity_actiontypes` SET `is_grouped` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'friends' LIMIT 1;");
      }
    }
    //End Group feed work

    $table_engine4_album_albums_exist = $db->query("SHOW TABLES LIKE 'engine4_album_albums'")->fetch();
    if ($table_engine4_album_albums_exist) {
      $column = $db->query("SHOW COLUMNS FROM `engine4_album_albums` LIKE 'type'")->fetch();
      if (!empty($column)) {
        $type = $column['Type'];
        if (!strpos($type, "'wall', 'wall_friend', 'wall_network',")) {
          $type = str_replace("'wall',", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
          $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
        } else if (!strpos($type, "'wall', 'wall_friend', 'wall_network', 'wall_onlyme',")) {
          $type = str_replace("'wall', 'wall_friend', 'wall_network', ", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
          $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
        }
      }
    }
    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='advancedactivity';");

    $table_engine4_music_playlists_exist = $db->query("SHOW TABLES LIKE 'engine4_music_playlists'")->fetch();
    if ($table_engine4_music_playlists_exist) {
      $column = $db->query("SHOW COLUMNS FROM `engine4_music_playlists` LIKE 'special'")->fetch();
      if (!empty($column)) {
        $type = $column['Type'];
        if (!strpos($type, "'wall', 'wall_friend', 'wall_network',")) {
          $type = str_replace("'wall',", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
          $db->query("ALTER TABLE `engine4_music_playlists` CHANGE `special` `special` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
        } else if (!strpos($type, "'wall', 'wall_friend', 'wall_network', 'wall_onlyme',")) {
          $type = str_replace("'wall', 'wall_friend', 'wall_network', ", "'wall', 'wall_friend', 'wall_network', 'wall_onlyme', ", $type);
          $db->query("ALTER TABLE `engine4_music_playlists` CHANGE `special` `special` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
        }
      }
    }
//     // For site title
//     $select = new Zend_Db_Select($db);
//     $select
//         ->from('engine4_core_settings', array('value'))
//         ->where('name = ?', 'advancedactivity.sitetabtitle');
//     $siteTitle = $select->query()->fetchAll();
// 
//     if (empty($siteTitle)) {
//     $site_title = new Zend_Db_Select($db);
//     $site_title
//             ->from('engine4_core_settings', array('value'))
//             ->where('name = ?', 'core.general.site.title');
//       $coresitetitle = $site_title->query()->fetchAll();
//       $coresitetitle = $coresitetitle[0]['value'] ;
//       $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
//('advancedactivity.sitetabtitle', '$coresitetitle')");
//     }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
    if (!empty($table_exist)) {
      $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
      if (empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actions'")->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query("SHOW COLUMNS FROM `engine4_activity_actions` LIKE 'privacy'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_activity_actions` ADD `privacy` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
      }
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version >= ?', '4.2.3')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
      //ADD NEW COLUMN IN engine4_sitepage_imports TABLE
      $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pages'")->fetch();
      if (!empty($table_exist)) {

        $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'fbpage_id'")->fetch();
        if (empty($column_exist)) {
          $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `fbpage_id` VARCHAR( 32 ) NOT NULL");
        }
      }
    }
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES
('sitebusiness', 'sitebusiness_business', 'Businesses', 1, 12, 1)");
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES
('sitebusiness', 'sitebusiness', 'Businesses', 1, 7, 1)");
    $db->query("UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'video_new'");

    $commentableColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actions` LIKE 'commentable'")->fetch();
    if (empty($commentableColumn)) {
      $db->query("ALTER TABLE `engine4_activity_actions` ADD `commentable` TINYINT( 1 ) NOT NULL DEFAULT '1',
ADD `shareable` TINYINT( 1 ) NOT NULL DEFAULT '1';");
    }
    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
(\'aaf_tagged\', \'advancedactivity\', \'{item:$subject} tagged your {var:$item_type} in a {item:$object:$label}.\', 0, \'\', 1);');
   
    //ADDING THE DEFAULT LINKEDIN ENTRY FOR MEMBER HOME PAGE ACTIVITY FEED.
    
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'advancedactivity')
            ->where('version < ?', '4.2.6');
    $check_advfeedversion = $select->query()->fetchObject();
		if (!empty($check_advfeedversion)){
			$select = new Zend_Db_Select($db);        
			$select->from('engine4_core_content',array('params'))
							->where('name = ?', 'advancedactivity.home-feeds')
							->where('page_id = ?', 4);
			$result = $select->query()->fetchObject();
			if(!empty($result->params)) {
				$params = Zend_Json::decode($result->params);
				
				
				if(isset($params['advancedactivity_tabs'])) { 
				  
		      $params['advancedactivity_tabs'][] = 'linkedin';
					$params = Zend_Json::encode($params);		
					$params = str_replace("'" , "\'", $params);
					$db->query("UPDATE `engine4_core_content` SET `params` = '".$params. "' WHERE `engine4_core_content`.`name` = 'advancedactivity.home-feeds' AND `engine4_core_content`.`page_id` = 4 LIMIT 1;");
				}
			}
    }
  
    $db->query("UPDATE `engine4_activity_actiontypes` SET `body` = '{item:".'$subject'."} shared {item:".'$object'."}''s {var:".'$type'."}.\r\n{body:".'$body'."}' WHERE `engine4_activity_actiontypes`.`type` = 'share' LIMIT 1");
    parent::onInstall();
  }

  private function getVersion() {
    $db = $this->getDb();

    $errorMsg = '';
    $finalModules = $getResultArray = array();
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
        'sitepage' => '4.2.0',
        'sitelike' => '4.2.0',
        'sitealbum' => '4.2.0',
        'suggestion' => '4.2.0',
        'peopleyoumayknow' => '4.2.0',
        'poke' => '4.2.0',
        'list' => '4.2.0',
        'recipe' => '4.2.0',
        'birthday' => '4.2.0',
        'Sitepageadmincontact' => '4.1.8p2',
        'Sitepagealbum' => '4.2.0',
        'Sitepagebadge' => '4.2.0',
        'Sitepagediscussion' => '4.2.0',
        'Sitepagedocument' => '4.2.0',
        'Sitepageevent' => '4.2.0',
        'Sitepageform' => '4.2.0',
        'Sitepageinvite' => '4.2.0',
        'Sitepagelikebox' => '4.1.8',
        'Sitepagemusic' => '4.2.0',
        'Sitepagenote' => '4.2.0',
        'Sitepageoffer' => '4.2.0',
        'Sitepagepoll' => '4.2.0',
        'Sitepagereview' => '4.2.0',
        'Sitepageurl' => '4.2.0',
        'Sitepagevideo' => '4.2.0'
    );
    foreach ($modArray as $key => $value) {
      $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
      if (!empty($isMod) && !empty($isMod['version'])) {
        $isModSupport = strcasecmp($isMod['version'], $value);
        if ($isModSupport < 0) {
          $finalModules['modName'] = $key;
          $finalModules['title'] = $isMod['title'];
          $finalModules['versionRequired'] = $value;
          $finalModules['versionUse'] = $isMod['version'];
          $getResultArray[] = $finalModules;
        }
      }
    }

    foreach ($getResultArray as $modArray) {
      $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Activity Feeds / Wall Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }

}

?>
