<?php

class Seaocore_Api_Facebook_Facebookinvite extends Core_Api_Abstract {

  protected $_api;

  public function getFBInstance() {

    return Engine_Api::_()->getApi('Facebook_Facebookinvite', 'seaocore')->getApi();
  }

  public function find_friends_added_thisapp() {
    $session = new Zend_Session_Namespace();

    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    try {

      $fbuid = $_SESSION['facebook_uid'];

      $fbFriends = $facebook->api('/me/friends');

      if (isset($fbFriends['data']) && !empty($fbFriends['data'])) {
        foreach ($fbFriends['data'] as $fb_uid) {
          $fqlResult [] = $fb_uid ['id'];
        }

        $all_CountFBFriends = count($fqlResult);
        //NOW CHECKING IF ANY OF THE USER HAS ALREADY JOINED THE SITE AND INTEGRATED THERE FACEBOOK ACCOUNT WITH THEIR SITE ACCOUNT:

        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebookTable_select = $facebookTable->select()
                ->from($facebookTable->info('name'), 'facebook_uid')
                ->where('facebook_uid IN(?) ', (array) $fqlResult)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        //CHECKING IF ANY OF THE FACEBOOK USER HAS JOINED THE SITE:

        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
        $inviteTable_select = $inviteTable->select()
                ->from($inviteTable->info('name'), 'social_profileid')
                ->where('social_profileid IN(?) ', (array) $fqlResult)
                ->where('new_user_id <>?', 0)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $joined_FBusers = array_unique(array_merge($inviteTable_select, $facebookTable_select));

        $all_CountFBFriendsonSite = count($joined_FBusers);
        $result['exclude_ids'] = '';
        $result ['showinviterpage'] = true;
        if (!empty($joined_FBusers)) {
          $result['exclude_ids'] = join(",", $joined_FBusers);
        }

        if ($all_CountFBFriendsonSite == $all_CountFBFriends)
          $result ['showinviterpage'] = false;

        return $result;
      }
    } catch (Exception $e) {
      
    }
//     } else return;
  }

