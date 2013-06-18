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
<div class='post_profile_related_posts'>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="post_list">
  <?php foreach ($this->paginator as $post): ?>
    <li>
      <div class="post_list_title"><?php echo $post->toString(); ?></div>
      <div class="post_list_meta">
        <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 
        	'show_owner'=>true, 
        	'show_date'=>true,
        ))?>
      </div>
    </li>
  <?php endforeach;?>  
  </ul>
<?php endif;?>
</div>