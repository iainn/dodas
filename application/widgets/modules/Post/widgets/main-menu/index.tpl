<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Post
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Posts');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php /*
<style>
.layout_post_main_menu ul.navigation ul {
  display: block !important;
}
</style>
<script>
$('menu-post-main-browsex').getParent().addEvents({
  mouseover: function() {
    this.getChildren('ul').each(function(el) {
      el.show();
     });
  },
  mouseout: function() {
	  this.getChildren('ul').each(function(el) {
	    el.hide();
	   });
	}
});
</script>
*/
?>