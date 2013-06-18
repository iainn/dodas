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

<div class='post_profile_submitter'>
  <?php echo $this->htmlLink($this->owner->getHref(),
    $this->itemPhoto($this->owner, 'thumb.icon'),
    array('class' => 'post_profile_submitter_photo')
  )?>
  <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'post_profile_submitter_user')) ?>
  <span><?php $total_posts = $this->translate(array('%s post entry','%s post entries', $this->totalPosts), $this->totalPosts); 
    echo $this->htmlLink(array('route'=>'post_general', 'action'=>'browse', 'user'=>$this->owner->getIdentity()), $total_posts);
  ?></span>
</div>