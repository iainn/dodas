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
<?php if ($this->paginator->getTotalItemCount()): ?>

  <?php if ($this->display_style == 'block'): ?>
  <ul class="post_blocks">
    <?php foreach ($this->paginator as $post): ?>
    <li>
      <?php if (true || $post->photo_id): ?>
      <div class="post_photo">
        <?php echo $this->itemPhoto($post, 'thumb.profile'); ?>
      </div>
      <?php endif; ?>      
      <div class="post_content">
        <?php echo $this->partial('post/_feedback.tpl', 'post', array('post' => $post))?>
        <div class="post_content_inner">
          <div class="post_title">
            <?php echo $this->htmlLink($post->getHref(), $this->radcodes()->text()->truncate($post->getTitle(), 68)); ?>
          </div>
          <div class="post_meta">
            <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 'shows' => 'date, owner'))?>
          </div>
        </div>
      </div>
    </li>
    <?php endforeach;?>  
  </ul>
  <?php elseif ($this->display_style == 'narrow'): ?>
  <ul class="post_list">
    <?php foreach ($this->paginator as $post): ?>
    <li>
      <div class="post_list_title"><?php echo $post->toString(); ?></div>
      <div class="post_list_meta">
        <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 'shows' => 'date, owner'))?>
      </div>
    </li>
    <?php endforeach;?>  
  </ul>
  <?php else: ?>
  <ul class="post_rows">
    <?php foreach ($this->paginator as $post): ?>
    <li><?php echo $this->partial('_post_row.tpl', 'post', array('post' => $post,
      'shows' => 'photo, votes, meta, owner, comments, date'
    ))?></li>
    <?php endforeach;?>
  </ul>
  <?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no posts posted yet.');?>
    </span>
  </div>  
<?php endif; ?>