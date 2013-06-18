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
  
  <ul class="post_rows">
  <?php foreach ($this->paginator as $post): ?>
    <li><?php echo $this->partial('_post_row.tpl', 'post', array('post' => $post,
      'shows' => 'photo, votes, meta, category, comments, date'
    ))?></li>
  <?php endforeach;?>
  </ul>
  
  <?php if ($this->showmemberpostslink): ?>
    <div class="post_profile_posts_link post_profile_posts_link_<?php echo $this->display_style; ?>">
      <?php echo $this->htmlLink(array('route'=>'post_general', 'action'=>'browse', 'user'=>$this->user->getIdentity()),
        $this->translate('View %s\'s Posts', $this->user->getTitle()),
        array('class'=>'buttonlink item_icon_post')
      )?>
    </div>    
  <?php endif; ?>  
<?php endif; ?>
 