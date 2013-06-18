<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: faq_help.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">	
   
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q: I have placed Advanced Activity Feeds widget on Content Profile / View Pages and enabled Welcome, Facebook and Twitter tabs there, but only the site activity feeds are getting displayed and no tabs are coming. What could be the reason ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: The Welcome, Facebook and Twitter tabs will not be shown on the content profile / view pages even if they are enabled for the widget location. On these pages, only the feeds for respective content profile will be shows."); ?> 
        </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q: I have placed all the widgets in the Welcome tab page but Welcome tab is not being shown in the Advanced Activity Feeds widget. What might be the reason ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: The Welcome tab will not be shown in the Advanced Activity Feeds widget for a user if none of the conditions configured by you for the blocks in it are being satisfied. You may edit these conditions from the "Welcome Settings" section of this plugin.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q: The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Q: I am performing lots of actions on my site, but the activity feeds of those actions are not shown in the 'all updates' section of the Advanced Activity Feeds. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("Ans: To show activity feeds of all the actions performed by you, please go to the 'Activity Feeds Settings' section of this plugin and find the field 'Item Limit Per User'. Now, enter the value for number of feeds per user you want to be displayed in the 'all updates' section. (Note : To have a nice mix of feeds from various users on your site, it is recommended to put a value less than 10.)"); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q: While updating status on my site, I selected the option to publish the status updates on twitter also, but my updates are not shown in my twitter timeline. Why is it happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: This is happening because you might have not given the 'Read and Write' permission while creating your application on twitter. To give the permission now, please go to <a href='https://dev.twitter.com/apps' target='_blank'> 'https://dev.twitter.com/apps/' </a> and select your application. Now, search for the field 'Application Type' in the settings section of your application. Selecting 'Read and write' value for this field will enable you to publish your status updates on twitter from your site."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q: I have selected the 'feed content' privacy on my site to 'friends only' but some of my feeds are still shown to the members who are not my friends when they visit my profile page. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: This is happening because while updating the status you might have chosen the privacy to 'Everyone' because of which feeds posted with privacy 'Everyone' will be visible to all the users when they visit your profile."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('Q: I want to enable / disable the "Scroll to Top" button for the Advanced Activity Feeds widget. What should I do?'); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: To do so, please go to the Layout Editor and click on the 'edit' link of the 'Advanced Activity Feeds' widget for the location where you want to enable / disable the 'Scroll to Top' button. Now, from the settings form popup of this widget, enable / disable the 'Scroll to Top Button' setting as per your requirement."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate('Q: I have enabled "Welcome" tab in the "Advanced Activity Feeds" widget placed on the Member Home Page on my site, but this widget is not displayed when I view my site in mobile. Why is this happening?'); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate("Ans: This is happening so because welcome tab will not be displayed when your site is viewed in mobile."); ?>
      </div>
    </li>
	</ul>
</div>