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

	<div class='global_form'>
	  <div>
	    <div class="post_success_panel">
	      <h3><?php echo $this->translate('Post Posted');?></h3>
	      <p>
	        <?php echo $this->translate('Your post "%s" has been successfully saved.', $this->post->toString());?>
	      </p>
	      <br />
	      <p class="post_success_notice">
        
	        <?php if ($this->post->isApprovedStatus()): ?>
	          <?php echo $this->translate('It has been automatically approved and live.')?>
	        <?php else: ?>
	          <?php echo $this->translate('Administrator will review your post, once it is approved, it will get listed.'); ?>
	        <?php endif;?>
          <br /><br />
	        <?php echo $this->translate('You can %1$s or %2$s.', 
	          $this->htmlLink($this->post->getHref(array('action'=>'edit')), $this->translate('edit')),
	          $this->htmlLink($this->post->getHref(), $this->translate('continue to view this post'))
	        )?>
	      </p>
	    </div>
	  </div>
	</div>

