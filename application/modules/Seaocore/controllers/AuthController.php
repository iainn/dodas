<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.seaocores.com/license/
 * @version    $Id: AuthController.php 6590 2012-26-01 00:00:00Z seaocores $
 * @author     seaocores
 */
class Seaocore_AuthController extends Core_Controller_Action_Standard {

  public function facebookAction() { 
    // Clear
    if (null !== $this->_getParam('clear')) {
      unset($_SESSION['facebook_lock']);
      unset($_SESSION['facebook_uid']);
    }
    $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    $session = new Zend_Session_Namespace();
    $viewer = Engine_Api::_()->user()->getViewer();
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
    $manage_pages = $this->_getParam('manage_pages', false);
    if ($manage_pages || isset($_SESSION['manage_pages'])) {
      $_SESSION['manage_pages'] = true;
      $permissions_array = array(
                            'email',
                            'user_birthday',
                            'user_status',
                            'publish_stream',
                            'offline_access',
                            'status_update',
                            'read_stream',
                            'manage_pages'
                        );
    }
    else {
      $permissions_array = array(
                            'email',
                            'user_birthday',
                            'user_status',
                            'publish_stream',
                            'offline_access',
                            'status_update',
                            'read_stream'
                        );
    }
    $db = Engine_Db_Table::getDefaultAdapter();
   
    $URL_Home = $this->view->url(array('action' => 'home' ), 'user_general', true);
    // Enabled?
//    if (!$facebook || 'none' == $settings->core_facebook_enable) {
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }
    $redirect_uri = $this->_getParam('redirect_urimain', '');
    
    if (!empty($redirect_uri)) {
       $session->aaf_redirect_uri = urldecode($this->_getParam('redirect_urimain'));
       
    }
    // Already connected

