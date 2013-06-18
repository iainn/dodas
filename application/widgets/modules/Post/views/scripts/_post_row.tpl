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
$post = $this->post;

if ($this->shows) {
  $shows = array_filter(array_map("trim", preg_split('/[,]+/', $this->shows)));
  foreach ($shows as $what) {
    $this->{"show_$what"} = true;
  }
}

$show_votes = ($this->show_votes) ? true : false;
$show_photo = ($this->show_photo) ? true : false;
$show_description = ($this->show_description) ? true : false;
$show_details = ($this->show_details) ? true : false;
$show_keywords = ($this->show_keywords) ? true : false;
$show_meta = ($this->show_meta) ? true : false;

// meta
$show_date = ($this->show_date) ? true : false;
$show_owner = ($this->show_owner) ? true : false;
$show_category = ($this->show_category) ? true : false;
$show_comments = ($this->show_comments) ? true : false;
$show_likes = ($this->show_likes) ? true : false;
$show_views = ($this->show_views) ? true : false;
$show_upvotes = ($this->show_upvotes) ? true : false;
$show_downvotes = ($this->show_downvotes) ? true : false;
$show_share = ($this->show_share) ? true : false;
$show_report = ($this->show_report) ? true : false;

?>
<?php if ($this->show_votes): ?>
<?php echo $this->partial('post/_feedback.tpl', 'post', array('post' => $post))?>
<?php endif;?>

<div class="post_item_inner">
  <?php if ($show_photo && $post->photo_id): ?>
  <div class="post_item_photo">
    <?php echo $this->itemPhoto($post, 'thumb.normal'); ?>
  </div>
  <?php endif; ?>
  <div class="post_item_title">
    <?php echo $this->htmlLink($post->getHref(), $post->getTitle()); ?>
    <?php if ($post->source): ?>
      <span class="post_item_source">(<?php echo $this->htmlLink($this->url(array('action'=>'browse','source'=>$post->source), 'post_general', true), $post->source); ?>)</span>
    <?php endif;?>
  </div>
  <?php if ($show_description && $post->getDescription()): ?>
  <div class="post_item_description"><?php echo nl2br($post->getDescription())?></div>
  <?php endif; ?>
  <?php if ($show_details): ?>
    <?php 
      $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($post);
      $postFieldValues = $this->fieldValueLoop($post, $fieldStructure);  
    ?>
    <?php if ($postFieldValues): ?>
    <div class="post_item_values">
      <div class="post_item_values_header">
        <?php echo $this->translate('Details:')?>
      </div>
      <div class="profile_fields">
        <?php echo $postFieldValues; ?>
      </div>
    </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($show_keywords && $post->getKeywords() && count($post->getKeywordsArray())): ?>
  <div class="post_item_keywords">
    <?php echo $this->partial('index/_keywords.tpl', 'post', array('post' => $post))?>
  </div>
  <?php endif; ?>
  <?php if ($show_meta): ?>
  <div class="post_item_meta">
    <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 
    	'show_owner'=>$show_owner, 
    	'show_date'=>$show_date,
      'show_category'=>$show_category,  
      'show_comments'=>$show_comments, 
      'show_views'=>$show_views, 
      'show_likes'=>$show_likes,   
    	'show_share'=>$show_share, 
    	'show_report'=>$show_report,
    	'show_upvotes'=>$show_upvotes, 
    	'show_downvotes'=>$show_downvotes,
    ))?>
  </div>
  <?php endif; ?>
</div>
