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



<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of post postings allowed.');?>
      <?php echo $this->translate('If you would like to create a new post, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'post_general', true));?>
    </span>
  </div>
  <br/>
<?php elseif ($this->media && $this->form): ?>  

  <?php include("_create_{$this->media}.tpl"); ?>
  
<?php else:?>

	<div class='global_form' id="posts_create_wrapper">
	  <div>
	    <div class="post_success_panel">
	      <h3><?php echo $this->translate('Create New Post');?></h3>
	      <p>
	        <?php echo $this->translate('What would you like to post?');?>
	      </p>
	      <br />
        <ul class="post_new_choose">
          <li class="post_new_choose_topic">
            <a href="<?php echo $this->url(array('action'=>'create', 'media'=>'topic'), 'post_general', true)?>" target="_top">
              <span><?php echo $this->translate('Topic')?></span>
            </a>
          </li>
          <li class="post_new_choose_link">
            <a href="<?php echo $this->url(array('action'=>'create', 'media'=>'link'), 'post_general', true)?>" target="_top">
              <span><?php echo $this->translate('Link')?></span>
            </a>
          </li>
          <li class="post_new_choose_photo">
            <a href="<?php echo $this->url(array('action'=>'create', 'media'=>'photo'), 'post_general', true)?>" target="_top">
              <span><?php echo $this->translate('Photo')?></span>
            </a>
          </li>
          <li class="post_new_choose_video">
            <a href="<?php echo $this->url(array('action'=>'create', 'media'=>'video'), 'post_general', true)?>" target="_top">
              <span><?php echo $this->translate('Video')?></span>
            </a>
          </li>
        </ul>
	    </div>
	  </div>
	</div>

<?php endif; ?>


  

