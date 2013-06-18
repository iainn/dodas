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
<?php $post = $this->post; ?>
<div class="post_profile_preview">
  <div class="post_profile_preview_<?php echo $post->media; ?>">
  <?php if ($post->isMedia('photo')): ?>
    <?php if ($post->thumb): ?>
      <?php echo $this->htmlLink($post->thumb, $this->itemPhoto($post, 'main'), array('target'=>'_blank')); ?>
    <?php else: ?>
      <?php echo $this->itemPhoto($post, 'main'); ?>
    <?php endif;?>
  <?php elseif ($post->isMedia('video')): ?>
    <?php $video = $this->video_data; ?>
    <iframe width="560" height="315" src="<?php echo $video['info']['player_url']?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
  <?php endif;?>
  </div>
</div>
