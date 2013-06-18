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

<?php if( count($this->quickNavigation) > 0 ): ?>
  <div class="quicklinks">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->quickNavigation)
        ->render();
    ?>
  </div>
  
  <?php if ($this->submenu): ?>
    <script>
    $('menu-post-create-quick').getParent().addEvents({
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
  <?php endif;?>
<?php endif; ?>    
    
