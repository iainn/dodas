<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Api_Core extends Core_Api_Abstract {

  protected $_table;

  /**
   *
   * @param $title: String which are require for truncate
   * @return string
   */
  public function seaddonstruncateTitle($title) {
    $tmpBody = strip_tags($title);
    return ( Engine_String::strlen($tmpBody) > 13 ? Engine_String::substr($tmpBody, 0, 15) . '..' : $tmpBody );
  }

  public function getDefault() {
    Engine_Api::_()->getApi('settings', 'core')->getSettings(Zend_Controller_Front::getInstance());
  }

  // return: CDN enabled or not at the site.
  public function isCdn() {
    $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
    $storageversion = $storagemodule->version;

    $db = Engine_Db_Table::getDefaultAdapter();
    // $type_array = $db->query("SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'")->fetch();
    $cdn_path = null;

    if ($storageversion >= '4.1.6') {
      $storageServiceTypeTable = Engine_Api::_()->getDbtable('serviceTypes', 'storage');
      $storageServiceTypeTableName = $storageServiceTypeTable->info('name');

      $storageServiceTable = Engine_Api::_()->getDbtable('services', 'storage');
      $storageServiceTableName = $storageServiceTable->info('name');

      $select = $storageServiceTypeTable->select()
              ->setIntegrityCheck(false)
              ->from($storageServiceTypeTableName, array(null))
              ->join($storageServiceTableName, "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id", array('enabled', 'config', 'default'))
              ->where("$storageServiceTypeTableName.plugin != ?", "Storage_Service_Local")
              ->where("$storageServiceTypeTableName.enabled = ?", 1);

      $storageCheck = $storageServiceTypeTable->fetchRow($select);
      if (!empty($storageCheck)) {
        if ($storageCheck->enabled == 1 && $storageCheck->default == 1) {
          $cdn_path = true;
        }
      }
    }
    return $cdn_path;
  }

  public function setDefaultConstant() {
    // Set Emotions Tag
    $file_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
            . 'modules' . DIRECTORY_SEPARATOR
            . "Seaocore/settings/config/emoticons.php";
    $tags = NULL;
    if (file_exists($file_path)) {
      $tags = include $file_path;
    }
    define('SEA_EMOTIONS_TAG', serialize($tags));

    $showLightboxOptionDisplay = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.lightbox.option.display', array());
    $seaocore_display_lightbox = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.display.lightbox', 1);
    define('SEA_DISPLAY_LIGHTBOX', $seaocore_display_lightbox);
    if (!empty($showLightboxOptionDisplay)) {
      define('SEA_SITEPAGEALBUM_LIGHTBOX', in_array('sitepagealbum', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEPAGEEVENT_LIGHTBOX', in_array('sitepageevent', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSEVENT_LIGHTBOX', in_array('sitebusinessevent', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSALBUM_LIGHTBOX', in_array('sitebusinessalbum', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_LIST_LIGHTBOX', in_array('list', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_RECIPE_LIGHTBOX', in_array('recipe', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEPAGENOTE_LIGHTBOX', in_array('sitepagenote', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSNOTE_LIGHTBOX', in_array('sitebusinessnote', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_LIKE_LIGHTBOX', in_array('sitelike', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
      define('SEA_SITEALBUM_LIGHTBOX', in_array('sitealbum', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
//      define('SEA_SITEREVIEW_LIGHTBOX', in_array('sitereview', $showLightboxOptionDisplay) || $seaocore_display_lightbox);
    } else {
      define('SEA_SITEPAGEALBUM_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSALBUM_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEPAGEEVENT_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSEVENT_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_LIST_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_RECIPE_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEPAGENOTE_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEBUSINESSNOTE_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_LIKE_LIGHTBOX', $seaocore_display_lightbox);
      define('SEA_SITEALBUM_LIGHTBOX', $seaocore_display_lightbox);
//      define('SEA_SITEREVIEW_LIGHTBOX', $seaocore_display_lightbox);
    }

    define('SITEALBUM_ENABLED', Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum'));
    define('SITEACTIVITY_ENABLED', Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity'));

    if ($seaocore_display_lightbox) {
      if ((SITEALBUM_ENABLED || SITEACTIVITY_ENABLED)) {
        define('SEA_ACTIVITYFEED_LIGHTBOX', $seaocore_display_lightbox);
      } else {
        define('SEA_ACTIVITYFEED_LIGHTBOX', 0);
      }
    } else {
      if ((SITEALBUM_ENABLED || SITEACTIVITY_ENABLED)) {
        if (!empty($showLightboxOptionDisplay)) {
          define('SEA_ACTIVITYFEED_LIGHTBOX', in_array('activity', $showLightboxOptionDisplay));
        } else {
          define('SEA_ACTIVITYFEED_LIGHTBOX', 0);
        }
      } else {
        define('SEA_ACTIVITYFEED_LIGHTBOX', 0);
      }
    }

    if (SITEALBUM_ENABLED) {
      if (!empty($showLightboxOptionDisplay)) {
        define("SEA_GROUP_LIGHTBOX", in_array("group", $showLightboxOptionDisplay));
        define("SEA_EVENT_LIGHTBOX", in_array("event", $showLightboxOptionDisplay));
        define("SEA_YNEVENT_LIGHTBOX", in_array("ynevent", $showLightboxOptionDisplay));
        define("SEA_ADVGROUP_LIGHTBOX", in_array("advgroup", $showLightboxOptionDisplay));
      } else {
        define("SEA_GROUP_LIGHTBOX", 0);
        define("SEA_EVENT_LIGHTBOX", 0);
        define("SEA_YNEVENT_LIGHTBOX", 0);
        define("SEA_ADVGROUP_LIGHTBOX", 0);
      }
    }

    $enableSubModules = array();
    $includeModules = array("sitepagealbum" => "Directory / Pages - Photo Albums Extension", "sitepagenote" => "Directory / Pages - Notes Extension","sitepageevent" => "Directory / Pages - Events Extension", "list" => "Listing", "recipe" => "Recipe", "sitelike" => "Likes Plugin and Widgets", "sitealbum" => "Advanced Photo Albums", "sitebusinessalbum" => "Directory / Businesses - Photo Albums Extension", "sitebusinessnote" => "Directory / Businesses - Notes Extension","sitebusinessevent" => "Directory / Businesses - Events Extension", 'sitereview' => 'Review Plugin');

    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
    if (!empty($enableModules)) {
      define('SEA_PHOTOLIGHTBOX_DOWNLOAD', Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.download', 1));
      define('SEA_PHOTOLIGHTBOX_REPORT', Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.report', 1));
      define('SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO', Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.makeprofile', 1));
      define('SEA_PHOTOLIGHTBOX_SHARE', Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.share', 1));
    } else {
      define('SEA_PHOTOLIGHTBOX_DOWNLOAD', 0);
      define('SEA_PHOTOLIGHTBOX_REPORT', 0);
      define('SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO', 0);
      define('SEA_PHOTOLIGHTBOX_SHARE', 0);
    }
  }

  /**
   * Returns array of "Mutual Friend" between "$friend_id" and "viewer_id".
   *
   * @param $friend_id: Find out mutual friend between "Pass friend id" and "Loggden user id".
   * @return Array.
   */
  public function getMutualFriend($friend_id, $LIMIT = null) {

    $mutualFriendArray = array();

    //GET THE VIEWER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $memberTable = Engine_Api::_()->getDbtable('membership', 'user');
    $memberTableName = $memberTable->info('name');

    $select = $memberTable->select()
            ->setIntegrityCheck(false)
            ->from($memberTableName, array('user_id'))
            ->join($memberTableName, "`{$memberTableName}`.`user_id`=`{$memberTableName}_2`.user_id", null)
            ->where("`{$memberTableName}`.resource_id = ?", $friend_id) // FRIEND ID.
            ->where("`{$memberTableName}_2`.resource_id = ?", $viewer_id) // VIEWER ID.
            ->where("`{$memberTableName}`.active = ?", 1)
            ->where("`{$memberTableName}_2`.active = ?", 1);
    if (!empty($LIMIT)) {
      $select->limit($LIMIT);
    }
    return Zend_Paginator::factory($select);
//     if (!empty($fetch_mutual_friend)) {
//       foreach ($fetch_mutual_friend as $mutual_friend_id) {
//         $mutualFriendArray[] = $mutual_friend_id['user_id'];
//       }
//     }
  }

  public function getCategory($resource_type, $resource) {


    // RETURN CATEGORY FOR MAGENTO PLUGIN.
    if (strstr($resource_type, 'siteestore')) {
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteestore')) {
        $categorysArray = $resource->category;
        $categorysArray = @unserialize($categorysArray);
        if (!empty($categorysArray) && !empty($categorysArray[0])) {
          $category = $categorysArray[0];
          return $category;
        }
      }
    }

    //Start Work for faq plugin.
    if ($resource_type == 'sitefaq_faq' && !empty($resource['category_id'])) {
      $first_category_id_array = explode('["', $resource['category_id']);
      $first_category_id_array = explode('"', $first_category_id_array[1]);
      $resource['category_id'] = $first_category_id_array[0];
    }
    //End Work for faq plugin.

    if (empty($resource['category_id'])) {
      return;
    }

    switch ($resource_type) {
      case 'event':
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('event')) {
          $Table = Engine_Api::_()->getItemTable('ynevent_category');
        } else {
          $Table = Engine_Api::_()->getDbtable('categories', $resource_type);
        }
        $title = 'title';
        break;
      case 'group':
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')) {
          $Table = Engine_Api::_()->getDbtable('categories', 'advgroup');
        } else {
          $Table = Engine_Api::_()->getDbtable('categories', $resource_type);
        }
        $title = 'title';
        break;
      case 'forum':
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('forum')) {
          $Table = Engine_Api::_()->getItemTable('ynforum_category');
        } else {
          $Table = Engine_Api::_()->getDbtable('categories', $resource_type);
        }
        $title = 'title';
        break;
      case 'video':
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {
          $Table = Engine_Api::_()->getItemTable('video_category');
        } else {
          $Table = Engine_Api::_()->getDbtable('categories', $resource_type);
        }
        $title = 'category_name';
        break;
      case 'classified':
      case 'recipe':
      case 'document':
        $Table = Engine_Api::_()->getDbtable('categories', $resource_type);
        $title = 'category_name';
        break;
      case 'album':
      case 'blog':
        $Table = Engine_Api::_()->getItemTable($resource_type . '_category');
        $title = 'category_name';
        break;
      case 'list_listing':
        $Table = Engine_Api::_()->getDbtable('categories', 'list');
        $title = 'category_name';
        break;
      case 'sitepage_page':
        $Table = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $title = 'category_name';
        break;
      case 'sitebusiness_business':
        $Table = Engine_Api::_()->getDbtable('categories', 'sitebusiness');
        $title = 'category_name';
        break;
      case 'sitefaq_faq':
        $Table = Engine_Api::_()->getDbtable('categories', 'sitefaq');
        $title = 'category_name';
        break;
      default:
        return;
    }
    return $Table->select()->from($Table, new Zend_Db_Expr($title))
                    ->where('category_id = ?', $resource['category_id'])->limit(1)->query()->fetchColumn();
  }

  /**
   * Gets a url slug for string
   *
   * @return string The slug
   */
  public function getSlug($str, $limit = 64) {
    
    if (strlen($str) > $limit) {
      $str = Engine_String::substr($str, 0, $limit) . '...';
    }
    
    $slugString = $str;

		//CASE 1:
    $search = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    $str = str_replace($search, $replace, $str);

    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $slugString);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');

		//CASE 2:
		if(empty($str) || $str == '-') {
      setlocale(LC_CTYPE, 'pl_PL.utf8');
      $str = iconv('UTF-8', 'ASCII//TRANSLIT', $slugString);
      $str = strtolower($str);
      $str = strtr($str, array('&' => '-', '"' => '-', '&' . '#039;' => '-', '<' => '-', '>' => '-', '\'' => '-'));
      $str = preg_replace('/^[^a-z0-9]{0,}(.*?)[^a-z0-9]{0,}$/si', '\\1', $str);
      $str = preg_replace('/[^a-z0-9\-]/', '-', $str);
      $str = preg_replace('/[\-]{2,}/', '-', $str);

			//CASE 3:
      if(empty($str) || $str == '-') {

				$cyrillicArray = array(
					"Є"=>"YE","І"=>"I", "Ї"=>"YI", "Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
					"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
					"Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
					"З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
					"М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
					"С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
					"Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
					"Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
					"а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
					"е"=>"e","ё"=>"yo","ж"=>"zh",
					"з"=>"z","и"=>"i", "ї"=>"yi", "й"=>"j","к"=>"k","л"=>"l",
					"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
					"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
					"ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
					"ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","«"=>"","»"=>"","—"=>"-"
				 );

				 $str = strtr($slugString, $cyrillicArray);
				 $str = preg_replace('/\W+/', '-', $str);
				 $str = strtolower(trim($str, '-'));
			}	
		}

    if (!$str) {
      $str = '-';
    }
    
    return $str;
  }

  /**
   * Returns true / false if "Friend Id" is the friend of "Loggden User"
   *
   * @param $friend_id: Friend Id,
   * @return true or false
   */
  public function isMember($friend_id) {

    //GET THE VIEWER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isFriend = false;

    //FETCH FRIEND ID FROM DATABASE.
    $memberTable = Engine_Api::_()->getDbtable('membership', 'user');
    $memberTableName = $memberTable->info('name');

    $select = $memberTable->select()
            ->where($memberTableName . '.active = ?', 1)
            ->where($memberTableName . '.resource_id = ?', $friend_id)
            ->where($memberTableName . '.user_id = ?', $viewer_id);

    $fetch = $select->query()->fetchAll();
    if (!empty($fetch)) {
      $isFriend = true;
    }
    return $isFriend;
  }

  public function canSendUserMessage($subject) {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
      return false;
    }
    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
      return false;
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($messageAuth == 'none') {
      return false;
    } else if ($messageAuth == 'friends') {
      // Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($subject);
      } else {
        $friendship_status = $subject->membership()->getRow($viewer);
      }

      if (!$friendship_status || $friendship_status->active == 0) {
        return false;
      }
    }
    return true;
  }

  public function baseOnContentOwner(User_Model_User $viewer, Core_Model_Item_Abstract $item) {

    $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
    if ($item->getType() == 'sitepage_page') {
      $advancedactivityEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
      if ($advancedactivityEnable && $settingsCoreApi->sitepage_feed_type && $item->isOwner($viewer)) {
        return true;
      }
    } elseif ($item->getType() == 'sitebusiness_business') {
      $advancedactivityEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
      if ($advancedactivityEnable && $settingsCoreApi->sitebusiness_feed_type && $item->isOwner($viewer)) {
        return true;
      }
    }
    return false;
  }

  public function isLessThan420ActivityModule() {
    $activityModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('activity');
    $activityModuleVersion = $activityModule->version;
    if ($activityModuleVersion < '4.1.8') {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Added Widget On Page
   *
   * @return bool
   */
  public function hasAddedWidgetOnPage($pageName, $widgetName, $params =array()) {
    $isCoreActivtyFeedWidget = false;

    $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pagesTableName = $pagesTable->info('name');
    $contentsTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentsTableName = $contentsTable->info('name');

    $select = $contentsTable->select()
            ->setIntegrityCheck(false)
            ->from($contentsTableName, array($contentsTableName . '.name'))
            ->join($pagesTableName, "`{$pagesTableName}`.page_id = `{$contentsTableName}`.page_id  ", null)
            ->where($pagesTableName . '.name = ?', $pageName)
            ->where($contentsTableName . '.name = ?', $widgetName);
    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $isCoreActivtyFeedWidget = true;
    return $isCoreActivtyFeedWidget;
  }

  /**
   * Get Truncation String
   *
   * @param string $text
   * @param int $limit
   * @return truncate string
   */
  public function seaocoreTruncateText($string, $limit) {

    //IF LIMIT IS EMPTY
    if (empty($limit)) {
      $limit = 16;
    }

    //RETURN TRUNCATED STRING
    $string = strip_tags($string);
    return ( Engine_String::strlen($string) > $limit ? Engine_String::substr($string, 0, ($limit - 3)) . '...' : $string );
  }

  public function canShowSuggestFriendLink($module_name) {
    $flage = false;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $sitevideo_view_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.view.type', null);
    $sitevideo_core_str = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.core.str', null);
    if (!empty($sitevideo_view_type) && ($sitevideo_view_type != $sitevideo_core_str)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideoview.view.queue', 0);
    }

    if (!empty($viewer_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')) {

      if (in_array($module_name, array('sitepagevideo', 'sitebusinessvideo'))) {
        if ($module_name == 'sitepagevideo') {
          $module_name = 'sitepage';
          $link = 'video_sugg_link';
        } elseif ($module_name == 'sitebusinessvideo') {
          $module_name = 'sitebusiness';
          $link = 'video_sugg_link';
        }
        $getModObj = Engine_Api::_()->suggestion()->getModSettings($module_name, $link);
        if (!empty($getModObj))
          $flage = true;
      } else {
        $getModObj = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getMod($module_name);
        $getModObj = !empty($getModObj) ? $getModObj[0] : null;
        if (!empty($getModObj) && !empty($getModObj['enabled'])) {
          $flage = true;
        }
      }
    }

    return $flage;
  }

  /**
   * Return Profile Map Bounds Params
   *
   * @param array $checkinMarkers
   */
  public function getProfileMapBounds($checkinMarkers) {
    $minLatitude = 200;
    $maxLatitude = -200;
    $minLongitude = 200;
    $maxLongitude = -200;

    if (count($checkinMarkers) == 0) {
      return array();
    } elseif (count($checkinMarkers) == 1) {
      $checkinMarker = $checkinMarkers[0];
      $minLatitude = $maxLatitude = $checkinMarker['latitude'];
      $minLongitude = $maxLongitude = $checkinMarker['longitude'];
    } else {
      foreach ($checkinMarkers as $checkinMarker) {
        if (empty($checkinMarker['longitude']) || empty($checkinMarker['latitude']))
          continue;

        if ($checkinMarker['longitude'] <= $minLongitude) {
          $minLongitude = $checkinMarker['longitude'];
        }

        if ($checkinMarker['longitude'] >= $maxLongitude) {
          $maxLongitude = $checkinMarker['longitude'];
        }

        if ($checkinMarker['latitude'] <= $minLatitude) {
          $minLatitude = $checkinMarker['latitude'];
        }

        if ($checkinMarker['latitude'] >= $maxLatitude) {
          $maxLatitude = $checkinMarker['latitude'];
        }
      }
    }

    if ($minLatitude == $maxLatitude && $minLongitude == $maxLongitude) {
      $minLatitude -= 0.0009;
      $maxLatitude += 0.0009;
      $minLongitude -= 0.0009;
      $maxLongitude += 0.0009;
    }

    $centerLat = (float) ($minLatitude + $maxLatitude) / 2;
    $centerLng = (float) ($minLongitude + $maxLongitude) / 2;

    return array(
        'min_lat' => $minLatitude,
        'max_lat' => $maxLatitude,
        'min_lng' => $minLongitude,
        'max_lng' => $maxLongitude,
        'center_lat' => $centerLat,
        'center_lng' => $centerLng
    );
  }

  public function isMobile() {
    $mobileEnable = false;
    $request = Zend_Controller_Front::getInstance()->getRequest();
    // Code for Mobile Compatibilty Plugins. We are not excuting the our plugin code in case of mode='mobile' or mode === 'touch'.
    $session = new Zend_Session_Namespace('standard-mobile-mode');
    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
      // Reference from "Detect mobile browser (smartphone)" and URL : http://www.serveradminblog.com/2011/01/detect-mobile-browser-smartphone/
      $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
      if
      (
              preg_match('/imageuploader|android|blackberry|compal|fennec|hiptop|iemobile/i', $useragent) ||
              preg_match('/ip(hone|od)|kindle|lge|maemo|midp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\//i', $useragent) ||
              preg_match('/pocket|psp|symbian|treo|up\.(browser|link)|vodafone|windows (ce|phone)|xda/i', $useragent)
      )
        $mobileEnable = true;
      if (preg_match('/imageuploader|android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
        $mobileEnable = true;
    }
    $mobile = false;
    if (!$mobileEnable && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi')) {
      $mobile = $request->getParam("mobile");
      $session = new Zend_Session_Namespace('mobile');

      if ($mobile == "1") {
        $mobileEnable = true;
        $session->mobile = true;
      } elseif ($mobile == "0") {
        $mobileEnable = false;
        $session->mobile = false;
      } else {
        if (isset($session->mobile)) {
          $mobileEnable = (bool) $session->mobile;
        } else {
          // CHECK TO SEE IF MOBILE
          if (Engine_Api::_()->mobi()->isMobile()) {
            $mobileEnable = true;
            $session->mobile = true;
          } else {
            $mobileEnable = false;
            $session->mobile = false;
          }
        }
      }
    }
    return $mobileEnable;
  }

  /**
   * Get the tags list in autosuggest(autosuggest list will have only the tags of resource_type)
   *
   * @param string $text
   * @param int $limit
   */
  public function getTagsByText($text = null, $limit = 10, $resourceType = '') {
    //GET TAG TABLE
    $tableTags = Engine_Api::_()->getDbtable('tags', 'core')->getTagTable();
    $tableTagsName = $tableTags->info('name');

    //GET TAG MAP TABLE
    $tableTagMaps = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tableTagMapsName = $tableTagMaps->info('name');

    //MAKE QUERY
    $select = $tableTags->select()
            ->setIntegrityCheck(false)
            ->from($tableTagsName)
            ->join($tableTagMapsName, "$tableTagsName.tag_id = $tableTagMapsName.tag_id", null);

    if (!empty($resourceType)) {
      $select->where("$tableTagMapsName.resource_type = ?", "$resourceType");
    }

    if ($text) {
      $select->where('text LIKE ?', '%' . $text . '%');
    }

    $select->order('text ASC')
            ->group("$tableTagsName.tag_id")
            ->limit($limit);

    return $tableTags->fetchAll($select);
  }

  /**
   * Return GOOGLE MAP API KEY
   */
  public function getGoogleMapApiKey() {

    //GET API KEY
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key', '');
  }

  /**
   * Return $location 
   *
   * @param $subject
   */
  public function getCustomFieldLocation($subject) {
    $resource_type = $subject->getType();
    $location = "";
    //SET CUSTOM FILLED ARRAY
    $customFilledLocationArray = array("classified", "list_listing", "recipe");
    //GET LOCATION FOR CUSTOM FILLED
    if (in_array($resource_type, $customFilledLocationArray)) {

      //GET RESOURCE TABLE
      $resourceTable = Engine_Api::_()->getItemTable($resource_type);

      //GET RESOURCE TABLE NAME
      $resourceTableName = $resourceTable->info('name');

      //GET PRIMARY KEY NAME
      $primary = current($resourceTable->info("primary"));

      //GET FIELD VALUE TABLE
      $valueTable = Engine_Api::_()->fields()->getTable($resource_type, 'values');

      //GET FIELD VALUE TABLE NAME
      $valueTableName = $valueTable->info('name');

      //GET FIELD META TABLE NAME
      $metaName = Engine_Api::_()->fields()->getTable($resource_type, 'meta')->info('name');

      //GET LOCATION
      $location = $valueTable->select()
              ->setIntegrityCheck(false)
              ->from($valueTableName, array('value'))
              ->join($metaName, $metaName . '.field_id = ' . $valueTableName . '.field_id', null)
              ->join($resourceTableName, $resourceTableName . '.' . $primary . '=' . $valueTableName . '.item_id', null)
              ->where($valueTableName . '.item_id = ?', $subject->getIdentity())
              ->where($metaName . '.type = ?', 'Location')
              ->order($metaName . '.field_id')
              ->query()
              ->fetchColumn();
    }

    //GET LOCATION FOR NOT CUSTOM FILED
    if (isset($subject->location) && !empty($subject->location)) {
      $location = $subject->location;
    }
    return $location;
  }

  public function hasLike($RESOURCE_ID, $viewer_id) {
    if (empty($RESOURCE_ID) || empty($viewer_id))
      return false;

    $sub_status_table = Engine_Api::_()->getItemTable('core_like');

    $sub_status_select = $sub_status_table->select()
            ->where('resource_type = ?', 'sitepage_page')
            ->where('resource_id = ?', $RESOURCE_ID)
            ->where('poster_id = ?', $viewer_id);
    $fetch_sub = $sub_status_table->fetchRow($sub_status_select);
    if (!empty($fetch_sub))
      return true;
    else
      return false;
  }

  /**
   * check the item is like or not.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @return results
   */
  public function checkAvailability($RESOURCE_TYPE, $RESOURCE_ID) {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer();
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $sub_status_select = $likeTable->select()
            ->from($likeTableName, array('like_id'))
            ->where('resource_type = ?', $RESOURCE_TYPE)
            ->where('resource_id = ?', $RESOURCE_ID)
            ->where('poster_type =?', $viewer->getType())
            ->where('poster_id =?', $viewer->getIdentity())
            ->limit(1);
    return $sub_status_select->query()->fetchAll();
  }

  /**
   * Get member online
   *
   * @param int $user_id
   * @return int $flag;
   */
  public function isOnline($user_id) {

    // Get online users
    $table = Engine_Api::_()->getItemTable('user');
    $onlineTable = Engine_Api::_()->getDbtable('online', 'user');

    $tableName = $table->info('name');
    $onlineTableName = $onlineTable->info('name');

    $select = $table->select()
            //->from($onlineTableName, null)
            //->joinLeft($tableName, $onlineTable.'.user_id = '.$tableName.'.user_id', null)
            ->from($tableName)
            ->joinRight($onlineTableName, $onlineTableName . '.user_id = ' . $tableName . '.user_id', null)
            //->where($onlineTableName.'.user_id > ?', 0)
            ->where($onlineTableName . '.user_id = ?', $user_id)
            //->where($onlineTableName.'.active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
            ->where($tableName . '.search = ?', 1)
            ->where($tableName . '.enabled = ?', 1)
            ->order($onlineTableName . '.active DESC')
            ->group($onlineTableName . '.user_id');
    $row = $table->fetchRow($select);

    $flag = false;
    if (!empty($row)) {
      $flag = true;
    }
    return $flag;
  }

  public function getCurrentVersion($maxVersion, $name) {

    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    $version = $moduleTable->select()
            ->from($moduleTable->info('name'), 'version')
            ->where('name = ?', $name)
            ->where('version >= ?', $maxVersion)
            ->query()
            ->fetchColumn();
    return $version;
  }

}
