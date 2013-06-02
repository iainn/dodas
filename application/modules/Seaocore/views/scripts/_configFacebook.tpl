<div id="facebook_helpsteps">
	<h3><?php echo $this->translate("Guidelines to configure Facebook Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_1">
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('fbstep-1');"><?php echo $this->translate("Step 1");?></a>
					<div id="fbstep-1" style='display: none;'>
						<p>
            	<?php echo $this->translate("Go to this URL for steps to configure basic Facebook Integration on your SocialEngine website : ");?><a href="http://www.google.com/url?q=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DlyfHc6jMvZc%26list%3DUUlUy2ac9Xb-Bw95e1EyKlEQ%26index%3D3%26feature%3Dplcp" target="_blank" style="color:#5BA1CD;">http://www.google.com/url?q=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DlyfHc6jMvZc%26list%3DUUlUy2ac9Xb-Bw95e1EyKlEQ%26index%3D3%26feature%3Dplcp</a><br/>    
            </p>
					</div>
				</div>	
			</li>	
			
			<li>	
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('fbstep-2');"><?php echo $this->translate("Step 2");?></a>
					<div id="fbstep-2" style='display: none;'>
						<p><?php echo $this->translate("1) Go to this URL: ");?><a href="https://developers.facebook.com/apps " target="_blank" style="color:#5BA1CD;">https://developers.facebook.com/apps </a><br /></p>
						
						<p><?php echo $this->translate("2) Fill the basic settings about your website in the 'Basic' section and then click on 'Save Changes' to save them.");?><br />
						     <?php echo $this->translate("a)  Enter your site's domain => <b>" . $_SERVER['HTTP_HOST'] . "</b> in the 'App Domain' field in the 'Basic Info' section. Please note that the domain should not contain 'www.' prefix. ");?><br />
	              <?php echo $this->translate("b) Enter your site's url") . ' => <b>' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . "</b> in the 'Site URL' field in the 'Website' section.";?><br />
	           
                <?php echo   $this->translate("c) Enter your site's canvas url") . ' => <b> http://' . $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . 'seaocore/auth/fb-Invite/ </b> in the \'Canvas URL\' field in the \'App on facebook\' section.' ;?><br />
                
                <?php echo $this->translate("d) If your site runs on https, then enter your site's secure canvas url") . ' => <b> https://'. $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . 'seaocore/auth/fb-Invite/ </b> in the \'Secure Canvas URL\' field in the \'App on facebook\' section.' ;?><br />                
	             
						</p>
						<img src="https://lh3.googleusercontent.com/-j6cPDFeoJ6w/T31CITgweGI/AAAAAAAAAJc/FXQiYGwOpUQ/s640/2.jpg" alt="Step 2" />
					</div>
				</div>
			</li>	
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('fbstep-3');"><?php echo $this->translate("Step 3");?></a>
					<div id="fbstep-3" style='display: none;'>
						<p><?php echo $this->translate("Now, go to the 'Auth Dialog' section from the left panel of the page and enable 'Authenticated Referrals' from the 'Configure how Facebook refers users to your app' section and then click on 'Save Changes' to save changes. Upload a logo for your site from the 'Logo' field in the 'Customize' section.");?></p>
						<img src="https://lh6.googleusercontent.com/-ra3S7_SUZWU/T31CJW2XYCI/AAAAAAAAAJg/Ovi6IH-lhxA/s700/3.jpg" alt="Step 3" />
					</div>
				</div>	
			</li>
		</ul>
	</div>
<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
</div>