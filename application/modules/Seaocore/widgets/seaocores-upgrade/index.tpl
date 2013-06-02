<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Sociealengineaddon
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function upgradePlugin(url) {
    Smoothbox.open(url);
  }
</script>
<h3>
  <?php echo $this->translate('Latest versions of SocialEngineAddOns plugins for your site') ?>
</h3>
<p>
  <?php echo $this->translate('Here, you can upgrade the latest version of these plugins by using ‘Upgrade’ button available in front of all the desired plugins that needs to be upgraded.<br />The latest versions of these plugins are also available to you in your SocialEngineAddOns Client Area. Login into your SocialEngineAddOns Client Area here: <a href="http://www.socialengineaddons.com/user/login" target="_blank">http://www.socialengineaddons.com/user/login</a>.'); ?>
</p><br />



<?php if ($this->flag_delete == 1) : ?>
<div class="tip">
  <span>  
<?php $module_name = 'socialengineaddon';
echo $this->translate('You have installed the latest version of “SocialEngineAddOns Core Plugin”. So, please remove the old version installed on your site and its associated old “Socialengineaddon” directory, which is available at "/application/modules/Socialengineaddon" as we are now using “Seaocore” directory. To remove the old version and its directory, <a href="' . $this->url(array('action' => 'delete', 'modules' => $module_name)) . '" class="smoothbox">Click here</a>.'); ?>
  </span>
</div>
<?php elseif ($this->flag_delete == 2): ?>
<div class="tip">
  <span>
<?php echo $this->translate('You have installed the latest version of “SocialEngineAddOns Core Plugin”. So, please remove the old “Socialengineaddon” directory, which is available at "/application/modules/Socialengineaddon" as we are now using “Seaocore” directory.'); ?>
  </span>
</div>
<?php endif; ?>


<?php if( !empty($this->enabledPagePlugin) || !empty($this->enabledBusinessPlugin )): ?>
<b class="bold"><?php echo $this->translate("Note: "); ?></b>
<?php endif; ?>

<?php if( !empty($this->isSitepage) && !empty($this->enabledPagePlugin) ): ?><br />&nbsp;&nbsp;
      1. <?php 
	$pageUrl = $this->url(array('module' => 'sitepage', 'controller' => 'extension', 'action' => 'upgrade'), 'admin_default', true); 
	$getPageLink = '<a href="' . $pageUrl . '">Click here</a>';
      ?>
      <?php echo $this->translate("To upgrade the latest version of the Extensions for Directory / Pages Plugin, %s.", $getPageLink); ?><br />&nbsp;&nbsp;
<?php endif; if( !empty($this->isSitebusiness) && !empty($this->enabledBusinessPlugin) ):?>
      2. <?php
	$businessUrl = $this->url(array('module' => 'sitebusiness', 'controller' => 'extension', 'action' => 'upgrade'), 'admin_default', true); 
	$getBusinessLink = '<a href="' . $businessUrl . '">Click here</a>';
      ?>
      <?php echo $this->translate("To upgrade the latest version of the Extensions for Directory / Business Plugin, %s.", $getBusinessLink); ?>
<?php endif; ?>
<br /><br />
<div class='sociealengineaddons_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<?php
	if( count($this->channel) ):
		$isPlugin = false;
?>
  <table class='admin_table'>
    <thead>
      <tr>

         <th align="left">
        	<?php echo $this->translate("Plugin Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Latest version on SocialEngineAddOns.com"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Version on your website"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Should you Upgrade?"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Upgrade?"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->channel as $item):?>
			<?php
        if($item['ptype'] === 'sitereviewlistingtype') {
          $product_version = $this->sitereviewListingTypeVersion;
        }else {
          $product_version = $item['product_version'];
        }
				$running_version = $item['running_version'];
				$versionInfo = 0;
				$status = $this->translate('No');
				$shouldUpgrade = FALSE;
				if( !empty($running_version) && !empty($product_version) ) {
					$isPlugin = true;
					$versionInfo = strcasecmp($product_version, $running_version);
					if( $versionInfo > 0 ) {
						$shouldUpgrade = TRUE;
						$status = $this->translate('Yes');
					}
				?>
        <tr>
          <td><?php echo $item['title']; ?></td>
					<td><?php echo $product_version; ?></td>
					<td><?php echo $running_version; ?></td>
					<td><?php echo $status; ?></td>
<td>

  <?php
     $url = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'upgrade-plugin', 'name' => $item['name'], 'version' => $product_version, 'ptype' => $item['ptype'], 'key' => $item['key'], 'title' => str_replace("/", "_", $item['title']), 'calling' => 'seaocore'), 'admin_default', true);
     $title = $this->translate("Upgrade '%s' to latest version %s", $item['title'], $product_version);
     if( empty($shouldUpgrade) ):
      echo '-';
     else:
  ?>
    <button title="<?php echo $title; ?>" style="font-size:11px;padding:2px;" onclick="upgradePlugin('<?php echo $url; ?>')">Upgrade</button>
    <?php endif; ?>
</td>
        </tr>
			<?php } ?>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />

<?php endif; 
		if( empty($isPlugin) ):?>
      <?php echo '<div class="tip"><span>No plugins by SocialEngineAddOns were found on your site. Click <a href="http://www.socialengineaddons.com/catalog/1/plugins" target="_blank">here</a> to view and purchase them.</span></div>'; ?>
<?php endif; ?>