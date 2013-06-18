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
<div class="post_title_featured_<?php echo $this->post->featured ? 'yes' : 'no'?> post_title_sponsored_<?php echo $this->post->sponsored ? 'yes' : 'no'?>">
  <?php 
    $post_title = $this->post->getTitle();
    if ($this->max_title_length) {
      $post_title = $this->radcodes()->text()->truncate($post_title, $this->max_title_length);
    }
  ?>
  <?php echo $this->htmlLink($this->post->getHref(), $post_title); ?>
</div>