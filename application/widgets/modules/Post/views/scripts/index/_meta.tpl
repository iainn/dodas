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
<?php 
if ($this->shows) {
  $shows = array_filter(array_map("trim", preg_split('/[,]+/', $this->shows)));
  foreach ($shows as $what) {
    $this->{"show_$what"} = true;
  }
}
?>
<ul class="post_meta_media_<?php echo $this->post->media; ?>">
  <?php if ($this->show_date): ?>
    <li class="post_meta_date"><?php echo $this->timestamp($this->post->creation_date); ?></li>
  <?php endif; ?>
  <?php if ($this->show_owner): ?>
    <li class="post_meta_owner"><?php echo $this->translate('by %s', $this->post->getOwner()->toString()); ?></li>
  <?php endif; ?>
  <?php if ($this->show_category): ?>
    <li class="post_meta_category"><?php echo $this->htmlLink($this->post->getCategory()->getHref(), $this->translate($this->post->getCategory()->getTitle())); ?></li>
  <?php endif; ?>
  <?php if ($this->show_comments): ?>
    <li class="post_meta_comments"><?php echo $this->translate(array("%s comment", "%s comments", $this->post->comment_count), $this->locale()->toNumber($this->post->comment_count));?></li>
  <?php endif; ?>
  <?php if ($this->show_likes): ?>
    <li class="post_meta_likes"><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->post->like_count), $this->locale()->toNumber($this->post->like_count)); ?></li>
  <?php endif; ?>
  <?php if ($this->show_views): ?>
    <li class="post_meta_views"><?php echo $this->translate(array('%s view', '%s views', $this->post->view_count), $this->locale()->toNumber($this->post->view_count)); ?></li>
  <?php endif; ?>
  <?php if ($this->show_upvotes): ?>
    <li class="post_meta_upvotes"><?php echo $this->locale()->toNumber($this->post->helpful_count)?></li>
  <?php endif; ?>
  <?php if ($this->show_downvotes): ?>
    <li class="post_meta_downvotes"><?php echo $this->locale()->toNumber($this->post->nothelpful_count)?></li>
  <?php endif; ?>
  <?php if ($this->show_share): ?>
    <li class="post_meta_share"><?php echo $this->htmlLink(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'post', 'id' => $this->post->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'post_meta_action icon_post_share smoothbox')); ?></li>
  <?php endif; ?>
  <?php if ($this->show_report): ?>
    <li class="post_meta_report"><?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->post->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'post_meta_action icon_post_report smoothbox')); ?></li>
  <?php endif; ?>
</ul>