<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeoLocationsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminGeoLocationsController extends Core_Controller_Action_Admin {

 
  public function importDataAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $basePath = APPLICATION_PATH . "/temporary/GeoLiteCity";
    $fBlocks = $basePath . '/GeoLiteCity-Blocks.csv';
    $this->view->error = null;
    if (!file_exists($fBlocks)) {
      $this->view->error = $error = "The file is not here.<br />" . $fBlocks;
      return;
    }

    $fLocation = $basePath . '/GeoLiteCity-Location.csv';
    $this->view->error = null;
    if (!file_exists($fLocation)) {
      $this->view->error = $error = "The file is not here.<br />" . $fLocation;
      return;
    }

    if ($this->getRequest()->isPost()) {

      ini_set("memory_limit", "1024M");
      set_time_limit(0);

      $i = 0;

      $handle = @fopen($fBlocks, "r");
      if ($handle) {
        $insert = "INSERT IGNORE INTO `engine4_seaocore_geolitecity_blocks` ( `ip_start`, `ip_end`, `location_id`) VALUES";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $values = array();
        $turncateTableSql = "TRUNCATE TABLE `engine4_seaocore_geolitecity_blocks`";

        $db->query($turncateTableSql);
        while (($buffer = fgets($handle, 4096)) !== false) {
          $values[] = $buffer;
          $i++;
          if ($i == 20000) {
            $str = "(" . join("),(", $values) . ")";
            $sql = $insert . $str;
            $db->query($sql);
            $values = array();
            $i = 0;
          }
        }
        if (!feof($handle)) {
          echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
      }


      $i = 0;

      $handle = @fopen($fLocation, "r");
      if ($handle) {
        $insert = "INSERT IGNORE INTO `engine4_seaocore_geolitecity_location` (  `locId` ,`country` ,`region` ,`city` ,`postalCode` ,`latitude` ,`longitude` ,`metroCode` ,`areaCode`) VALUES ";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $values = array();
        $turncateTableSql = "TRUNCATE TABLE `engine4_seaocore_geolitecity_location`";

        $db->query($turncateTableSql);
        fgets($handle, 4096);
        fgets($handle, 4096);
        while (($buffer = fgets($handle, 4096)) !== false) {
          $buffer = explode("\n", $buffer);
          $buffer = explode(",", $buffer[0]);

          if (empty($buffer[7])) {
            $buffer[7] = 0;
          }
          if (empty($buffer[8])) {
            $buffer[8] = 0;
          }
          $buffer = join(",", $buffer);
          $values[] = $buffer;
          $i++;
          if ($i == 50) {
            $str = "(" . join("),(", $values) . ")";
           $sql = $insert . $str; 
            
            $db->query($sql);
            $values = array();
            $i = 0;
          }
        }
        if (!feof($handle)) {
          echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
      }


      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('Suucessfully import data'))
      ));
    }
    $this->renderScript('admin-geo-locations/import-data.tpl');
  }

}
?>