  public function facebook_invitefriend($var_fbconnect_invitef_redirect, $pageinvite_id = '', $moduletype = '') {
    $var_site_name = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    $sitename = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    if (empty($sitename)) {
      $sitename = Zend_Registry::get('Zend_Translate')->_('My Community');
    }
    $actiontxt = Zend_Registry::get('Zend_Translate')->_('Come and join me on this %1s at: %2s . There is a lot to do here!');
    $invite_mess_default = sprintf($actiontxt, $sitename, $var_site_name);
    $excluded_friends = Seaocore_Api_Facebook_Facebookinvite::find_friends_added_thisapp();
    if (!$excluded_friends['showinviterpage'])
    //return;
      $invite_mess = substr(Engine_Api::_()->getApi('settings', 'core')->getSetting('fbfriend.siteinvite'), 0, 240);

    if (empty($invite_mess))
      $invite_mess = substr(Engine_Api::_()->getApi('settings', 'core')->invite_message, 0, 240);

    if (empty($invite_mess))
      $invite_mess = $invite_mess_default;


    if ($moduletype == 'sitepage' || $moduletype == 'sitebusiness')
      $var_fbconnect_invitef_content = Seaocore_Api_Facebook_Facebookinvite::getInviteMessage($pageinvite_id, $moduletype);

    else
      $var_fbconnect_invitef_content = Seaocore_Api_Facebook_Facebookinvite::parseString($invite_mess);

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {

      $refuser = 'facebook';
    } else {
      $refuser = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    $callbackURL = $var_site_name . '/seaocore/auth/social-Signup/?type=facebook&refuser=' . $refuser;

    $content = Seaocore_Api_Facebook_Facebookinvite::parseString($var_fbconnect_invitef_content);

    return array('exclude_ids' => $excluded_friends['exclude_ids'], 'message' => $content);
  }

  public function parseString($content) {
    $ret_str = '';
    //$str = trim($content);
    $str = str_replace('"', "'", $content);
    return $str;
  }

  public function isConnected($fbapi) {
    if (($api = $this->getApi())) {
      return (bool) $fbapi->getUser();
    } else {
      return false;
    }
  }

  public function checkConnection(User_Model_User $user = null, $fbapi) {
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if (null === $user) {
      $user = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    try {
      $session = new Zend_Session_Namespace();
      if (isset($session->aaf_fbaccess_token) && !empty($session->aaf_fbaccess_token)) {
        $fbapi->setAccessToken($session->aaf_fbaccess_token);
        //unset($session->aaf_fbaccess_token) ;
      }
      $fbapi->api('/me');

      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  //GETTING THE FACEBOOK INSTANCE FOR FETCHING THE USER'S ALLWED FACEBOOK INFO.
  public function getApi() {

    if (null !== $this->_api) {
      return $this->_api;
    }
    // Need to initialize
    $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
    if (empty($settings['secret']) ||
            empty($settings['appid'])
    ) {
      $api = $this->_api = null;
      Zend_Registry::set('Seaocore_Api_Facebook_Api', $api);
      return false;
    }

    $api = $this->_api = new Seaocore_Api_Facebook_Api(array(
                'appId' => $settings['appid'],
                'secret' => $settings['secret'],
                'cookie' => false, // @todo make sure this works
                'baseDomain' => $_SERVER['HTTP_HOST'],
            ));

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    Zend_Registry::set('Seaocore_Api_Facebook_Api', $this->_api);
    $fbUser = $this->_api->getUser();
    // Try to log viewer in?

    if (!isset($_SESSION['facebook_uid']) || empty($_SESSION['facebook_uid']) ||
            @$_SESSION['facebook_lock'] !== $viewer_id) {
      $_SESSION['facebook_lock'] = $viewer_id;
      if ($fbUser) {
        $_SESSION['facebook_uid'] = $fbUser;
      }
      if ($viewer_id) {
        $FacebookUserTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $FacebookUserTableName = Engine_Api::_()->getDbtable('facebook', 'user')->info('name');
        // Try to get from db
        $info = $FacebookUserTable->select()
                ->from($FacebookUserTableName)
                ->where('user_id = ?', $viewer_id)
                ->query()
                ->fetch();
        if (is_array($info) && !empty($info['facebook_uid']) &&
                !empty($info['access_token']) && !empty($info['code'])) {
          $_SESSION['facebook_uid'] = $info['facebook_uid'];
          $this->_api->clearAllPersistentData();
          $this->_api->setPersistentData('code', $info['code']);
          $this->_api->setPersistentData('access_token', $info['access_token']);
          $this->_api->setAccessToken($info['access_token']);
        } else {
          // Could not get
          $_SESSION['facebook_uid'] = false;
        }
      } else {
        // Could not get
        //$_SESSION['facebook_uid'] = false;
      }
    }
    //}

    if (isset($session->aaf_fbaccess_token) && !empty($session->aaf_fbaccess_token))
      $this->checkConnection(null, $this->_api);


    return $this->_api;
  }

  public function getAccessTokenFB($code) {

    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
    $url = "https://graph.facebook.com/oauth/access_token";
    $postString = "client_id=" . $settings['appid'] . "&client_secret=" . $settings['secret'] . "&redirect_uri=" . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . "/seaocore/auth/facebook&code=" . $code;

    $ch = curl_init();
    $opts = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'facebook-php-3.1',
        # fix for http://github.com/facebook/php-sdk/issues/issue/99
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSLVERSION => 3,
    );
    $opts[CURLOPT_POSTFIELDS] = $postString;
    $opts[CURLOPT_URL] = $url;

    // disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
    // for 2 seconds if the server does not support this header.
    if (isset($opts[CURLOPT_HTTPHEADER])) {
      $existing_headers = $opts[CURLOPT_HTTPHEADER];
      $existing_headers[] = 'Expect:';
      $opts[CURLOPT_HTTPHEADER] = $existing_headers;
    } else {
      $opts[CURLOPT_HTTPHEADER] = array('Expect:');
    }

    curl_setopt_array($ch, $opts);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  //INSERT THE FACEBOOK USER IDS WHICH HAS BEEN INVITED INTO INVITE TABLE DURING THE SIGNUP PROCESS:

  public function seacoreInvite($invitedFBFriends_ids = array(), $Type = null, $user = null) {

    //FINDING THE MODULE FROM WHICH THIS ACTION IS CALLED:
    $invite_type = 'user_invite';
    $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    if ($module == 'sitepageinvite' || $module == 'sitebusinessinvite')
      $invite_type = 'page_invite';
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    if (is_array($invitedFBFriends_ids) && (count($invitedFBFriends_ids) > 0)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if (!$viewer->getIdentity()) {
        $viewer = $user;
      }
      $viewer->invites_used += count($invitedFBFriends_ids);
      $viewer->save();

      if ($Type == 'facebook') {
        $invite_mess = Engine_Api::_()->getApi('settings', 'core')->getSetting('fbfriend.siteinvite');
        if (empty($invite_mess)) {
          $invite_mess = Zend_Registry::get('Zend_Translate')->_('Come and join me on this community. There is a lot to do here!');
        }
      } else {
        $invite_mess = Zend_Registry::get('Zend_Translate')->_('Come and join me on this community. There is a lot to do here!');
      }
      foreach ($invitedFBFriends_ids as $userId => $displayname) {
        //CHECK IF THE USER IS ALREADY INVITED. IF ALREADY INVITED THEN UPDATE THE ROW:
        $row = $inviteTable->fetchRow(array('recipient = ?' => $Type . '-' . $userId));

        if (!empty($row)) {
          $inviteTable->update(array(
              'timestamp' => new Zend_Db_Expr('NOW()'),
                  ), array(
              'recipient = ?' => $Type . '-' . $userId,
          ));
        } else {

          do {
            $inviteCode = substr(md5(rand(0, 999) . $Type . '-' . $userId), 10, 7);
          } while (null !== $inviteTable->fetchRow(array('code = ?' => $inviteCode)));

          $row = $inviteTable->createRow();
          $row->user_id = $viewer->getIdentity();
          $row->recipient = $Type . '-' . $userId;
          $row->code = $inviteCode;
          $row->timestamp = new Zend_Db_Expr('NOW()');
          $row->message = $invite_mess;
          $row->social_profileid = $userId;
          if (isset($row->service))
            $row->service = $Type;
          if (isset($row->invite_type))
            $row->invite_type = $invite_type;
          if (isset($row->displayname))
            $row->displayname = $displayname;
          $row->save();
        }
      }
    }
  }

  public function getInviteMessage($pageinvite_id, $moduletype) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $host = $_SERVER['HTTP_HOST'];
    $base_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $host . Zend_Controller_Front::getInstance()->getBaseUrl();
    $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
    $inviter_name = $viewer->getTitle();

    if ($pageinvite_id) {
      $sitepage = Engine_Api::_()->getItem($moduletype . '_page', $pageinvite_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }
    $sitepage = Engine_Api::_()->core()->getSubject($moduletype . '_page');
    $inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id),
                    ), $moduletype . '_entry_view', true);


    $invite_mess = Zend_Registry::get('Zend_Translate')->_('Hello, %1s is inviting you to join %2s at: %3s and visit the Page: %4s.');
    $invite_mess = sprintf($invite_mess, $inviter_name, $site_title, $base_url, $inviteUrl);

    return $invite_mess;
  }

}

?>