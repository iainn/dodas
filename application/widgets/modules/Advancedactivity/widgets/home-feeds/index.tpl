<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php if ($this->count_tabs == 1 && empty($this->title)): ?>
  <h3> 
    <?php echo $this->translate($this->settingsApi->getSetting('advancedactivity.sitetabtitle', "What's New!")); ?>
  </h3>
<?php endif; ?>
<?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/core.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/advancedactivity-facebookse.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/advancedactivity-twitter.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/advancedactivity-linkedin.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');

	$this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Advancedactivity/externals/styles/style_advancedactivity.css');


  if( !empty( $this->is_welcomeTabEnabled ) && !empty($this->is_suggestionEnabled) ) {
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
     $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/usercontacts.js');
    
  }else if( !empty( $this->is_welcomeTabEnabled ) && !empty($this->is_pymkEnabled) ) {
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Peopleyoumayknow/externals/scripts/core.js');
     $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/usercontacts.js');
  }

  $this->headScript()->appendFile($this->layout()->staticBaseUrl. 'application/modules/Activity/externals/scripts/core.js')
   ->appendFile($this->layout()->staticBaseUrl.  'externals/flowplayer/flashembed-1.0.1.pack.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');

 $this->headTranslate(array('ADVADV_SHARE', 'Who are you with?','with'));
?>
 <?php // SHOW SITE ACTIVITY FEED. ?>
 
<div class="aaf_tabs aaf_main_tabs_feed" id="aaf_main_tabs_feed">
  <?php if($this->count_tabs > 1):?>
   <ul class="aaf_tabs_apps">
    <?php if ($this->isWelcomeEnable): ?>
      <li <?php if($this->activeTab==4): ?> class="aaf_tab_active" <?php endif; ?> id="Welcometab_activityfeed">
        <a href="javascript:void(0);" class='' onclick="tabSwitchAAFContent($(this), 'welcome' );" >        
          <?php if (1 & $this->tabtype): ?>
          <?php $welcomeIcon = Engine_Api::_()->getApi('settings',
          'core')->getSetting('advancedactivity_icon1',
				'application/modules/Advancedactivity/externals/images/welcome-icon.png');
				$photoName = $this->baseUrl() . '/' . $welcomeIcon; ?>
            <img src="<?php echo  $photoName ?>" alt="" title="<?php echo $this->translate("Welcome"); ?>" <?php if (2 & $this->tabtype): ?>class="aaf_main_tabs_icon"<?php endif; ?> />
          <?php endif; ?>
          <?php if (2 & $this->tabtype): ?> 
            <span class="aaf_main_tabs_txt"><?php echo $this->translate("Welcome"); ?></span>
          <?php endif; ?>
        </a>
      </li>  
    <?php endif; ?>
    <?php if($this->isAaffeedEnable): ?>
    <li <?php if($this->activeTab==1): ?> class="aaf_tab_active" <?php endif; ?> id="Site_activityfeed"
     <?php if($this->tabtype == 1): ?> title="<?php echo
          $this->translate($this->settingsApi->getSetting('advancedactivity.sitetabtitle',
          "What's New!")) ?>" <?php endif; ?> >
      <a href="javascript:void(0);"   onclick="tabSwitchAAFContent($(this), 'aaffeed' );" >
        <span id="update_advfeed_blink" class="notification_star"></span>
        <?php if (1 & $this->tabtype): ?>

				<?php $logo_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity_icon',
				'application/modules/Advancedactivity/externals/images/web.png');
				$photoName = $this->baseUrl() . '/' . $logo_photo; ?>
        
          <img src="<?php echo $photoName ?>" alt="" title="<?php echo $this->translate($this->settingsApi->getSetting('advancedactivity.sitetabtitle', "What's New!")) ?>" <?php if (2 & $this->tabtype): ?>class="aaf_main_tabs_icon"<?php endif; ?> />
        <?php endif; ?>
        <?php if (2 & $this->tabtype): ?> 
          <span class="aaf_main_tabs_txt"><?php echo
