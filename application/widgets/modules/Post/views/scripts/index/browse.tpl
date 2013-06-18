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
    <?php echo $this->translate("Browse Posts: %s", $this->translate($this->categoryObject->getTitle())); ?>
  <?php else: ?>
    <?php echo $this->translate("Browse Posts"); ?>
  <?php endif; ?>  
  </span>
</h3>    

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <?php if (true && ($this->user || $this->tag || $this->source)): ?>
  <div class="posts_result_pager">
    <?php echo $this->translate('Showing posts'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'browse', 'tag'=>$this->tag), 'post_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>    
      <?php if ($this->source): ?>
        <?php echo $this->translate('with %s source', $this->htmlLink('http://'.$this->source,
          $this->source
        ), array('target'=>"_blank"));?>
      <?php endif; ?>    
      <?php if ($this->user): ?>
        <?php if ($this->userObject instanceof User_Model_User): ?>
          <?php echo $this->translate('by %s', $this->userObject->toString()); ?>
        <?php else: ?>
          <?php echo $this->translate('by #%s', $this->user); ?>
        <?php endif; ?>
      <?php endif; ?>     
    
    <?php echo $this->htmlLink(array('action'=>'browse', 'route'=>'post_general'), $this->translate('(x)'))?>
  </div>
  <?php endif; ?>
  
  <ul class="post_rows">
  <?php foreach ($this->paginator as $post): ?>
    <li><?php echo $this->partial('_post_row.tpl', 'post', array('post' => $post,
      'shows' => 'photo, votes, meta, owner, comments, date'
    ))?></li>
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

 

