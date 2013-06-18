<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: profile-content-tabs.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<script type="text/javascript">
  var tabProfileContainerSwitch = function(element) {
    if( en4.core.request.isRequestActive())return;
    if( element.tagName.toLowerCase() == 'a' ) {
      element = element.getParent('li');
    }
    var myContainer = element.getParent('.aaf_tabs_feed').getParent(); 
    myContainer.getElements('ul > li').removeClass('aaf_tab_active');
    element.get('class').split(' ').each(function(className){
      className = className.trim();
      if( className.match(/^tab_[0-9]+$/) ) {       
        element.addClass('aaf_tab_active');        
      
      }
    });
  }
  var activeAAFAllTAb=function(){
    if($('update_advfeed_blink'))
      $('update_advfeed_blink').style.display ='none';
   var element=$('tab_advFeed_everyone');
      if( element.tagName.toLowerCase() == 'a' ) {
      element = element.getParent('li');
    }

    var myContainer = element.getParent('.aaf_tabs_feed').getParent();

    //  myContainer.getChildren('div:not(.tabs_alt)').setStyle('display', 'none');
    myContainer.getElements('ul > li').removeClass('aaf_tab_active');
    element.get('class').split(' ').each(function(className){
      className = className.trim();
      if( className.match(/^tab_[0-9]+$/) ) {
        //    myContainer.getChildren('div.' + className).setStyle('display', null);
        element.addClass('aaf_tab_active');
      }
    });
  }
</script>
<div class="aaf_tabs_feed">
  <div class="aaf_tabs_loader" style="display: none;" id="aaf_tabs_loader">
		<img alt="Loading" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" align="left" />
	</div>
  <ul class="aaf_tabs_apps_feed">
    <li class="tab_1"> 	
      <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));getTabBaseContentFeed('owner','0'); $('feed-update').empty(); $('feed-update').style.display = 'none';">
<?php if (($this->subject()->getType() === 'user') ||($this->subject()->getType() === 'sitepage_page' && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
         || ($this->subject()->getType() === 'sitebusiness_business' && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
         ):
 echo $this->subject()->getTitle(); 
   else: 
      echo $this->translate("Owner")." (".$this->string()->truncate($this->subject()->getTitle(), 15).")" ;
  endif;
?>  
</a>
    </li>
    <?php if ($this->viewer()->getIdentity() && ($this->subject()->getType() != 'user')): ?>
      <li>&#8226;</li>	
      <li class="tab_2">
        <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));getTabBaseContentFeed('membership','0'); $('feed-update').empty();" ><?php echo $this->translate("Friends") ?></a>
      </li>   
    <?php endif; ?>
    <li>&#8226;</li>	
    <li class="tab_3 aaf_tab_active" id="tab_advFeed_everyone">        
      <a href="javascript:void(0);"   onclick="javascript: tabProfileContainerSwitch($(this));getTabBaseContentFeed('all','0'); $('feed-update').empty();" ><?php echo $this->translate("Everyone") ?><span id="update_advfeed_blink" class="notification_star"></span></a>
    </li>
  </ul> 	
</div>