<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('Text', 'advancedactivity_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'required' => true,
        'value' => $coreSettingsApi->getSetting('advancedactivity.lsettings'),
    ));

    if (APPLICATION_ENV == 'production') {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $settings = $coreSettingsApi;
    $this->addElement('Radio', 'advancedactivity_post_byajax', array(
        'label' => 'Status Update via AJAX',
        'description' => "Do you want to enable posting of status updates via AJAX? (Note: Select 'No' if you have either enabled Janrain Integration with 'Publish' feature or are using Social DNA Publisher plugin. Status updates from Facebook, Twitter or LinkedIn tabs will always be via AJAX, even if you select No over here.)?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.post.byajax', 1),
    ));

    // Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    $this->addElement('Radio', 'advancedactivity_post_canedit', array(
        'label' => 'Edit Privacy for Status Update Posts',
        'description' => "Do you want users to be able to edit privacy for their status update posts? (Note: If you select ‘Yes’ over here, then the option to change status update post privacy will be shown on member profile page activity feeds in the dropdown alongside the post.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.post.canedit', 1),
    ));

    $this->addElement('Text', 'advancedactivity_sitetabtitle', array(
        'label' => 'Site Feeds Tab Title',
        'description' => "Enter the title for the tab that displays site activity feeds in the Advanced Activity Feeds.",
        'value' => $settings->getSetting('advancedactivity.sitetabtitle', "What's New!"),
    ));



    $this->addElement('Radio', 'advancedactivity_tabtype', array(
        'label' => 'Tab Types',
        'description' => 'Select the design type for the tabs in Advanced Activity Feeds. (These tabs enable users to switch between Welcome tab, feeds from your website and feeds from Facebook, Twitter and LinkedIn. Below, you will be able to choose the icon for your site\'s tab. In the Advanced Activity Feeds widget, for any placement, you can choose the tabs/sections to be available via the Edit Settings popup for the widget. The tabs are visible only if more than 1 sections are selected. On Content Profile/View pages, the Welcome, Facebook, Twitter and LinkedIn tabs will not be shown even if they are enabled and tabs will not appear there.)',
        'multiOptions' => array(
            1 => 'Icon Only',
            3 => 'Icon and Title',
            2 => 'Title Only'
        ),
        'onclick' => 'showiconOptions(this.value)',
        'value' => $settings->getSetting('advancedactivity.tabtype', 3),
    ));



    // Get available files (Icon for activity Feed).
    $logoOptions = array('application/modules/Advancedactivity/externals/images/web.png' => 'Default Icon');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($it as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $basename = basename($file->getFilename());
      if (!($pos = strrpos($basename, '.')))
        continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if (!in_array($ext, $imageExtensions))
        continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }

    $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have not
     uploaded an image for site logo. Please upload an image.") . "</span></div>";