    if ($facebook && $facebook->getUser() && empty($_GET['redirect_urimain'])) { 
       
      try { 
        if (!isset($_GET['redirect_fbback'])) {
          $permissions = $facebook->api("/me/permissions");
          
          //CHECK IF SITE IS IN MOBILE MODE THEN WE WILL ONLY ASK ABOUT PUBLISH STREAM PERMISSION.
          if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
            if( !array_key_exists('publish_stream', $permissions['data'][0]) ) { 
               $url = $facebook->getLoginUrl(array(
                        'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(). '?redirect_fbback=1',
                        'scope' => join(',', array(                            
                            'publish_stream',
                            'offline_access',                            
                        )),
                            ));


                    return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));

            }
          }
          else {
            if( !array_key_exists('read_stream', $permissions['data'][0]) ) { 
               $url = $facebook->getLoginUrl(array(
                        'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(). '?redirect_fbback=1',
                        'scope' => join(',', $permissions_array),
                            ));


                    return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));

            }
            else {
              $session->fb_canread = true;
              if (!array_key_exists('manage_pages', $permissions['data'][0])) {
                $session->fb_can_managepages = false;               
              }
              else
                $session->fb_can_managepages = true;

            }
          }
        }
      }catch (Exception $e) { 
        //continue;
      }
      $code = $facebook->getPersistentData('code');
      if (!empty($_GET['code'])) {
        $code = $_GET['code'];
      }
      
			
			//GETTING THE NEW ACCESS TOKEN FOR THIS REQUEST.
			$result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($code);
			$result = explode("&expires=", $result);
			//CLEARING THE FACEBOOK OLD PERSISTENTDATA AND SETTING THEM TO NEW.
      
      $facebook->setPersistentData('code', $code);
      $response_temp = array();
      if (!empty($result)) {
         $response_temp = explode("access_token=", $result[0]);
         if (!empty($response_temp[1])) { 
           $facebook->setAccessToken($response_temp[1]); 
           $facebook->setPersistentData('access_token', $facebook->getAccessToken());
         }
         else {
           $response_temp[1] = $facebook->getAccessToken();
         }
         
      }
      if (empty($response_temp[1])) {
        $response_temp[1] = $facebook->getAccessToken();
      }
      
			
			if ($viewer->getIdentity() && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode')) ) { 
          
          // Attempt to connect account
          $info = $facebookTable->select()
                  ->from($facebookTable)
                  ->where('user_id = ?', $viewer->getIdentity())
                  ->orwhere('facebook_uid = ?', $facebook->getUser())
                  ->limit(1)
                  ->query()
                  ->fetch();
                  
            $core_fbenable = Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable;
            $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');      
          if (empty($info) && ('publish' == $core_fbenable || 'login' == $core_fbenable || $enable_socialdnamodule)) { 
            //CHECKING FOR WHAT ADMIN HAS SET REGARDING THE REGISTRATION USING FACEBOOK.
            
             $facebookTable->insert(array(
                'user_id' => $viewer->getIdentity(),
                'facebook_uid' => $facebook->getUser(),
                'access_token' => $response_temp[1],
                'code' => $code,
                'expires' => 0, // @todo make sure this is correct
            ));
            
          } else if (!empty($info)){  

            // Save info to db
            if ($info['facebook_uid'] == $facebook->getUser() && $info['user_id'] == $viewer->getIdentity()) {
               $facebookTable->update(array(
                  'facebook_uid' => $facebook->getUser(),
                  'access_token' => $response_temp[1],
                  'code' => $code,
                  'expires' => 0, // @todo make sure this is correct
                      ), array(
                  'user_id = ?' => $viewer->getIdentity(),
              ));
            }
         
                
        }
      //}
        }
        $_SESSION['facebook_uid'] = $facebook->getUser();
        $session->aaf_fbaccess_token = $response_temp[1];
        if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) { 
           echo "<script type='text/javascript'>
           
           $(document).ready(function() { 
             if (window.opener) { 
                window.opener.fb_loginURL = '';
                window.opener.sm4.socialService.initialize('facebook');
                close();
              }
            }); 
        </script>";return;
        }
        else { 
          if (isset($_SESSION['manage_pages'])) 
              unset($_SESSION['manage_pages']); 
          if (!empty($session->aaf_redirect_uri)) { 
            $redirect_uri = $session->aaf_redirect_uri; 
            unset($session->aaf_redirect_uri);            
            return $this->_helper->redirector->gotoUrl($redirect_uri, array('prependBase' => false)); 

          } else 
           // Redirect to home
             return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));    
        }
      
    }

    // Not connected
    else {
      // Okay
      if (!empty($_GET['code'])) {
        // This doesn't seem to be necessary anymore, it's probably
        // being handled in the api initialization
        if (isset($_SESSION['manage_pages'])) 
          unset($_SESSION['manage_pages']);
        return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
      }

      // Error
      else if (!empty($_GET['error'])) { 
        if (isset($_SESSION['manage_pages'])) 
          unset($_SESSION['manage_pages']);
        // @todo maybe display a message?
        return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
      }
      else if (isset($_GET['redirect_fbback'])) { 
        if (isset($_SESSION['manage_pages'])) 
          unset($_SESSION['manage_pages']);
        if (!empty($session->aaf_redirect_uri))
          return $this->_helper->redirector->gotoUrl($session->aaf_redirect_uri, array('prependBase' => false));
      }

      // Redirect to auth page
      else { 
        if (!empty($_GET['redirect_urimain'])) {
					$session = new Zend_Session_Namespace();
					$session->aaf_redirect_uri = urldecode($_GET['redirect_urimain']);
				}
				
        //CHECK IF THE SITE IS IN MOBILE MODE. THEN WE WILL ONLY ASK FOR PUBLISH STREAM.
        if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) {
          $scope = array('publish_stream');
        }
        else {
          $scope = join(',', $permissions_array);
        }
			
        $url = $facebook->getLoginUrl(array(
            'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(),
            'scope' => $scope,
                ));
                
              
        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    }
  }


  public function twitterAction() {
    $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    // Clear
    if (null !== $this->_getParam('clear')) {
      unset($_SESSION['twitter_lock']);
      unset($_SESSION['twitter_token']);
      unset($_SESSION['twitter_secret']);
      unset($_SESSION['twitter_token2']);
      unset($_SESSION['twitter_secret2']);
    }

    if ($this->_getParam('denied')) {
      $this->view->error = 'Access Denied!';
      return;
    }

    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    $twitter = $twitterTable->getApi();
    $twitterOauth = $twitterTable->getOauth();

    $db = Engine_Db_Table::getDefaultAdapter();
    

    // Check
    if (!$twitter || !$twitterOauth) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Connect
    try {

      $accountInfo = null;
      if (isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2'])) {
        // Try to login?
        if (!$viewer->getIdentity()) {
          // Get account info
          try {
            $accountInfo = $twitter->account->verify_credentials();
          } catch (Exception $e) {
            // This usually happens when the application is modified after connecting
            unset($_SESSION['twitter_token']);
            unset($_SESSION['twitter_secret']);
            unset($_SESSION['twitter_token2']);
            unset($_SESSION['twitter_secret2']);
            $twitterTable->clearApi();
            $twitter = $twitterTable->getApi();
            $twitterOauth = $twitterTable->getOauth();
          }
        }
      }

      if (isset($_SESSION['twitter_token'], $_SESSION['twitter_secret'], $_GET['oauth_verifier'])) {
        $twitterOauth->getAccessToken('https://twitter.com/oauth/access_token', $_GET['oauth_verifier']);

        $_SESSION['twitter_token2'] = $twitter_token = $twitterOauth->getToken();
        $_SESSION['twitter_secret2'] = $twitter_secret = $twitterOauth->getTokenSecret();

        // Reload api?
        $twitterTable->clearApi();
        $twitter = $twitterTable->getApi();

        // Get account info
        $accountInfo = $twitter->account->verify_credentials();
        $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
        // Save to settings table (if logged in)
        if ($viewer->getIdentity() && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {
          $info = $twitterTable->select()
                  ->from($twitterTable)
                  ->where('user_id = ?', $viewer->getIdentity())
                  ->orwhere('twitter_uid = ?', $accountInfo->id)
                  ->query()
                  ->fetch();
   
          if( !empty($info) ) { 
            if( !empty($info['twitter_uid']) && $info['twitter_uid'] != $accountInfo->id ) {
//                $error_msg = Zend_Registry::get('Zend_Translate')->_('The Twitter account that you are trying to login with seems to be already used by some other user. Please logout from this Twitter account and try again.');
//                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
//                die;
            } else if ($info['user_id'] == $viewer->getIdentity() && $info['twitter_uid'] == $accountInfo->id){
                $twitterTable->update(array(
                  'twitter_uid' => $accountInfo->id,
                  'twitter_token' => $twitter_token,
                  'twitter_secret' => $twitter_secret,
                ), array(
                  'user_id = ?' => $viewer->getIdentity(),
                ));
            }
          } 
          else if ('publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable || 'login' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable || $enable_socialdnamodule) {
            $twitterTable->insert(array(
              'user_id' => $viewer->getIdentity(),
              'twitter_uid' => $accountInfo->id,
              'twitter_token' => $twitter_token,
              'twitter_secret' => $twitter_secret,
            ));
          }
          // Redirect
          if (!empty($_GET['return_url'])) { 
            //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
             $queryString = explode("?", urldecode($_GET['return_url']));
             if (isset($queryString[1])) {
               $returnUrl = urldecode($_GET['return_url']) . '&redirect_tweet=1';
             }
             else {
               $returnUrl = $_GET['return_url'] . '?redirect_tweet=1';
             }
            
            return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
          } else {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
          }
        } else { // Otherwise try to login?
          if (!empty($_GET['return_url'])) { 
            //CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
             $queryString = explode("?", urldecode($_GET['return_url']));
             if (isset($queryString[1])) {
               $returnUrl = urldecode($_GET['return_url']) . '&redirect_tweet=1';
             }
             else {
               $returnUrl = $_GET['return_url'] . '?redirect_tweet=1';
             }
            return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
          } else { 
            if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) { 
               echo "<script type='text/javascript'>

               $(document).ready(function() { 
                 if (window.opener) { 
                    window.opener.twitter_loginURL = '';
                    window.opener.sm4.socialService.initialize('twitter');
                    close();
                  }
                }); 
            </script>";return;
            }
            else 
              return $this->_helper->redirector->gotoRoute(array(), 'default', true);
          }
        }
      } else {

        unset($_SESSION['twitter_token']);
        unset($_SESSION['twitter_secret']);
        unset($_SESSION['twitter_token2']);
        unset($_SESSION['twitter_secret2']);

        // Reload api?
        $twitterTable->clearApi();
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        
        // Connect account
        if (!empty($_GET['return_url']))
          $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token', (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?return_url=' . urlencode($_GET['return_url']));
        else
          $twitterOauth->getRequestToken('https://twitter.com/oauth/request_token', (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url());

        $_SESSION['twitter_token'] = $twitterOauth->getToken();
        $_SESSION['twitter_secret'] = $twitterOauth->getTokenSecret();

        $url = $twitterOauth->getAuthorizeUrl('http://twitter.com/oauth/authorize');

        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    } catch (Services_Twitter_Exception $e) { 
      if (in_array($e->getCode(), array(500, 502, 503))) {
        $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
        echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
        die;
        return;
      } else {
        $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
        echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
        die;
      }
    } catch (Exception $e) {
      $error_msg = Zend_Registry::get('Zend_Translate')->_('Twitter is currently experiencing technical issues, please try again later.');
      echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
      die;
    }
  }
  
  
  public function linkedinAction () { 
     $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
  	// Clear
     if (null !== $this->_getParam('clear')) {
      unset($_SESSION['linkedin_lock']);
      unset($_SESSION['linkedin_token']);
      unset($_SESSION['linkedin_secret']);
      unset($_SESSION['linkedin_token2']);
      unset($_SESSION['linkedin_secret2']);
    }
    if ($this->_getParam('denied')) {
      $this->view->error = 'Access Denied!';
      return;
    }
    
    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'advancedactivity');
    $OBJ_linkedin = $linkedinTable->getApi();
    if (empty($OBJ_linkedin)) return;
    //$linkedinOauth = $linkedinTable->getOauth();

    $db = Engine_Db_Table::getDefaultAdapter();
    
    $URL_Home = $this->view->url(array('action' => 'home' ), 'user_general', true);

    $redirect_uri = $this->_getParam('redirect_urimain', '');
    
    if (!empty($redirect_uri)) {
       $session->aaf_redirect_uri = urldecode($this->_getParam('redirect_urimain'));
       
    }
//     
    
      
						
			if (isset($_SESSION['linkedin_token'], $_SESSION['linkedin_secret'], $_GET['oauth_verifier'])) {  
			 $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_linkedin=' . $redirect_uri);
        $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['linkedin_token'], $_SESSION['linkedin_secret'], $_GET['oauth_verifier']);
        
        if ($response['success'] == TRUE) { 
          $_SESSION['linkedin_obj'] = $OBJ_linkedin;
					$_SESSION['linkedin_token2'] = $linkedin_token = $response['linkedin']['oauth_token'];
					$_SESSION['linkedin_secret2'] = $linkedin_secret = $response['linkedin']['oauth_token_secret'];

					// Reload api?
					$linkedinTable->clearApi();
					$linkedin = $linkedinTable->getApi();

					// Get account info
				$getUserinfo = $OBJ_linkedin->profile('~:(id)');
				
				$getUserinfo = json_decode(json_encode((array) simplexml_load_string($getUserinfo['linkedin'])), 1);
				
					$enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
					// Save to settings table (if logged in)
					if ($viewer->getIdentity() && (!$enable_sitemobile || !Engine_API::_()->sitemobile()->checkMode('mobile-mode'))) {
						$info = $linkedinTable->select()
										->from($linkedinTable)
										->where('user_id = ?', $viewer->getIdentity())
										->orwhere('linkedin_uid = ?', $getUserinfo['id'])
										->query()
										->fetch();
		
						if( !empty($info) ) { 
							if( !empty($info['linkedin_uid']) && $info['linkedin_uid'] != $getUserinfo['id'] ) {
	//                $error_msg = Zend_Registry::get('Zend_Translate')->_('The Twitter account that you are trying to login with seems to be already used by some other user. Please logout from this Twitter account and try again.');
	//                echo '<ul class="form-errors"><li>' . $error_msg . '</li></ul>';
	//                die;
							} else if ($info['user_id'] == $viewer->getIdentity() && $info['linkedin_uid'] == $getUserinfo['id']){
									$linkedinTable->update(array(
										'linkedin_uid' => $getUserinfo['id'],
										'linkedin_token' => $linkedin_token,
										'linkedin_secret' => $linkedin_secret,
									), array(
										'user_id = ?' => $viewer->getIdentity(),
									));
							}
						} 
						else  {
							$linkedinTable->insert(array(
								'user_id' => $viewer->getIdentity(),
								'linkedin_uid' => $getUserinfo['id'],
								'linkedin_token' => $linkedin_token,
								'linkedin_secret' => $linkedin_secret,
							));
						}
					
						// Redirect
						if (!empty($_GET['redirect_urimain'])) { 
							//CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
							$queryString = explode("?", urldecode($_GET['redirect_urimain']));
							if (isset($queryString[1])) {
								$returnUrl = urldecode($_GET['redirect_urimain']) . '&redirect_linkedin=1';
							}
							else {
								$returnUrl = $_GET['redirect_urimain'] . '?redirect_linkedin=1';
							}
							
							return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
						} else {
							return $this->_helper->redirector->gotoRoute(array(), 'default', true);
						}
					} else { // Otherwise try to login?
						if (!empty($_GET['redirect_urimain'])) { 
							//CHECK IF ALREADY THERE ARE SOME QUERY PARAMETERS OR NOT.
							$queryString = explode("?", urldecode($_GET['redirect_urimain']));
							if (isset($queryString[1])) {
								$returnUrl = urldecode($_GET['redirect_urimain']) . '&redirect_linkedin=1';
							}
							else {
								$returnUrl = $_GET['redirect_urimain'] . '?redirect_linkedin=1';
							}
							return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
						} else { 
              if ($enable_sitemobile && Engine_API::_()->sitemobile()->checkMode('mobile-mode')) { 
               echo "<script type='text/javascript'>

               $(document).ready(function() { 
                 if (window.opener) { 
                    window.opener.linkedin_loginURL = '';
                    window.opener.sm4.socialService.initialize('linkedin');
                    close();
                  }
                }); 
            </script>";return;
            }
            else 
							return $this->_helper->redirector->gotoRoute(array(), 'default', true);
						}
					}
        }
        else { 
        $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_urimain=' . $redirect_uri);
           $response = $OBJ_linkedin->retrieveTokenRequest();
	     
					if($response['success'] === TRUE) {
							// store the request token						
							$_SESSION['linkedin_token'] = $response['linkedin']['oauth_token'];
							$_SESSION['linkedin_secret'] = $response['linkedin']['oauth_token_secret'];
							// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
							header('Location: ' . Seaocore_Api_Linkedin_Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']); die;
						} 
        }
      }
			
			
			
			
			if (!isset($_GET['redirect_linkedin'])) {   
			 
			  $OBJ_linkedin->setCallbackUrl((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_urimain=' . $redirect_uri);
			  $OBJ_linkedin->setToken(NULL);
	      $response = $OBJ_linkedin->retrieveTokenRequest();	   
		  	if($response['success'] === TRUE) {
		        // store the request token
		        //$_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
		        $_SESSION['linkedin_token'] = $response['linkedin']['oauth_token'];
		        $_SESSION['linkedin_secret'] = $response['linkedin']['oauth_token_secret'];
		       
		        // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
		        header('Location: ' . Seaocore_Api_Linkedin_Linkedin::_URL_AUTH . $response['linkedin']['oauth_token'] ); die;
		      } else  { 
		       echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
		     }
     }
    
//      	 $URL_Home = $this->view->url(array('action' => 'home' ), 'user_general', true);
//      	 return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_linkedin=1', array('prependBase' => false));
  	
  	
  }

  public function logoutAction() {

    if ($this->_getParam('logout_service') == 'facebook') {
      try { 
         
          $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        
        if ($facebook->getUser())
          $fb_userid = $facebook->getUser();
        else
          $fb_userid = $_SESSION['facebook_uid'];
        if ($facebook && $fb_userid) {
          
          
          $accessToken = $facebook->getAccessToken();
          $statusUpdate = $facebook->api('/' . $fb_userid . '/permissions/', 'DELETE', array('access_token' => $accessToken));
          
        }
          //unset($_SESSION['facebook_lock']);
          //unset($_SESSION['facebook_uid']);
          if (isset($session->aaf_redirect_uri))
           unset($session->aaf_redirect_uri);
          if (isset($session->aaf_fbaccess_token))
           unset($session->aaf_fbaccess_token);
          if (isset($session->fb_canread))
           unset($session->fb_canread);
          if (isset($session->fb_can_managepages))
           unset($session->fb_can_managepages);
               
         if (isset($session->fb_checkconnection))
           unset($session->fb_checkconnection);
       
      } catch (Exception $e) { 
      }
    } else if ($this->_getParam('logout_service') == 'twitter') {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        unset($_SESSION['twitter_token']);
        unset($_SESSION['twitter_secret']);
        unset($_SESSION['twitter_token2']);
        unset($_SESSION['twitter_secret2']);

        // Reload api?
        $twitterTable->clearApi();
      } catch (Exception $e) {
        
      }
    }
    else if ($this->_getParam('logout_service') == 'linkedin') { 
      try {		
		
        unset($_SESSION['linkedin_token']);
        unset($_SESSION['linkedin_secret']);
        unset($_SESSION['linkedin_token2']);
        unset($_SESSION['linkedin_secret2']);
        $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'advancedactivity');
        $linkedin = $linkedinTable->getApi();

        // Reload api?
        $linkedinTable->clearApi();    
      
      } catch (Exception $e) {
          
      }
    }
    
    echo Zend_Json::encode(array('success' => 1));
    exit();
  }

  
  //INTERMEDIATE SIGNUP PROCESS WHEN INVITED USERS ARE COMING FROM THE OTHER SOCIAL INVITER SERVICES:
  
  public function socialSignupAction () { 
    
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer && $viewer->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $callbackURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url()
        . '?type=' . $this->_getParam('type', null) . '&refuser=' . $this->_getParam('refuser', null) ;
     
    if( 1) {
      if ($this->_getParam('type', null) == 'facebook') {    
        $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        	    
         if (!$this->_getParam('code', '')) { 
          
          $result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB ($this->_getParam('code')); 
        	if (!empty ($result)) {
            $result = explode("&expires=", $result);
            $response_temp = explode("access_token=", $result[0]);
            $facebook->setAccessToken($response_temp[1]); 
            $facebook->setPersistentData('access_token', $facebook->getAccessToken());
        	}
         }
            
         try {       
      	       $userFBInfo = $facebook->api('/me'); 
      	       $recipientId = $userFBInfo['id'];
      	       $recipientEmail = $userFBInfo['email'];
            } catch (Exception $e) { 
              
                //CHECK IF ALREADY EMAIL ADDRESS EXIST OF THIS USER
                if ($facebook->getUser()) {
                  $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
                  $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser()));
                  $validator = new Zend_Validate_EmailAddress();
                  if ($validator->isValid($userInvited->recipient)) {
                    $recipientId = $facebook->getUser();
        	          $recipientEmail = $userInvited->recipient;
                  }
                  
                }
                if (!isset ($recipientId)) {
                
                $url = $facebook->getLoginUrl(array(
                'redirect_uri' => $callbackURL,
                'scope' => join(',', array(
                              'email',
                              'user_birthday'
                              
                              
                          )),
                    )) ;
                    
                return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false)); 
                
              }  
            }
