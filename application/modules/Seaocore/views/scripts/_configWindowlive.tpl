<div id="windowlive_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Windows Live Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />

  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="guideline_4">
			<li>			
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="msnstep-1" style='display: none;'>
						<p><?php echo $this->translate("To register your application, go here :");?> <a href="http://msdn.microsoft.com/en-us/library/cc287659.aspx" target="_blank" style="color:#5BA1CD;">http://msdn.microsoft.com/en-us/library/cc287659.aspx.</a><br /><br />
				<?php echo $this->translate("Click on the 'Windows Live application management site' in the 'Registering Your Application' section.");?><br />
	</p>
						<img src="https://lh5.googleusercontent.com/_nEoXA-sO-_M/TZxrHAY0KxI/AAAAAAAAACA/_sCm06Mf8Vs/s720/msnst1.gif" alt="Step 1" />
					</div>
				</div>
			</li>
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="msnstep-2" style='display: none;'>
						<p>
							<?php echo $this->translate("Click on 'create application' link to create your application.");?><br/>
						</p>
						<img src="https://lh6.googleusercontent.com/-U5h-Bd-Anoc/T5kyx0NtayI/AAAAAAAAALA/NvVLcqQwn7M/s996/2.jpg" alt="Step 2" />
					</div>
				</div>
			</li>	
						
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="msnstep-3" style='display: none;'>
						<p>
						   <?php echo $this->translate("Fill the application name in the 'Application name' field. You can make this the same as your site name and click on 'I accept' button to get your 'Client ID' and 'Secret Key'.");?><br/>
									  
						</p>
						<img src="https://lh3.googleusercontent.com/-kOcdIh4Q7RA/T5kyx5sOq_I/AAAAAAAAALE/rsXt4iXec5A/s912/3.jpg" alt="Step 3" />
					</div>
				</div>
			</li>

			<li>		
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-4');"><?php echo $this->translate("Step 4") ?></a>
					<div id="msnstep-4" style='display: none;'>
						<p>
								<?php echo $this->translate("Now, you will be redirected to the below 'My apps' page where you will get your 'Client ID' and 'Secret Key'. Copy and paste these values in your site's Windows Live contact importer settings fields. Now, click on 'Application Settings Page' to configure your application created using above step.");?><br/>
						</p>
						<img src="https://lh5.googleusercontent.com/-KHI2dUeLOC0/T5kyye66n7I/AAAAAAAAALM/jq_RbguZX3w/s711/4.jpg" alt="Step 4" />
					</div>
				</div>
			</li>	
						
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-5');"><?php echo $this->translate("Step 5") ?></a>
					<div id="msnstep-5" style='display: none;'>
						<p>
						
							<?php echo $this->translate("Upload a logo for your application from the 'Application Logo' field in the 'Text & Logos' section and then click on 'Save' to save changes.");?><br/>
						</p>
						<img src="https://lh4.googleusercontent.com/-e46OmZKkfqA/T5kyy3wvw7I/AAAAAAAAALU/zdeGmyeMgB0/s720/5.jpg" alt="Step 5" />
					</div>
				</div>
			</li>
			
			<li>	
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('msnstep-6');"><?php echo $this->translate("Step 6") ?></a>
					<div id="msnstep-6" style='display: none;'>
						<p>
							<?php echo $this->translate("Go to 'API Settings' section from the left panel of the page and enter your site's domain in the 'Redirect domain' field and click on 'Save' to save the changes. (A help note is provided against the field for entering your site's domain name.)");?><br/>
						
						</p>
						<img src="https://lh5.googleusercontent.com/-hqxMPp2DBVw/T5kyzZHqlaI/AAAAAAAAALY/ru7nnf7p59o/s796/6.jpg" alt="Step 6" />
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