$this->translate($this->settingsApi->getSetting('advancedactivity.sitetabtitle', "What's New!")); ?></span>
        <?php endif; ?>
      </a> 
    </li>
   <?php endif;?>
    <?php if ($this->isFacebookEnable && empty($this->FBloginURL) && !empty($this->web_values)): ?>
      <li <?php if($this->activeTab==3): ?> class="aaf_tab_active" <?php endif; ?> id="Facebook_activityfeed"
       <?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("Facebook"); ?>" <?php endif; ?> >
        <a href="javascript:void(0);" class='' onclick="javascript:tabSwitchAAFContent($(this), 'facebook' );" >
          <span id="update_advfeed_fbblink" class="notification_star"></span>
          <?php if (1 & $this->tabtype): ?> 
            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/facebook.png" alt="" title="<?php echo $this->translate("Facebook"); ?>" <?php if (2 & $this->tabtype): ?>class="aaf_main_tabs_icon"<?php endif; ?> />
          <?php endif; ?>
          <?php if (2 & $this->tabtype): ?> 
            <span class="aaf_main_tabs_txt"><?php echo $this->translate("Facebook"); ?></span>
          <?php endif; ?>
        </a>
      </li>  
    <?php endif; ?>
    
      <?php if ($this->isTwitterEnable && empty($this->TwitterLoginURL)  && !empty($this->web_values)): ?>
        <li <?php if($this->activeTab==2): ?> class="aaf_tab_active" <?php endif; ?> id="Twitter_activityfeed"
<?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("Twitter"); ?>" <?php endif; ?> >
          <a href="javascript:void(0);" class='' onclick="javascript:tabSwitchAAFContent($(this), 'twitter' );" >
            <span id="update_advfeed_tweetblink" class="notification_star"></span> 
            <?php if (1 & $this->tabtype): ?> 
              <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/twitter.png" alt="" title="<?php echo $this->translate("Twitter"); ?>" <?php if (2 & $this->tabtype): ?>class="aaf_main_tabs_icon"<?php endif; ?> />
            <?php endif; ?>
            <?php if (2 & $this->tabtype): ?> 
              <span class="aaf_main_tabs_txt"><?php echo $this->translate("Twitter"); ?></span>
          	<?php endif; ?>
          </a>
        </li>  
      <?php endif; ?>
      
       <?php if ($this->isLinkedinEnable && empty($this->LinkedinloginURL)  && !empty($this->web_values)): ?>
        <li <?php if($this->activeTab==5): ?> class="aaf_tab_active" <?php endif; ?> id="Linkedin_activityfeed"
<?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("LinkedIn"); ?>" <?php endif; ?> >
          <a href="javascript:void(0);" class='' onclick="javascript:tabSwitchAAFContent($(this), 'linkedin' );" >
            <span id="update_advfeed_linkedinblink" class="notification_star"></span> 
            <?php if (1 & $this->tabtype): ?> 
              <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/linkedin.png" alt="" title="<?php echo $this->translate("LinkedIn"); ?>" <?php if (2 & $this->tabtype): ?>class="aaf_main_tabs_icon"<?php endif; ?> />
            <?php endif; ?>
            <?php if (2 & $this->tabtype): ?> 
              <span class="aaf_main_tabs_txt"><?php echo $this->translate("LinkedIn"); ?></span>
          	<?php endif; ?>
          </a>
        </li>  
      <?php endif; ?>
      
     	<?php if ($this->isFacebookEnable && !empty($this->FBloginURL)  && !empty($this->web_values)): ?>
        <li <?php if($this->activeTab==3): ?> class="aaf_tab_active" <?php endif; ?>
