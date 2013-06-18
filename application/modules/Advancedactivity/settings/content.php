<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: content.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$final_array =  array(
    array(
        'title' => $view->translate('Advanced Activity Feeds'),
        'description' => $view->translate('Displays the advanced activity feeds on your site. This widget facilitates you to enable any of the 5 available tabs: Welcome, Site Activity Feeds, Facebook Feeds, Twitter Feeds and Linkedin Feeds at various widget locations. On content profile / view pages, in site activity feeds, the feeds for respective content profile will show, whereas at other locations of this widget, overall site activity feeds will show. The Welcome, Facebook, Twitter and Linkedin tabs will not be shown on the content profile / view pages even if they are enabled. Facebook, Twitter and Linkedin tabs will show the logged-in user\'s Facebook, Twitter and Linkedin feeds. It is recommended to place this widget where SocialEngine\'s Core Activity Feed widget is placed.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.home-feeds',
        'defaultParams' => array(
            'title' => 'What\'s New',
            'advancedactivity_tabs'=>array("aaffeed")
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    "Text",
                    "title",
                    array(
                        'label' => 'Title',
                        'value' => 'What\'s New',
                    )
                ),
                array(
                    "MultiCheckbox",
                    "advancedactivity_tabs",
                    array(
                        'description' => 'Select the tabs that you want to be available in this block.',
                        'label' => 'Tabs',
                        'multiOptions' => array("welcome" => "Welcome", "aaffeed" => "Site Activity Feeds",
                           "facebook" => "Facebook Feeds", "twitter" => "Twitter Feeds", "linkedin" => "LinkedIn Feeds"),                       
                    )
                ),
                 array(
                    'Radio',
                    'showScrollTopButton',
                    array(
                        'label' => $view->translate('Scroll to Top Button'),
                        'description'=>$view->translate('Do you want the "Scroll to Top" button to be displayed for this Activity Feeds block? (As a user scrolls down to see more Activity Feeds from this widget, the "Scroll to Top" button will be shown in the bottom-right side of the screen, enabling user to easily move to the top.)'),
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Welcome: Search for People'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block shows to users a search field to search for their friends who might be members of the site. This enables them to easily and quickly add them as friends to grow their network on your site.'), 
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.search-for-people',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Profile Photo Uploading'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block will enable users to easily and quickly upload a profile photo, thus increasing trust on your website.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.profile-photo',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Custom Blocks'),
        'description' => $view->translate("This is a widget for the Welcome Tab. You can use custom blocks to show welcome content to users which is different from the already available blocks. For example, you can introduce those features / aspects of your website that form your site's most important core features. To manage content of this widget, please go to the Custom Blocks tab in Welcome Settings of Advanced Activity Feeds plugin."),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.custom-block',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Welcome Message'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block shows to users a welcome message with their name in it, thus increasing personalization on your website.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.welcome-message',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
 );

return $final_array;
?>
