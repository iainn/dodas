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

<div id="profile_options">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->dashboardNavigation)
        ->render();
    ?>
</div> 