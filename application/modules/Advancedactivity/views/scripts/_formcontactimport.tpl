<?php ?>
<style type="text/css">
.form-sub-heading{
	margin-top:10px;
	border-top: 1px solid #EEEEEE;

	height: 1em;
	margin-bottom: 15px;
}
.form-sub-heading div{
	display: block;
	overflow: hidden;
	padding: 4px 6px 4px 0px;
	font-weight: bold;
	float:left;
	margin-top:10px;
}
</style>

<div class="form-sub-heading">
	<div><?php echo $this->translate("LinkedIn Integration Settings");?></div>
	<div><a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'help-invite'), 'default', true); ?>" target="_blank" ><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/admin/help.gif"></a></div>
</div>

<div id="linkedin_apikey-wrapper" class="form-wrapper" style="border:none;">
  <div id="linkedin_apikey-label" class="form-label">
    <label for="linkedin_apikey" ><?php echo $this->translate("API Key");?></label>
  </div>
  <div id="linkedin_apikey-element" class="form-element">
    <input name="linkedin_apikey" id="linkedin_apikey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'); ?>" type="text">
  </div>
</div>
<div id="linkedin_secretkey-wrapper" class="form-wrapper" style="border:none;">
  <div id="linkedin_secretkey-label" class="form-label">
    <label for="linkedin_secretkey" ><?php echo $this->translate("Secret Key");?></label>
  </div>
  <div id="linkedin_secretkey-element" class="form-element">
    <input name="linkedin_secretkey" id="linkedin_secretkey" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'); ?>" type="text">
  </div>
</div>
