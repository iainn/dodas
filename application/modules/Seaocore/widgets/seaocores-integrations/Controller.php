<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Widget_SeaocoresIntegrationsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Zend_Feed required DOMDocument
    // @todo add to sanity check
    if( !class_exists('DOMDocument', false) ) {
      $this->view->badPhpVersion = true;
      return;
      //return $this->setNoRender();
    }
		if( !empty($_POST['level_id']) ) {
			$show_table = $_POST['level_id'];
		} else {
			$show_table = 4;
		}
		$this->view->show_table = $show_table;
    $rss = Zend_Feed::import('http://www.socialengineaddons.com/feed.xml');
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );

    $integration_plugin_name = array('advancedactivity' => 1, 'communityad' => 2, 'facebookse'  => 3, 'facebooksefeed'  => 4, 'suggestion'  => 5, 'sitefaq'  => 7, 'sitetagcheckin'  => 8, 'sitevideoview'  => 9, 'sitelike'  => 10, 'advancedslideshow' => 11);

    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {

			$product_type = $item->ptype();
			$modules_info = $this->module_info($product_type);
			
			if (!array_key_exists($modules_info['mod_name'], $integration_plugin_name)) {
				continue;
			}
		
			
      if( strstr($item->title(), "Directory / Pages Plugin") ) {
        $tempBussImages = explode("::", $item->image());
      }

			if( $show_table == 2 && !empty($modules_info['version']) ) {
				$license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
				$plugin_info['title'] = $item->title();
				$plugin_info['product_version'] = $item->version();
				$plugin_info['key'] = $license_key;
				$plugin_info['link'] = $item->link();
				$plugin_info['price'] = $item->price();
				$plugin_info['socialengine_url'] = $item->socialengine_url();
				if( !empty($modules_info['status']) ) {			
					$plugin_info['running_version'] = $modules_info['version'];
				} else {
					$plugin_info['running_version'] = 0;
				}
				$product_images = explode("::", $item->image());
				$plugin_info['image'] = $product_images;
				$plugin_info['description'] = $item->description();
				$channel['items'][$integration_plugin_name[$modules_info['mod_name']]] = $plugin_info;
			} else if( $show_table == 3 && empty($modules_info['version']) ) {
				$license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
				$plugin_info['title'] = $item->title();
				$plugin_info['product_version'] = $item->version();
				$plugin_info['key'] = $license_key;
				$plugin_info['link'] = $item->link();
				$plugin_info['price'] = $item->price();
				$plugin_info['socialengine_url'] = $item->socialengine_url();
				if( !empty($modules_info['status']) ) {				
					$plugin_info['running_version'] = $modules_info['version'];
				} else {
					$plugin_info['running_version'] = 0;
				}
				$product_images = explode("::", $item->image());
				$plugin_info['image'] = $product_images;
				$plugin_info['description'] = $item->description();
				$channel['items'][$integration_plugin_name[$modules_info['mod_name']]] = $plugin_info;
			} else  if( $show_table == 1 ){
				$license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
				$plugin_info['title'] = $item->title();
				$plugin_info['product_version'] = $item->version();
				$plugin_info['key'] = $license_key;
				$plugin_info['link'] = $item->link();
				$plugin_info['price'] = $item->price();
				$plugin_info['socialengine_url'] = $item->socialengine_url();
				if( !empty($modules_info['status']) ) {				
					$plugin_info['running_version'] = $modules_info['version'];
				} else {
					$plugin_info['running_version'] = 0;
				}
				$product_images = explode("::", $item->image());
				$plugin_info['image'] = $product_images;
				$plugin_info['description'] = $item->description();
				$channel['items'][$integration_plugin_name[$modules_info['mod_name']]] = $plugin_info; 
			}
			else  if( ($show_table == 4) ) {

				$front = Zend_Controller_Front::getInstance();
				$module = $front->getRequest()->getModuleName();

// 				if (strstr($product_type, $module)) {
// 					$pluginIntegration = explode(",", $item->integration());
// 				}
				
				$license_key = Engine_Api::_()->getApi('settings', 'core')->getSetting($modules_info['key']);
				$plugin_info['title'] = $item->title();
				$plugin_info['product_version'] = $item->version();
				$plugin_info['key'] = $license_key;
				$plugin_info['link'] = $item->link();
				$plugin_info['price'] = $item->price();
				$plugin_info['socialengine_url'] = $item->socialengine_url();
				if( !empty($modules_info['status']) ) {
					$plugin_info['running_version'] = $modules_info['version'];
				} else {
					$plugin_info['running_version'] = 0;
				}
				$product_images = explode("::", $item->image());
				$plugin_info['image'] = $product_images;
				$plugin_info['description'] = $item->description();
				
				$temp[$product_type] = $plugin_info;
				
				 $channel['items'][$integration_plugin_name[$modules_info['mod_name']]] = $plugin_info;
			}
    }

// 		if ($show_table == 4 && !empty($pluginIntegration)) {
// 			foreach($pluginIntegration as $plugin)  {
// 				$channel['items'][] = $temp[$plugin];
// 			}
// 		}

    if( !empty($tempBussImages) ) {
      $flag = 0;
      ksort($channel['items']);
      foreach( $channel['items'] as $items ) {
        if( strstr($items['title'], "Directory / Businesses Plugin") ) {
          $channel['items'][$flag]['image'] = $tempBussImages;
          break;
        }
        $flag++;
      }
    }

    $this->view->channel = $channel['items'];
  }
	public function module_info($product_type)
	{
		switch($product_type) {
			case 'userconnection':
				$name = 'userconnection';
				$key_firld = 'user.licensekey';
			break;
			case 'feedbacks':
				$name = 'feedback';
				$key_firld = 'feedback.license_key';
			break;
			case 'suggestion':
				$name = 'suggestion';
				$key_firld = 'suggestion.controllersettings';
			break;
			case 'peopleyoumayknow':
				$name = 'peopleyoumayknow';
				$key_firld = 'pymk.controllersettings';
			break;
			case 'siteslideshow':
				$name = 'siteslideshow';
				$key_firld = 'siteslideshow.controllersettings';
			break;
			case 'mapprofiletypelevel':
				$name = 'mapprofiletypelevel';
				$key_firld = 'mapprofiletypelevel.controllersettings';
			break;
			case 'documentsv4':
				$name = 'document';
				$key_firld = 'document.controllersettings';
			break;
			case 'groupdocumentsv4':
				$name = 'groupdocument';
				$key_firld = 'groupdocument.controllersettings';
			break;
			case 'backup':
				$name = 'dbbackup';
				$key_firld = 'dbbackup.controllersettings';
			break;
			case 'mcard':
				$name = 'mcard';
				$key_firld = 'mcard.controllersettings';
			break;
			case 'like':
				$name = 'sitelike';
				$key_firld = 'sitelike.controllersettings';
			break;
			case 'seaddons-core':
				$name = 'seaocore';
				$key_firld = '';
			break;
			default:
				$name = $product_type;
				$key_firld = $product_type . 'lsettings';
			break;				
		}
		$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
		$moduleName = $moduleTable->info('name');
		$select = $moduleTable->select()
			->setIntegrityCheck(false)
			->from($moduleName, array('version'))
			->where('name = ?', $name)
			->limit(1);
		$module_info = $select->query()->fetchAll();
		if ( !empty($module_info) ) {
			$module_info_array['version'] = $module_info[0]['version'];
			$module_info_array['status'] = 1;
		} else {
			$module_info_array['status'] = 0;
		}
		$module_info_array['key'] = $key_firld;
		$module_info_array['mod_name'] = $name;
		return $module_info_array;
	}
}
