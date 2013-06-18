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

<h3 class="sep post_main_header">
  <span>
  <?php if ($this->category && $this->categoryObject instanceof Post_Model_Category): ?>
    <?php echo $this->translate("My Posts: %s", $this->translate($this->categoryObject->getTitle())); ?>
  <?php else: ?>
    <?php echo $this->translate("My Posts"); ?>
  <?php endif; ?>    
  </span>
</h3>    

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <?php if (false && ($this->category || $this->keyword || $this->user || $this->tag || $this->source || $this->period)): ?>
  <div class="posts_result_pager">
    <?php echo $this->translate('Showing posts'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'manage', 'tag'=>$this->tag), 'post_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>    
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with %s keyword', $this->htmlLink(
          $this->url(array('action'=>'manage', 'keyword'=>$this->keyword), 'post_general', true),
          $this->keyword
        ));?>
      <?php endif; ?>    
    <?php echo $this->htmlLink(array('action'=>'manage', 'route'=>'post_general'), $this->translate('(x)'))?>
  </div>
  <?php endif; ?>
  
  <ul class="post_rows">
  <?php foreach ($this->paginator as $post): ?>
    <li>
      <div class="post_item_options">
        <?php if ($post->authorization()->isAllowed($this->viewer(), 'edit')): ?>
          <?php echo $this->htmlLink($post->getHref(array('action'=>'edit')), $this->translate('Edit Post'), array('class'=>'buttonlink icon_post_edit'))?>
        <?php endif; ?>
        <?php if ($post->authorization()->isAllowed($this->viewer(), 'delete')): ?>
          <?php echo $this->htmlLink($post->getHref(array('action'=>'delete')), $this->translate('Delete Post'), array('class'=>'buttonlink icon_post_delete'))?>
        <?php endif; ?>
      </div>
      <?php echo $this->partial('_post_row.tpl', 'post', array('post' => $post,
        'shows' => 'photo, votes, meta, category, comments, views, upvotes, downvotes, date'
      ))?>
    </li>
  <?php endforeach;?>
  </ul>
  
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'query' => $this->formValues,
  )); ?>       
  
<?php elseif ($this->category || $this->keyword || $this->user || $this->tag || $this->media || $this->source || $this->period): ?>  
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no posts that match your search criteria.');?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no posts submitted yet.');?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> something.', $this->url(array('action'=>'create'), 'post_general'));?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

 

