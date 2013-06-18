<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Twitter.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Advancedactivity_Model_Dbtable_Linkedin extends Engine_Db_Table
{
  protected $_api;  

  public function getApi()
  {
    if( null === $this->_api ) {
      $this->_initializeApi();
    }

    return $this->_api;
  } 

  public function clearApi()
  {
    $this->_api = null;    
    return $this;
  }

  public function isConnected()
  {
    // @todo make sure that info is validated
    return ( !empty($_SESSION['linkedin_token2']) && !empty($_SESSION['linkedin_secret2']) );
  }

  protected function _initializeApi()
  {
    
    // Load settings
    $settings['key'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
		$settings['secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
    
    if( empty($settings['key']) ||
        empty($settings['secret'])
        ) {
            $this->_api = null;
						Zend_Registry::set('Seaocore_Api_Linkedin_Linkedin', $this->_api);
						return false;
      
    }
    
    $this->_api = new Seaocore_Api_Linkedin_Linkedin(array(
		    'appKey'       => $settings['key'],
			  'appSecret'    => $settings['secret'],
			  'callbackUrl'  => '' 
		  	));
      Zend_Registry::set('Seaocore_Api_Linkedin_Linkedin', $this->_api);
    
     
    // Try to log viewer in?
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !isset($_SESSION['linkedin_uid']) ||
        @$_SESSION['linkedin_lock'] !== $viewer->getIdentity() ) {
      $_SESSION['linkedin_lock'] = $viewer->getIdentity();
      if( $viewer && $viewer->getIdentity() ) {
        // Try to get from db
        $info = $this->select()
            ->from($this)
            ->where('user_id = ?', $viewer->getIdentity())
            ->query()
            ->fetch();
        if( is_array($info) &&
            !empty($info['linkedin_secret']) &&
            !empty($info['linkedin_token']) ) {
          $_SESSION['linkedin_uid'] = $info['linkedin_uid'];
          $_SESSION['linkedin_secret2'] = $info['linkedin_secret'];
          $_SESSION['linkedin_token2'] = $info['linkedin_token'];
         
          $this->_api->setToken(array('oauth_token' => $info['linkedin_token'], 'oauth_token_secret' => $info['linkedin_secret']));
        } else {
          $_SESSION['linkedin_uid'] = false; // @todo make sure this gets cleared properly
        }
      } else {
        // Could not get
        //$_SESSION['linkedin_uid'] = false;
      }
    }
    else if (isset($_SESSION['linkedin_secret2'], $_SESSION['linkedin_token2'])){
      $this->_api->setToken(array('oauth_token' => $_SESSION['linkedin_token2'], 'oauth_token_secret' => $_SESSION['linkedin_secret2']));
    
    }

    // Get oauth
//     if( isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']) ) {
//       $this->_oauth = new HTTP_OAuth_Consumer($settings['key'], $settings['secret'],
//           $_SESSION['twitter_token2'], $_SESSION['twitter_secret2']);
//     } else if( isset($_SESSION['twitter_token'], $_SESSION['twitter_secret']) ) {
//       $this->_oauth = new HTTP_OAuth_Consumer($settings['key'], $settings['secret'],
//           $_SESSION['twitter_token'], $_SESSION['twitter_secret']);
//     } else {
//       $this->_oauth = new HTTP_OAuth_Consumer($settings['key'], $settings['secret']);
//     }
//     $this->_api->setOAuth($this->_oauth);
  }
}