//     $this->addElement('Dummy', 'advancedactivity_linkfor_icon', array(
//         'label' => 'Upload Small Site Icon',
//         'description' => "<a href='" . $view->baseUrl() . "/admin/files' target='_blank'>" .
//Zend_Registry::get('Zend_Translate')->_('Go to this link and choose icon.') . "</a>",
//     ));
//
//$this->getElement('advancedactivity_linkfor_icon')->getDecorator('Description')->setOptions(array(
//'placement' , 'APPEND', 'escape' => false));

    $URL = $view->baseUrl() . "/admin/files";
    $click = '<a href="' . $URL . '" target="_blank">over here</a>';
    $customBlocks = sprintf("Upload a small icon for your website %s. The ideal dimensions of this icon should be: 16 X 16 px. (This icon will be shown for your site's activity feeds tab in Advanced Activity Feeds. Once you upload a new icon at the link mentioned, then refresh this page to see its preview below after selection.)", $click);

    if (!empty($logoOptions)) {
      $this->addElement('Select', 'advancedactivity_icon', array(
          'label' => 'Choose Small Site Icon',
          'description' => $customBlocks,
          'multiOptions' => $logoOptions,
          'onchange' => "updateTextFields(this.value)",
          'value' => $settings->getSetting('advancedactivity.icon', ''),
      ));
      $this->getElement('advancedactivity_icon')->getDecorator('Description')->setOptions(array('placement' =>
          'PREPEND', 'escape' => false));
    }
    $logo_photo = $coreSettingsApi->getSetting('advancedactivity_icon', 'application/modules/Advancedactivity/externals/images/web.png');
    if (!empty($logo_photo)) {

      $photoName = $view->baseUrl() . '/' . $logo_photo;
      $description = "<img src='$photoName' width='20' height='20'/>";
    }
    //VALUE FOR LOGO PREVIEW.
    $this->addElement('Dummy', 'logo_photo_preview', array(
        'label' => 'Site Icon Preview',
        'description' => $description,
    ));
    $this->logo_photo_preview
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));






    // Get available files (Icon for activity Feed).
    $logoOptions1 = array('application/modules/Advancedactivity/externals/images/welcome-icon.png' =>
        'Default Icon');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($it as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $basename = basename($file->getFilename());
      if (!($pos = strrpos($basename, '.')))
        continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if (!in_array($ext, $imageExtensions))
        continue;
      $logoOptions1['public/admin/' . $basename] = $basename;
    }

    //WELCOME ICON WORK
    $click = '<a href="' . $URL . '" target="_blank">over here</a>';
    $customBlocks = sprintf('Upload a small icon for the Welcome Tab %s. The ideal dimensions of this icon should be: 16 X 16 px. (Once you upload a new icon at the link mentioned, then refresh this page to see its preview below after selection.)', $click);

    if (!empty($logoOptions1)) {
      $this->addElement('Select', 'advancedactivity_icon1', array(
          'label' => 'Choose Welcome Tab Icon',
          'description' => $customBlocks,
          'multiOptions' => $logoOptions1,
          'onchange' => "updateTextFields1(this.value)",
          'value' => $settings->getSetting('advancedactivity.icon1', ''),
      ));

      $this->getElement('advancedactivity_icon1')->getDecorator('Description')->setOptions(array('placement'
          => 'PREPEND', 'escape' => false));
    }
    $logo_photo1 = $coreSettingsApi->getSetting('advancedactivity_icon1', 'application/modules/Advancedactivity/externals/images/welcome-icon.png');
    if (!empty($logo_photo1)) {

      $photoName1 = $view->baseUrl() . '/' . $logo_photo1;
      $description1 = "<img src='$photoName1' width='20' height='20'/>";
    }
    //VALUE FOR LOGO PREVIEW.
    $this->addElement('Dummy', 'logo_photo_preview1', array(
        'label' => 'Welcome Tab Icon Preview',
        'description' => $description1,
    ));
    $this->logo_photo_preview1
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' =>
                false));
    //WELCOME ICON WORK
    //info tooltips
    $this->addElement('Radio', 'advancedactivity_info_tooltips', array(
        'label' => 'Info Tooltips',
        'description' => "Do you want to enable interactive Info Tooltips on mouse-over for sources and entities in the site activity feeds in Advanced Activity Feeds? (The interactive Info Tooltips contain information and quick action links for the entities. You can choose more settings for these from the Info Tooltip Settings.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.info.tooltips', 1),
    ));

    $this->addElement('Radio', 'advancedactivity_comment_show_bottom_post', array(
        'label' => 'Quick Comment Box',
        'description' => 'Do you want to show a "Post a comment" box for activity feeds that have comments? (Enabling this can increase interactivity via comments on activity feeds on your site. Users will be able to quickly post a comment on activity feeds just by pressing the "Enter" key.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.comment.show.bottom.post', 1),
    ));

    $this->addElement('Radio', 'advancedactivity_scroll_autoload', array(
        'label' => 'Auto-Loading Activity Feeds On-scroll',
        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds? (This setting will apply to the site activity feeds as well as those from Facebook, Twitter and LinkedIn.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onchange' => 'switchContentField(this.value,"advancedactivity_maxautoload")',
        'value' => $coreSettingsApi->getSetting('advancedactivity.scroll.autoload', 1),
    ));
    $this->addElement('Select', 'advancedactivity_maxautoload', array(
        'label' => "Auto-Loading Count",
        'description' => 'Select the number of times that auto-loading of old activity feeds should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
        'multiOptions' => array(
            '0' => '0',
            '1' => "1",
            '2' => '2',
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
            "10" => "10"
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.maxautoload', 0),
    ));

    $composerLink = 'http://www.socialengineaddons.com/socialengine-directory-pages-plugin';
    //WELCOME ICON WORK
    $pageitem = "<a href= '" . $composerLink . "' target='_blank'>Directory Items / Pages</a>";
    $composerOptionDes = sprintf("Choose the options from below that you want to be enabled for the Status Update Box in site activity feeds. (Additionally, using the “@” character with name, users will be able to tag their Friends and site’s %s in their updates on Member Homepage and User Profiles. Tagged friends and Page Admins of tagged %s will get notification updates.)", $pageitem, $pageitem);



    // VALUE FOR AJAX LAYOUT
    $this->addElement('MultiCheckbox', 'advancedactivity_composer_options', array(
        'description' => $composerOptionDes,
        'label' => 'Status Box Options',
        'multiOptions' => array(
            "withtags" => "Add Friends (Users will be able to add friends in their updates. These friends will appear in the updates with a “with” text. Added friends will get a notification update. If enabled, this will be available on Member Homepage and User Profiles.)",
            "emotions" => "Emoticons (Users will see an \"Insert Emoticons\" icon in the updates posting box and will be able to insert attractive Emoticons / Smileys in their posts. Symbols for smileys entered in comments of activity feeds will also be displayed as their emoticons. If enabled, this will be available at all locations of the status box and comments on feeds.)",
            "userprivacy" =>  'Post Sharing Privacy (Users will be able to choose the people and networks with whom they want to share their updates. The available sharing privacy options come pre-configured with "Everyone", "Friends & Networks" and "Friends Only", and Friend Lists created by users like "Family", "Work Colleagues", etc are also shown. You can also enable users to share their updates within certain Network(s) by choosing the desired option for \'Networks Post Sharing Privacy\' field below. Additionally, users can also create custom sharing lists using their Friend Lists and Networks. If enabled, this will be available on Member Homepage and User Profiles.)',
            "webcam" => "Add Photo Using Webcam (Users will be able to add photo using their webcam in their updates. If enabled, this will be available in all locations of the status box with the 'Select File' option.)"
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy", "webcam")),
    ));
    $this->advancedactivity_composer_options
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' =>
                false));

    $content = $coreSettingsApi->getSetting('activity.content', 'everyone');
    $tip = null;
    if ($content == 'friends') {
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $link = $view->url(array("module" => "activity", "controller" => "settings"), "admin_default", true);
      $tip = sprintf("<div class='tip'> <span>Note: In Activity Feed Settings, you have chosen ‘My Friends’ for the ‘Feed Content’ field. Please %s, either to choose ‘All Members’ or ‘My Friends & Networks’ for this field.</span></div>", "<a href='" . $link . "'>click here</a>");
    }
    // VALUE FOR ENABLE /DISABLE Proximity Search
    $this->addElement('Radio', 'advancedactivity_networklist_privacy', array(
        'label' => 'Networks Post Sharing Privacy',
        'description' => 'Do you want to enable users to share their updates with desired Networks? (If yes, choose an appropriate value below.)'.$tip,
        'multiOptions' => array(
            2 => 'Yes, enable users to share their updates with all networks.',
            1 => 'Yes, enable users to share their updates only with the networks joined by them.',
            0 => 'No'
        ),
        'value' => $settings->getSetting('advancedactivity.networklist.privacy', 0),
    ));
     $this->getElement('advancedactivity_networklist_privacy')->getDecorator('Description')->setOptions(array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    // Searchable Media
    $this->addElement('Radio', 'advancedactivity_post_searchable', array(
        'label' => 'Shared Media Searchability',
        'description' => "Do you want the media that is shared from the Status Update Box to be made searchable?",
        'multiOptions' => array(
            1 => "Yes (Note: In this case, the shared media will also appear on the browse page of the shared content type. Thus, even though privacy chosen during sharing [like Friends, Family, etc] will work on the main content page for viewing content, the shared media content will be visible on the browse page because you have made it searchable.)"
            ,
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('advancedactivity.post.searchable', 0),
    ));
    $multiOptions = array('friends' => 'Friends');
    $include = array('sitepage', 'sitebusiness', 'list', 'group', 'event');
    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type =?', 'extra')
            ->where($module_name . '.name in(?)', $include)
            ->where($module_name . '.enabled =?', 1);

    $contentModule = $select->query()->fetchAll();
    $include[] = 'friends';
    foreach ($contentModule as $module) {
      $multiOptions[$module['name']] = $module['title'];
    }
    $this->addElement('MultiCheckbox', 'aaf_tagging_module', array(
        'label' => 'Tag Contents',
        'description' => "Which type of content should be tagged?",
        'multiOptions' => $multiOptions,
        'value' => $coreSettingsApi->getSetting('aaf.tagging.module', $include)
    ));
    $this->addElement('Radio', 'aaf_largephoto_enable', array(
        'label' => 'Bigger Photo Size in Feeds',
        'description' => 'Do you want to enable bigger photo size and improve alignment of photos in activity feeds? (Note: This setting will not affect the photos uploaded / shared from "SocialEngine Photo Albums Plugin" and our "Advanced Photo Albums Plugin"; photos from these two will always appear bigger.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('aaf.largephoto.enable', 1),
    ));


    //Third Party Services Settings
    $this->addElement('Dummy', 'thirdparty_settings', array(
        'label' => 'Third Party Services Settings',
    ));
// VALUE FOR AJAX LAYOUT
    /*   $this->addElement('MultiCheckbox', 'advancedactivity_fb_twitter', array(
      'description' => 'Select the third party services that you want to be available on Member Home
      Activity Feed.',
      'label' => 'Third Party Services',
      'multiOptions' => array("facebook" => "Facebook", "twitter" => "Twitter"),
      'value' =>$coreSettingsApi->getSetting('advancedactivity.fb.twitter',
      array("0" => "facebook", "1" => "twitter")),
      ));
     */

    $this->addElement('Select', 'advancedactivity_update_frequency', array(
        'label' => 'Update Frequency for Facebook, Twitter and LinkedIn Feeds',
        'description' => 'This application connects to the respective third-party (using AJAX) after regular intervals to check if there are any new updates to the corresponding activity feed. How often do you want this process to occur? A shorter amount of time will consume more server resources. If your server is experiencing slowdown issues, try lowering the frequency the application checks for updates.',
        'value' => $coreSettingsApi->getSetting('advancedactivity.update.frequency', 120000),
        'multiOptions' => array(
            60000 => '1 minute',
            120000 => "2 minutes",
            180000 => "3 minutes",
            0 => 'Never'
        )
    ));

    $this->addElement('Dummy', 'linkedin_settings_temp', array(
        'label' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formcontactimport.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Radio', 'facebook_enable', array(
        'label' => 'Publish to Facebook',
        'description' => "Do you want to integrate the 'Publish to Facebook' feature for status updates?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish'? 1:0),
    ));
    
    $this->addElement('Radio', 'twitter_enable', array(
        'label' => 'Publish to Twitter',
        'description' => "Do you want to integrate the 'Publish to Twitter' feature for status updates?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('twitter.enable', Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable == 'publish'? 1:0),
    ));
    
    $this->addElement('Radio', 'linkedin_enable', array(
        'label' => 'Publish to LinkedIn',
        'description' => "Do you want to integrate the 'Publish to LinkedIn' feature for status updates?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('linkedin.enable', 0),
    ));


    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>
