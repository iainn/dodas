<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GeoLocation.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_GeoLocation extends Core_Api_Abstract {

  public function getMaxmindCurrentLocation() {

     $license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.maxmind.key'); 
    if (empty($license_key))
      return;
    $ipaddress =  '113.193.239.124'; //$_SERVER["REMOTE_ADDR"];
    $query = "http://geoip3.maxmind.com/f?l=" . $license_key . "&i=" . $ipaddress;
    $url = parse_url($query);
    $host = $url["host"];
    $path = $url["path"] . "?" . $url["query"];
    $timeout = 1;
    $fp = fsockopen($host, 80, $errno, $errstr, $timeout)
        or die('Can not open connection to server.');
    if ($fp) {
      $buf = null;
      fputs($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
      while (!feof($fp)) {
        $buf .= fgets($fp, 128);
      }
      $lines = explode("\n", $buf);
      $data = $lines[count($lines) - 1];
      fclose($fp);
    } else {
      # enter error handing code here
    }
    $data = explode(',', $data);
    $location = array();
		$session = new Zend_Session_Namespace('Current_location');
		$session->country=$location['country'] = $data['0'];
    $session->city=$location['city'] = $data['2'];
    $session->latitude=$location['latitude'] = $data['4'];
    $session->longitude=$location['longitude'] = $data['5'];
    return $location;
  }

  public function getMaxmindGeoLiteCountry() {
    $ipaddress = $_SERVER["REMOTE_ADDR"];
    $ipaddress_digites = explode('.', $ipaddress);
    $ipnum = (16777216 * $ipaddress_digites[0]) + (65536 * $ipaddress_digites[1]) + (256 * $ipaddress_digites[2]) + ($ipaddress_digites[3]);

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    $select
        ->from('engine4_seaocore_geolitecity_blocks')
        ->where('ip_start <= ?', $ipnum)
        ->where('ip_end >= ?', $ipnum)
        ->limit(1);
    $result = $select->query()->fetchObject();

    if (empty($result))
      return;
    $select = new Zend_Db_Select($db);

    $select
        ->from('engine4_seaocore_geolitecity_location')
        ->where('locId = ?', $result->location_id)
        ->limit(1);
    $resultlocation = $select->query()->fetchObject();

    if (empty($resultlocation))
      return;
    $location = array();
		$session = new Zend_Session_Namespace('Current_location');
		$session->country=$location['country'] =  $resultlocation->country;
    $session->city=$location['city'] =  $resultlocation->city;
    $session->latitude=$location['latitude'] = $resultlocation->latitude;
    $session->longitude=$location['longitude'] = $resultlocation->longitude;

    return $location;
  }

}