//        	}
//        }
      }
      else if ($this->_getParam('type', null) == 'linkedin') { 
        $API_CONFIG = array(
          'appKey'       => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'),
      	  'appSecret'    =>  Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'),
      	  'callbackUrl'  => $callbackURL 
        );
        $OBJ_linkedin = new Seaocore_Api_Linkedin_Linkedin($API_CONFIG); 
        if (!isset($_GET['oauth_verifier']) && !isset($_GET['email'])) {
          $response = $OBJ_linkedin->retrieveTokenRequest();
  			
    			if($response['success'] === TRUE) {
            // store the request token
            $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
          
            // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
            header('Location: ' . Seaocore_Api_Linkedin_Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
          }
        }
        else { 
          if (!isset($_GET['email'])) {
            $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
            if($response['success'] === TRUE) { 
               // the request went through without an error, gather user's 'access' tokens
              $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
              
              // set the user as authorized for future quick reference
              $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
              
              //FETCHING THE PROFILE INFO OF THE CURRENT USER.
               $response = $OBJ_linkedin->profile('~:(id)');
                if($response['success'] === TRUE) { 
                  if( $settings->getSetting('user.signup.checkemail') ) { 
                    $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
                    $recipientId = (string)$response['linkedin']->id;
                    //CHECK IF THE USER HAS ALREADY REGISTERED TO SITE OR NOT.
                    $this->validateUser($recipientId, '');
                    $this->view->form = $form = new Seaocore_Form_getEmail();
                    $form->refuser->setValue($this->_getParam('refuser', null));
                  }
                  else {
                      $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
                      $recipientId = (string)$response['linkedin']->id;
            	        $recipientEmail = '';
            	        
                  }
                } 
              }
            }
            else {  
              $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
              $response = $OBJ_linkedin->profile('~:(id)');
              $response['linkedin'] = new SimpleXMLElement($response['linkedin']);
              $recipientId = (string)$response['linkedin']->id;
      	      $recipientEmail = $_GET['email'];
      	      
            }
          }
      }
      else if ($this->_getParam('type', null) == 'twitter') { 
        if (!isset($_GET['redirect_tweet']) && !isset($_GET['email'])) { 
          $TwitterloginURL = Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble(array('module' => 'seaocore', 'controller' => 'auth',
                            'action' => 'twitter'), 'default', true) . '?return_url=' . urlencode($callbackURL);
  		    return $this->_helper->redirector->gotoUrl($TwitterloginURL, array('prependBase' => false));
  		    
        }
        try { 
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          if (!isset($_GET['email'])) {
            if ($twitter && $twitterTable->isConnected()) { 
              $accountInfo = $twitter->account->verify_credentials();
              $recipientId = (string)$accountInfo->id;
              
              if( $settings->getSetting('user.signup.checkemail') ) { 
                 
                 $recipientEmail = '';             
                //CHECK IF THE USER HAS ALREADY REGISTERED TO SITE OR NOT.
               
                
                $this->validateUser($recipientId, '');
                $this->view->form = $form = new Seaocore_Form_getEmail(); 
                $form->refuser->setValue($this->_getParam('refuser', null));
                $form->type->setValue($this->_getParam('type', null));
              }
              else {
                 
        	        $recipientEmail = '';
        	        
              }
              
            }
            
          }
          else { 
             if (isset($_GET['email'])) {
                $recipientEmail = $_GET['email'];
            }
            $accountInfo = $twitter->account->verify_credentials();
            $recipientId = (string)$accountInfo->id;
          }
          
        } catch (Exception $e) {
          
         
        }
      }
      if($this->_getParam('type', null) !== null && $this->_getParam('refuser', null) !== null && isset($recipientId)) {
         $this->validateUser($recipientId, $recipientEmail);
           
      }
    }
    else { 
      return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
    }
      
      if (!empty($recipientId) && !empty($recipientEmail)) { 
        return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
      }
  }
  
  //SAVING FACEBOOK INVITED USERS.....
  
  public function saveFbInviterAction () { 
     if ($this->getRequest()->isPost()) { 
        $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
        $fbUser_ids = $this->getRequest()->get('ids');
        $fb_User = array();
        //GETTING THE DISPLAY NAME OF THE USERS ALSO.
        $session = new Zend_Session_Namespace();
        $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
        try {
        if ($facebook) { 
          foreach ($fbUser_ids as $key => $id) :
              
             $displayname = $facebook->api('/' . $id);
        
             $fb_User[$id] = $displayname['name'];
          endforeach;
        }
        } catch (Exception $e) {
          
        }
        if (empty($fb_User)) :
          foreach ($fbUser_ids as $key => $id) :            
             $fb_User[$id] = '';
          endforeach;
        endif;  
      
        $facebookInvite->seacoreInvite($fb_User, 'facebook');
        echo Zend_Json::encode(array('status' => true));
        exit();
    }
   
 }
 
 //CHECK FOR VALID USER AND THEN REDIRECT
  public function validateUser ($recipientId, $recipientEmail) { 
   
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    
    if (empty($recipientEmail)) { 
       $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $recipientId)); 
       
       if ($userInvited) {
         if (!empty($userInvited->recipient) && $userInvited->recipient != $this->_getParam('type') . '-' . $recipientId ) {
            $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'invite',
              'controller' => 'signup',
            ), 'default', true)
          . '?code=' . $userInvited->code . '&email=' . $userInvited->recipient;
          return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
         }
         
       }
       else { 
         return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
       }
    }
    else { 
      //CHECK EITHER THIS USER IS VALID USER OR NOT:
  	   $userInvited = $inviteTable->fetchRow(array('recipient = ?' => $this->_getParam('type') . '-' . $recipientId)); 
  	   if ($userInvited) { 
  	          	       
  	        $inviteTable->update(array(
            'recipient' => $recipientEmail,
          ), array(
             'new_user_id = ?' => 0,
            'recipient = ?' => $this->_getParam('type') . '-' . $recipientId
          ));
  	      
      	   $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'invite',
              'controller' => 'signup',
            ), 'default', true)
          . '?code=' . $userInvited->code . '&email=' . $recipientEmail;
        
          
          return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
  	   }
  	   else {
  	     $userInvited = $inviteTable->fetchRow(array('recipient = ?' => $recipientEmail, 'social_profileid = ?' => $recipientId));
  	      if ($userInvited) {
  	        
  	         $inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'invite',
              'controller' => 'signup',
            ), 'default', true)
          . '?code=' . $userInvited->code . '&email=' . $recipientEmail;
           return $this->_helper->redirector->gotoUrl($inviteUrl, array('prependBase' => false));
          
  	      }
  	   }
    } 
    
  }
  
  //THIS IS THE MESSAGE WHICH WILL BE SHOWN TO THE FACEBOOK INIVTED USERS.
  public function fbInviteAction () {   
    $refuser = 0;
    $this->_helper->layout->disableLayout(true);
    
    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB ($this->_getParam('code'));
    if (!empty($result)) {
      $result = explode("&expires=", $result);
      $response_temp = explode("access_token=", $result[0]);
      
      $facebook->setAccessToken($response_temp[1]); 
      $facebook->setPersistentData('access_token', $facebook->getAccessToken());
    }
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if ($facebook && $facebook->getUser()) {  
      
      //IS JOINED THE WEBSITE USING THEIR FACEBOOK ACCOUNT INTEGRATION.
      $info = $facebookTable->select()
                    ->from($facebookTable)                  
                    ->where('facebook_uid = ?', $facebook->getUser())
                    ->limit(1)
                    ->query()
                    ->fetch();
                    
      // IS INVITED AND SIGNUPED FOR WEBSITE OR NOT.              
      $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
      $userInvited = $inviteTable->fetchRow(array('social_profileid = ?' => $facebook->getUser()));
      
      //CASE: IS INVITED AND YET NOT JOINED THIS WEBSITE:
      if ($userInvited && empty($userInvited->new_user_id) && empty($info)) { 
        
        $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl(). '/seaocore/auth/social-Signup/?type=facebook&refuser=' . $userInvited->user_id; 
      }
      else { 
        
        $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
       
      }
    }
    else if (isset($_GET['fbredirect'])) {
      $this->view->callBackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    }
    else { 
      
      $FBloginURL = $facebook->getLoginUrl(array(
          'redirect_uri' => ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/seaocore/auth/fb-Invite?fbredirect=1',
          'scope' => join(',', array(
                           'email',
                          'user_birthday',
                          'user_status'
                       
            
         )),
      ));
      $this->view->callBackURL = $FBloginURL;      
    }    
  }
}
