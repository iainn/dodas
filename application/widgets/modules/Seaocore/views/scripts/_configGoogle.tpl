<div id="google_helpsteps" style="display:none;">
	<h3><?php echo $this->translate("Guidelines to configure Google Application") ?></h3>
	<p><?php echo $this->translate('Please follow the steps given below.') ?></p><br />
	
  <div class="admin_seaocore_guidelines_wrapper">
    <ul class="admin_seaocore_guidelines" id="google-config">
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-1');"><?php echo $this->translate("Step 1") ?></a>
					<div id="googlestep-1" style='display: none;'>
						<p>
						
						<?php echo $this->translate("To start using Google APIs console go here :");?> <a href="https://code.google.com/apis/console" target="_blank" style="color:#5BA1CD;">https://code.google.com/apis/console</a><br />
						
						
						 <?php echo $this->translate("Login to your google account and click on the 'Create project...' to select API access.");?><br />
						
						</p>
						<img src="https://lh4.googleusercontent.com/-BXrw5T8QqxM/Tx7LjkmjgaI/AAAAAAAAAI0/nmJ6lzqUk_Y/s912/step1.jpg" alt="Step 1" />
					</div>
				</div>
			</li>
			
			<li>	
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-2');"><?php echo $this->translate("Step 2") ?></a>
					<div id="googlestep-2" style='display: none;'>
						<p>
							 <?php echo $this->translate("Select 'API Access' tab from left menu and click on 'Create an OAuth 2.0 Client' button. After clicking on this button a popup will open where you will be asked to enter a 'Product name'. So enter your product name.");?><br />
						
						</p>
						<img src="https://lh5.googleusercontent.com/-2Qls_S7ZeaU/Tx7LjXpZGgI/AAAAAAAAAI4/Ra4S28cmdOQ/s912/step2.jpg" alt="Step 2" />
					</div>
				</div>
			</li>	
			
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-3');"><?php echo $this->translate("Step 3") ?></a>
					<div id="googlestep-3" style='display: none;'>
						<p>
							
							<?php echo $this->translate("Enter your 'Product name'. This product name will be displayed to users while authenticating. You may also upload a logo within mentioned dimensions. Now click on the 'Next' button at bottom to enter 'Authorized Redirect URLs' and 'Authorized JavaScript Origins' of your site.");?><br />
						
						</p>
						<img src="https://lh6.googleusercontent.com/-e8gdSFWmikM/Tx7DElP9elI/AAAAAAAAAH8/weRVtEqp05w/s573/Step%2525203.jpg" alt="Step 3" />
					</div>
				</div>
			</li>	
						
			<li>
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-4');"><?php echo $this->translate("Step 4") ?></a>
					<div id="googlestep-4" style='display: none;'>
						<p>
							<?php echo $this->translate("Select the 'Web application' Application Type and enter the below mentioned 'Authorized Redirect URLs' and 'Authorized JavaScript Origins' and click on 'Create client ID' button to get your 'Client ID'");?><br />
							<?php echo  '<b>' . $this->translate("1) Authorized Redirec URLs") . ' =>' .  ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->layout()->staticBaseUrl . 'seaocore/usercontacts/getgooglecontacts </b>' ;?><br />
							 
							<?php echo '<b>' . $this->translate("2) Authorized JavaScript Origins") . ' => ' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. '</b>' ;?><br />
						
						</p>
						<img src="https://lh5.googleusercontent.com/--vclqbqdA68/Tx7DFbl162I/AAAAAAAAAII/b9_xh8GhrDM/s512/Step%2525204.jpg" alt="Step 4" />
					</div>
				</div>
			</li>	

			<li>		
				<div class="steps">
					<a href="javascript:void(0);" onClick="guideline_show('googlestep-5');"><?php echo $this->translate("Step 5") ?></a>
					<div id="googlestep-5" style='display: none;'>
						<p>
						   <?php echo $this->translate("Copy below 'Client ID' and paste this value in your site's Gmail contact importer settings field.");?><br />
						</p>
						<img src="https://lh6.googleusercontent.com/-z0vE1EfIKq8/Tx7DEVrOdqI/AAAAAAAAAH4/52t3swk5m80/s743/Step%2525205.jpg" alt="Step 5" />
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