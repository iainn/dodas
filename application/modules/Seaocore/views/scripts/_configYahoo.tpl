<div id="yahoo_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Yahoo Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_2">
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="yahoostep-1" style='display: none;'>
						<p>
						<?php echo $this->translate("Go here to register your application:");?> <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_blank" style="color:#5BA1CD;">https://developer.apps.yahoo.com/dashboard/createKey.html</a><br />
						
						<div style="font-weight:bold;"><?php echo $this->translate("About creating your application :-");?></div>
	 		
						<?php echo $this->translate("a) Write your application name. You can make this the same as your site name.");?><br />
						<?php echo $this->translate("b) In the 'Kind of Application' field, choose : 'Web-based'.");?><br />
						<?php echo $this->translate("c) Write a short description about your application/website.");?><br />
						<?php echo $this->translate("d) In the 'Application URL' field, write the URL of your site (example : http://www.mysite.com).");?><br />
						
	          <div style="font-weight:bold;"><?php echo $this->translate("Security &amp; Privacy:-");?></div>
	          <?php echo $this->translate("a) In the 'Application Domain' field, write your site's domain (example : http://www.mysite.com).");?><br />
						<?php echo $this->translate("b) Choose the 'Access Scopes' field to : 'This app requires access to private user data.'. Selecting this option will give you a list of Yahoo services. Among them, choose 'Yahoo! Contacts' in 'Read' mode.");?><br />
						<?php echo $this->translate("c) Check on the 'Terms of Use' checkbox.");?><br />
						<?php echo $this->translate("d) Now click on the 'Get API Key' button. After clicking on this button you will be redirected to a page where it would ask you to verify your domain. So, verify your domain.");?><br />
						
						Please note that your "Application Domain" must be started with the "www." prefix. For example the application domain "http://www.example.com" will be correct but "http://example.com" will not be correct. 
						
						
						</p>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/yahst1.gif" alt="Step 1" />
					</div>
				</div>
			</li>	
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="yahoostep-2" style='display: none;'>
						<p>
						   <?php echo $this->translate("a) Verify your domain.");?><br />
						   <?php echo $this->translate("b) After verifying your domain successfully, you will be redirected to a success page where you will get your 'API Key' and 'Shared Secret Key'. Copy them and paste these values in your site's Yahoo contact importer settings fields.");?><br />
						
						</p>
						<img src="https://lh4.googleusercontent.com/_nEoXA-sO-_M/TZxrEpPl3HI/AAAAAAAAABY/T7z5wR0Hh7c/yahst2.gif" alt="Step 2" />
					</div>
				</div>
			</li>	
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="yahoostep-3" style='display: none;'>
						<p><?php echo $this->translate("Copy below 'Consumer Key (API Key)' and 'Consumer Secret (Shared Secret Key)' and paste these values in your site's Yahoo contact importer settings fields.");?><br /></p>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/yahst3.gif" alt="Step 3" />
					</div>
				</div>
			</li>	
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('yahoostep-4');"><?php echo $this->translate("Step 4") ?></a>
					<div id="yahoostep-4" style='display: none;'>
						<p><?php echo $this->translate("Open the hidden file '.htaccess', from your root directory and search for the lines given below:");?></p>
						<div class="code_box">
						  <b>
								<?php echo $this->translate("&lt;IfModule mod_rewrite.c&gt;<br /><br />
	
	                  Options +FollowSymLinks<br /><br />
	                
	                  RewriteEngine On");?><br /><br />
	             </b>      
	           </div><br />
	           <p>Now replace 'yourdomain.com' with your site's domain name in the below lines, and insert them just after the above mentioned lines.</p>
	          <div  class="code_box">
						  <b>
								<?php echo $this->translate("<br /><br />RewriteCond %{HTTP_HOST} ^yourdomain.com [NC]<br /><br />  
	              RewriteRule ^(.*)$ http://www.yourdomain.com/$1 [L,R=301]");?><br /><br />  
	             </b>
	              
	          </div>
						</p>
						
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