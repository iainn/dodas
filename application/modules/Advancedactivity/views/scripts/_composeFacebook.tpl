<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _composeFacebook.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php 
    $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
    $facebook_apikey = $settings['appid'];
    $facebook_secret = $settings['secret'];
  // Disabled
  
  if((!Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish'? 1:0) || empty ($this->isAFFWIDGET)|| empty($facebook_secret) || empty($facebook_apikey))) {
    return;
  }
  // Add script
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/composer_facebook.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.AdvFacebook({
      lang : {
        'Publish this on Facebook' : '<?php echo $this->translate('Publish this on Facebook') ?>',
        'Do not publish this on Facebook' : '<?php echo $this->translate('Do not publish this on Facebook') ?>'
      }
    }));
  });
</script>
<style type="text/css">
.composer_facebook_toggle {
background-position:right !important;
padding-right:15px;

}
</style>
