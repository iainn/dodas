<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Api.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Api_Twitter_Api { 
  
  
   /**
	 *  Retrive the contacts from the xml response from linkedin server.
	 * 
	 */  
public function retriveContacts ($moduletype = '') {
  
  try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        if ($twitter || $twitterTable->isConnected()) { 
          
          //get all the followers ids who are following the currently authenticated user.
          $logged_TwitterUserfollowersIds = $twitter->followers->ids(array('cursor' => -1));
          $i = 0;
          $followerids_String = '';
          $count = count($logged_TwitterUserfollowersIds->ids);
          foreach ($logged_TwitterUserfollowersIds->ids as $key => $followerId) :
            $followerids_String = $followerids_String. $followerId . ',';
            $i++;
            if ($i == 100 || ($count == $i)) { 
              //GET THE USER INFORMATIONS FOR 100 USERS AT A TIME.
              $logged_TwitterUserfollowers[] = $twitter->users->lookup(array('user_id' => trim($followerids_String, ',')));
              $i = 0;
            }          
            
          endforeach;
          $result = array();
          foreach ($logged_TwitterUserfollowers as $key => $value) { 
            $result = array_merge($result, $value);
          }
          $logged_TwitterUserfollowers = $result;
          if(count($logged_TwitterUserfollowers) > 0) {
            $contactInfo = array ();
            $key = 0;
            foreach($logged_TwitterUserfollowers as $follower) { 
               $contactInfo[$key]['name'] = $follower->screen_name ;
               $contactInfo[$key]['id'] = $follower->id ;
               $contactInfo[$key]['picture'] = $follower->profile_image_url ;
               $key++;
            }
           
            if ($moduletype == 'sitepage' || $moduletype == 'sitebusiness') { 
    			    $SiteNonSiteFriends = array();
    			    $result = array();
    			    $SiteNonSiteFriends[1] = $contactInfo;
			    
              $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
              $contactInfo = $result;
            
  			  }
  			  else 
            $contactInfo = $this->parseUserContacts ($contactInfo);
           }
          
        } 
      } catch (Exception $e) { 
        $this->view->TwitterLoginURL = $TwitterloginURL;
        // Silence
      }
 
   return $contactInfo;
}

  
  
  
  /**
	 *  Parse the contacts in two parts :
	 * 
	 * 1) Contacts which are already at site
	 * 
	 * 2) Contacts which are not on this site
	 * 
	 */  
public function parseUserContacts ($contactInfo) {
  
  $viewer =  Engine_Api::_()->user()->getViewer();
	$user_id = $viewer->getIdentity();
	$table_user = Engine_Api::_()->getitemtable('user');
	$tableName_user = $table_user->info('name');
	
	$inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
  $inviteTableName = $inviteTable->info('name');
  
	$table_user_memberships = Engine_Api::_()->getDbtable('membership' , 'user');
	$tableName_user_memberships = $table_user_memberships->info('name');
	$SiteNonSiteFriends[] = '';
	
	foreach ($contactInfo as $values) {

		//FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
		$select = $table_user->select()
		->setIntegrityCheck(false)
		->from($tableName_user, array('user_id', 'displayname', 'photo_id'))
		->join($inviteTableName, "$inviteTableName.new_user_id = $tableName_user.user_id", null)
		->where($inviteTableName . '.new_user_id <>?', 0)
		->limit(1)
		->where($inviteTableName . '.social_profileid = ?', $values['id']);

		$is_site_members = $table_user->fetchRow($select);
		
    if (empty($user_id)) {
			if (!empty($is_site_members->user_id)) {
				continue;
      }

    }
		//NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
		if (!empty($is_site_members->user_id) && $is_site_members->user_id != $user_id) { 
		  
			$contact =  Engine_Api::_()->user()->getUser($is_site_members->user_id);
			// Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($contact);
      }
      else
        $friendship_status = $contact->membership()->getRow($viewer);
       
      if (!$friendship_status || $friendship_status->active == 0) {
        $SiteNonSiteFriends[0][] = $is_site_members->toArray();;
      }
     
		}
		//IF USER IS NOT SITE MEMBER .
		else if (empty($is_site_members->user_id)) {
		  
			$SiteNonSiteFriends[1][] = $values;
		}
	}

	$result[0] = '';
	$result[1] = '';
	if (!empty($SiteNonSiteFriends[1]))
	   $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));

	if (!empty($SiteNonSiteFriends[0]))    
	   $result[0] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[0])));
  return $result;
  
   
}

public function sendInvite ($friendsToJoin, $user_data = null, $pageinvite_id = null, $moduletype = '') { 
  $viewer = Engine_Api::_()->user()->getViewer();
  if (!$viewer->getIdentity()) {
   $viewer = $user_data; 
  } 
  //Now make the body of invite messge:
  //CHECK IF WWW EXIST IN THE HOST NAME.
  $removeWWW = explode("www.", $_SERVER['HTTP_HOST']);
  if (count($removeWWW) == 2) {
    $HOST = $removeWWW[1];
  }
  else {
    $HOST = $removeWWW[0];
  }
  $callbackURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $HOST . Zend_Controller_Front::getInstance()->getBaseUrl(). '/seaocore/auth/social-Signup/?type=twitter&refuser=' . $viewer->getIdentity();

  $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
  $twitter = $twitterTable->getApi();
 if ($moduletype == 'sitepage' || $moduletype == 'sitebusiness') { 
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
    
     if (strlen(Zend_Registry::get('Zend_Translate')->_('You are being invited to visit my Page at: ')) > 50) { 
		 $message = substr(Zend_Registry::get('Zend_Translate')->_('You are being invited to visit my Page at: '), 0, 40) ;
		 $message .= '...'; 
		 $bodyTextTemplate = $message . ' ' . $inviteUrl;
	 }
	 else 
		$bodyTextTemplate = Zend_Registry::get('Zend_Translate')->_('You are being invited to visit my Page at: ') . ' ' . $inviteUrl;
   
 }
  else { 
	   

      if (strlen(Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at')) > 50) { 
		 $message = substr(Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at'), 0, 40) ; 
		 $message .= '...';
		 $bodyTextTemplate = $message . ' ' . $callbackURL; 
		 
	 }   
      $bodyTextTemplate = Zend_Registry::get('Zend_Translate')->_('You are being invited to join me at') . ' ' . $callbackURL;
  }
  try {
    if ($twitter || $twitterTable->isConnected()) {
      foreach ($friendsToJoin as $follower) {
        $response =  $twitter->direct_messages->new(array('text' => $bodyTextTemplate, 'user_id' => $follower, 'wrap_links' => true));
        
      }
    }
  } catch (Exception $e) { 
    
      echo  Zend_Registry::get('Zend_Translate')->_("Some problem occured while sending invitation to your followers. Please try again later.");die;
  }
  //SAVING THE INFO INTO DATABASE.
  Seaocore_Api_Facebook_Facebookinvite::seacoreInvite($friendsToJoin, 'twitter', $viewer);
  
}
  
  
}