id="Facebook_activityfeed" <?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("Facebook");
?>" <?php endif; ?> >
	        <a href="javascript:void(0);" onclick="AAF_ShowFeedDialogue_FB('<?php echo $this->FBloginURL; ?>')" title="<?php echo $this->translate("Connect to Facebook");?>">
	         	<?php if (1 & $this->tabtype && ! (2 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/facebook-add.png" alt="" />			 
	        	<?php elseif (2 & $this->tabtype && ! (1 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/add-app.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("Facebook"); ?></span>
					 	<?php else: ?>
					 		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/facebook-add.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("Facebook"); ?></span>
					 	<?php endif; ?>
	        </a>
        </li>  
     	<?php endif; ?>
     
      <?php if ($this->isTwitterEnable &&!empty($this->TwitterLoginURL) && !empty($this->web_values) && in_array("twitter", $this->web_values)): ?>
        <li <?php if($this->activeTab==2): ?> class="aaf_tab_active" <?php endif; ?>
id="Twitter_activityfeed" <?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("Twitter");
?>" <?php endif; ?> >
      	  <a href="javascript:void(0);" onclick="AAF_ShowFeedDialogue_Tweet('<?php echo $this->TwitterLoginURL; ?>')" title="<?php echo $this->translate("Connect to Twitter");?>">
	        	<?php if (1 & $this->tabtype && ! (2 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/twitter-add.png" alt="" />			 
	        	<?php elseif (2 & $this->tabtype && ! (1 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/add-app.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("Twitter"); ?></span>
					 	<?php else: ?>
					 		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/twitter-add.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("Twitter"); ?></span>
					 	<?php endif; ?>
	        </a>
        </li>
       
      <?php endif; ?>
      
      
      <?php if ($this->isLinkedinEnable &&!empty($this->isLinkedinEnable) && !empty($this->LinkedinloginURL) && !empty($this->web_values) && in_array("linkedin", $this->web_values)): ?>
        <li <?php if($this->activeTab==5): ?> class="aaf_tab_active" <?php endif; ?>
id="Linkedin_activityfeed" <?php if($this->tabtype == 1): ?> title="<?php echo $this->translate("LinkedIn");
?>" <?php endif; ?> >
      	  <a href="javascript:void(0);" onclick="AAF_ShowFeedDialogue_Linkedin('<?php echo $this->LinkedinloginURL; ?>')" title="<?php echo $this->translate("Connect to LinkedIn");?>">
	        	<?php if (1 & $this->tabtype && ! (2 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/linkedin-add.png" alt="" />			 
	        	<?php elseif (2 & $this->tabtype && ! (1 & $this->tabtype)): ?> 
	        		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/add-app.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("LinkedIn"); ?></span>
					 	<?php else: ?>
					 		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/linkedin-add.png" alt="" class="aaf_main_tabs_icon" />
					 		<span class="aaf_main_tabs_txt"><?php echo $this->translate("LinkedIn"); ?></span>
					 	<?php endif; ?>
	        </a>
        </li>
       
      <?php endif; ?>
		<li class="aaf_apps_op_wrapper">
			<div class="aaf_apps_ops_cont">
					<div class="aaf_apps_ops" id="aaf_main_tab_refresh" style="display: none;" >
					<span onclick="showDefaultContent();" title="<?php echo $this->translate("Refresh") ?>" ><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/refresh.png' alt="Refresh" align="left" /></span>
				</div>	
				<div class="aaf_apps_ops" id="aaf_main_tab_logout" style="display:none;">
					<span title="<?php echo $this->translate("Logout"); ?>"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/logout.png' alt="Logout" align="left" /></span>
				</div>
			</div>	      
		</li>
  </ul>
<?php endif; ?>
</div>

<?php if($this->showScrollTopButton): ?>
 <a id="back_to_top_feed_button" href="#" class="seaocore_up_button Offscreen" title="<?php echo $this->translate("Scroll to Top"); ?>">
 		<span></span>
 </a>
 <?php endif; ?>

<script type="text/javascript"> 
 var is_enter_submitothersocial = "<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.comment.show.bottom.post', 1); ?>";
 var autoScrollFeedAAFEnable="<?php echo $this->autoScrollFeedEnable?true:false; ?>";
  var feedToolTipAAFEnable="<?php if( !empty($this->composerType) ){ echo $this->feedToolTipEnable?true:false; }else { echo ''; } ?>";
  var maxAutoScrollAAF="<?php echo $this->maxAutoScrollFeed ?>";
  var is_welcomeTab_default = 1;
  var  current_window_url='<?php echo (_ENGINE_SSL ? 'https://' : 'http://' )
                    . $_SERVER['HTTP_HOST'].$this->url() ?>';
  var activity_type='<?php echo $this->activeTab; ?>';
 var moreADVHideEventEnable=false;
 <?php if($this->showScrollTopButton): ?>
window.addEvent('scroll', function (){
  var element=$("back_to_top_feed_button");  
  if(!element)
    return;
  if( typeof( $('aaf_main_tabs_feed').offsetParent ) != 'undefined' ) {
    var elementPostionY=$('aaf_main_tabs_feed').offsetTop;
  }else{
    var elementPostionY=$('aaf_main_tabs_feed').y; 
  }
  if(elementPostionY + window.getSize().y < window.getScrollTop()){
    if(element.hasClass('Offscreen'))
      element.removeClass('Offscreen');
  }else if(!element.hasClass('Offscreen')){       
    element.addClass('Offscreen');
  }
});
<?php endif; ?>
 en4.core.runonce.add(function () {
   <?php if($this->showScrollTopButton): ?>
   <?php
   $request = Zend_Controller_Front::getInstance()->getRequest();
   // Get body identity
   if (isset($this->layout()->siteinfo['identity'])) {
     $identity = $this->layout()->siteinfo['identity'];
   } else {
     $identity = $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName();
   }
   ?>
  var scroll = new Fx.Scroll('global_page_<?php echo $identity ?>', {
    wait: false,
    duration: 750,
    offset: {'x': -200, 'y': -100},
    transition: Fx.Transitions.Quad.easeInOut
  });

  $('back_to_top_feed_button').addEvent('click', function(event) {
    event = new Event(event).stop();
    scroll.toElement('aaf_main_tabs_feed');       
  });
  <?php endif; ?>
   <?php if(!empty($this->action_id)): ?>
    aaf_feed_actionId=<?php echo $this->action_id ?>;
    $$(".tab_<?php echo $this->identity ?>").each(function(element){
      if( element.tagName.toLowerCase() == 'li' ) {
       tabContainerSwitch(element);
      }
    });
   
   <?php endif; ?>
   <?php if(!empty($this->viewAllLikes)): ?>
    show_likes=<?php echo $this->viewAllLikes ?>;   
   <?php endif; ?>
   <?php if(!empty($this->viewAllComments)): ?>
    show_comments=<?php echo $this->viewAllComments ?>;   
   <?php endif; ?>
   //showDefaultContent();
 setContentAfterLoad(activity_type);
  var moreADVHideClickEvent=function() {
    if(!moreADVHideEventEnable)
      $$(".aaf_pulldown_btn_wrapper").removeClass('aaf_tabs_feed_tab_open').addClass('aaf_tabs_feed_tab_closed');
    moreADVHideEventEnable=false; 
  }
  //hide on body clicdk
  $(document.body).addEvent('click',moreADVHideClickEvent.bind());

    });

 <?php if (!empty($this->FBloginURL)) : ?>
 fb_loginURL = '<?php echo $this->FBloginURL;?>';
 
 <?php endif;?>
 
 <?php if (!empty($this->TwitterLoginURL)) : ?>
 tweet_loginURL = '<?php echo $this->TwitterLoginURL;?>';
 
 <?php endif;?>
 
 <?php if (!empty($this->LinkedinloginURL)) : ?>
 linkedin_loginURL = '<?php echo $this->LinkedinloginURL;?>';
 
 <?php endif;?>
 
 <?php if (!empty($this->FBloginURL_temp)) : ?>
 fb_loginURL_temp = '<?php echo $this->FBloginURL_temp;?>';
 
 <?php endif;?>
 
 <?php if (!empty($this->TwitterLoginURL_temp)) : ?>
 tweet_loginURL_temp = '<?php echo $this->TwitterLoginURL_temp;?>';
 
 <?php endif;?>
 
  <?php if (!empty($this->LinkedinloginURL_temp)) : ?>
 linkedin_loginURL_temp = '<?php echo $this->LinkedinloginURL_temp;?>';
 
 <?php endif;?>
 
 
if (window.opener!= null) { 
<?php if (!empty($_GET['redirect_fb'])) : ?>
    
    if ($type(window.opener.$('compose-facebook-form-input'))) {
      window.opener.$('compose-facebook-form-input').disabled = '';
    }
    
   if (window.opener.aaf_feed_type_tmp == 3) { 
     
    if($type(window.opener.$('aaf_main_contener_feed_3')) ){ 
      window.opener.showDefaultContent();
      window.opener.action_logout_taken_fb = 0;
      if (window.opener.$('aaf_main_tab_logout'))
        window.opener.$('aaf_main_tab_logout').style.display = 'block';
      if (window.opener.$('aaf_main_tab_refresh'))  
          window.opener.$('aaf_main_tab_refresh').style.display = 'block';  
      
      if (fb_loginURL == '') {
        if ($type(window.opener.$('Facebook_activityfeed'))) {
          window.opener.$('Facebook_activityfeed').innerHTML = $('Facebook_activityfeed').innerHTML;
       }
      }
    }
    else  {  
     if ($type(window.opener.$('Facebook_activityfeed'))) {
        window.opener.$('Facebook_activityfeed').innerHTML = $('Facebook_activityfeed').innerHTML;
      }
      window.opener.tabSwitchAAFContent(window.opener.$('Facebook_activityfeed'), 'facebook' );
    }
    window.opener.fb_loginURL = '';        
  }
  else {
    if (fb_loginURL == '') {  
        window.opener.$('compose-facebook-form-input').set('checked', ! window.opener.$('compose-facebook-form-input').get('checked'));
        window.opener.$('composer_facebook_toggle').removeClass('composer_facebook_toggle_active');
        window.opener.$('composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
        var spanelement = window.opener.$('composer_facebook_toggle').getElement('.aaf_composer_tooltip'); 
					spanelement.innerHTML = en4.core.language.translate('Do not publish this on Facebook') + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />';
        if ($type(window.opener.$('Facebook_activityfeed'))) {
          window.opener.$('Facebook_activityfeed').innerHTML = $('Facebook_activityfeed').innerHTML;
       }
     window.opener.fb_loginURL = '';
     window.opener.action_logout_taken_fb = 0;  
    }
    
  }
  
   close();
 
 <?php endif;?>
 
 <?php if (!empty($_GET['redirect_linkedin'])) : ?>
    
    if ($type(window.opener.$('compose-linkedin-form-input'))) {
      window.opener.$('compose-linkedin-form-input').disabled = '';
    }
    
   if (window.opener.aaf_feed_type_tmp == 5) { 
     
    if($type(window.opener.$('aaf_main_contener_feed_5')) ) { 
      window.opener.showDefaultContent();
      window.opener.action_logout_taken_linkedin = 0;
      if (window.opener.$('aaf_main_tab_logout'))
        window.opener.$('aaf_main_tab_logout').style.display = 'block';
      if (window.opener.$('aaf_main_tab_refresh'))  
          window.opener.$('aaf_main_tab_refresh').style.display = 'block';  
      
      if (linkedin_loginURL == '') {
        if ($type(window.opener.$('Linkedin_activityfeed'))) {
          window.opener.$('Linkedin_activityfeed').innerHTML = $('Linkedin_activityfeed').innerHTML;
       }
      }
    }
    else  {  
     if ($type(window.opener.$('Linkedin_activityfeed'))) {
        window.opener.$('Linkedin_activityfeed').innerHTML = $('Linkedin_activityfeed').innerHTML;
      }
      window.opener.tabSwitchAAFContent(window.opener.$('Linkedin_activityfeed'), 'linkedin' );
    }
    window.opener.linkedin_loginURL = '';        
  }
  else {
    if (linkedin_loginURL == '') {
        window.opener.$('compose-linkedin-form-input').set('checked', ! window.opener.$('compose-linkedin-form-input').get('checked'));
         window.opener.$('composer_linkedin_toggle').removeClass('composer_linkedin_toggle_active');
        window.opener.$('composer_linkedin_toggle').toggleClass('composer_linkedin_toggle_active');
        var spanelement = window.opener.$('composer_linkedin_toggle').getElement('.aaf_composer_tooltip');
					spanelement.innerHTML = en4.core.language.translate('Do not publish this on LinkedIn') + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />';
        if ($type(window.opener.$('Linkedin_activityfeed'))) {
          window.opener.$('Linkedin_activityfeed').innerHTML = $('Linkedin_activityfeed').innerHTML;
       }
     window.opener.linkedin_loginURL = '';
     window.opener.action_logout_taken_linkedin = 0;  
    }
    
  }
  
   close();
 
 <?php endif;?>
 
 <?php if (!empty($_GET['redirect_tweet'])) : ?>
   
   if ($type(window.opener.$('compose-twitter-form-input'))) {
      window.opener.$('compose-twitter-form-input').disabled = '';
    }
    window.opener.tweet_loginURL = '';
   if (window.opener.aaf_feed_type_tmp == 2) { 
    
    if ($type(window.opener.$('compose-twitter-form-input'))) {
      window.opener.$('compose-twitter-form-input').disabled = '';
    }  
    
    window.opener.tweet_loginURL = '';
    if($type(window.opener.$('aaf_main_contener_feed_2')) ){ 
      window.opener.showDefaultContent();
      window.opener.action_logout_taken_tweet = 0;
      if (window.opener.$('aaf_main_tab_logout'))
        window.opener.$('aaf_main_tab_logout').style.display = 'block';
      if (window.opener.$('aaf_main_tab_refresh'))  
          window.opener.$('aaf_main_tab_refresh').style.display = 'block';
          if ($type(window.opener.$('Twitter_activityfeed'))) {  
        window.opener.$('Twitter_activityfeed').innerHTML = $('Twitter_activityfeed').innerHTML;
      }     
    }
    else { 
      if ($type(window.opener.$('Twitter_activityfeed'))) {  
        window.opener.$('Twitter_activityfeed').innerHTML = $('Twitter_activityfeed').innerHTML;
      }
      window.opener.tabSwitchAAFContent(window.opener.$('Twitter_activityfeed'), 'twitter' );
    }
  }
  else {
    if (tweet_loginURL == '') { 
        window.opener.$('compose-twitter-form-input').set('checked', !window.opener.$('compose-twitter-form-input').get('checked'));
        window.opener.$('composer_twitter_toggle').removeClass('composer_twitter_toggle_active');
        window.opener.$('composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
        var spanelement = window.opener.$('composer_twitter_toggle').getElement('.aaf_composer_tooltip');
					spanelement.innerHTML = en4.core.language.translate('Do not publish this on Twitter') + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />';
        if ($type(window.opener.$('Twitter_activityfeed'))) {  
          window.opener.$('Twitter_activityfeed').innerHTML = $('Twitter_activityfeed').innerHTML;
      }
       }
       window.opener.tweet_loginURL = '';
       window.opener.action_logout_taken_tweet = 0;
    }
  
 
  close();
   <?php endif;?>
}
 </script>
 <?php 
 if ($this->enableComposer):
  if ($this->isMobile):
    echo $this->partial('_aafcomposermobile.tpl', 'advancedactivity', array(
        'enableComposer' => $this->enableComposer,
        'showPrivacyDropdown' => $this->showPrivacyDropdown,
        'enableList' => $this->enableList,
        'lists' => $this->lists,
        'countList' => $this->countList,
        'composePartials' => $this->composePartials,
        'settingsApi' => $this->settingsApi,
        'availableLabels' => $this->availableLabels,
        'showDefaultInPrivacyDropdown' => $this->showDefaultInPrivacyDropdown,
        'privacylists' => $this->privacylists,
        'formToken' => $this->formToken,
        'enableNetworkList'=>$this->enableNetworkList,
        'network_lists'=>$this->network_lists,
        'showDefault' =>  true 
    ));
  else:
    echo $this->partial('_aafcomposer.tpl', 'advancedactivity', array(
        'enableComposer' => $this->enableComposer,
        'showPrivacyDropdown' => $this->showPrivacyDropdown,
        'enableList' => $this->enableList,
        'lists' => $this->lists,
        'countList' => $this->countList,
        'composePartials' => $this->composePartials,
        'settingsApi' => $this->settingsApi,
        'availableLabels' => $this->availableLabels,
        'showDefaultInPrivacyDropdown' => $this->showDefaultInPrivacyDropdown,
        'privacylists' => $this->privacylists,
        'formToken' => $this->formToken,
        'enableNetworkList'=>$this->enableNetworkList,
        'network_lists'=>$this->network_lists,
        'showDefault' => $this->activeTab == 1 ? true : false
    ));
  endif;
endif;
 ?>
 
 <div id="adv_activityfeed">   
   <div id="aaf_main_container_lodding" style="display: none;">
     <div class="aaf_main_container_lodding"></div>
   </div>   
   <div id="aaf_main_contener_feed_<?php echo $this->activeTab ?>">
     <?php if ($this->activeTab == 1): ?>
       <?php echo $this->content()->renderWidget("advancedactivity.feed", array("homefeed" => 1, "action_id" => $this->action_id, "show_likes" => $this->viewAllLikes, "show_comments" => $this->viewAllComments, "subject" => $this->subjectGuid)); ?>
     <?php elseif ($this->activeTab == 2): ?>
       <?php echo $this->content()->renderWidget("advancedactivity.advancedactivitytwitter-userfeed", array("homefeed" => 1, "subject" => $this->subjectGuid)); ?>
     <?php elseif ($this->activeTab == 3): ?>
       
       <?php echo $this->content()->renderWidget("advancedactivity.advancedactivityfacebook-userfeed", array("homefeed" => 1, "subject" => $this->subjectGuid)); ?>
    <?php elseif ($this->activeTab == 5): ?>
       
       <?php echo $this->content()->renderWidget("advancedactivity.advancedactivitylinkedin-userfeed", array("homefeed" => 1, "subject" => $this->subjectGuid)); ?>
     <?php elseif ($this->activeTab == 4): ?>
       <?php echo $this->content('advancedactivity_index_welcometab'); ?>
     <?php endif; ?>
   </div>
 </div>
