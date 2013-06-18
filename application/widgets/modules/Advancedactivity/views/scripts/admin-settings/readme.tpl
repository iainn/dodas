<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: readme.tpl 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<h2>
  <?php echo $this->translate("ADVANCED_ACTIVITY_PLUGIN_NAME")." ".$this->translate("Plugin") ?>
</h2>
<div class="seaocore_admin_tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/advancedactivity/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>

    </li>
  </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Advancedactivity/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
	<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>
	
<script type="text/javascript" >

function form_submit() {
	
	var url='<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'settings'), 'admin_default', true) ?>';
	window.location.href=url;
}

</script>