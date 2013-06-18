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
<?php echo $this->partial('post/_feedback.tpl', 'post', array('post' => $post))?>
<div class="post_profile_title post_profile_title_featured_<?php echo $post->featured ? 'yes' : 'no'?> post_profile_title_sponsored_<?php echo $post->sponsored ? 'yes' : 'no'?>">
<?php if ($post->url): ?>
  <?php echo $this->htmlLink($post->url, $post->getTitle(), array('target'=>'_blank'))?>
  <?php if ($post->source): ?>
    <span class="post_profile_title_source">(<?php echo $this->htmlLink($this->url(array('action'=>'browse','source'=>$post->source), 'post_general', true), $post->source); ?>)</span>
  <?php endif;?>  
<?php else: ?>
  <?php echo $post->getTitle(); ?>
<?php endif;?>
</div>