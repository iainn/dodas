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

  <?php $this->headScript()->appendFile('application/modules/Radcodes/externals/scripts/slideshow.js') ?>
  
  <div class="post_featured_posts">
    <div class="post_featured_mask">
      <div id="<?php echo $this->widget_name?>" class="post_featured_slides">
        <?php foreach ($this->paginator as $post): ?>
          <div class="post_featured_slide post_record_<?php echo $post->getIdentity();?>">
            <?php if ($post->photo_id): ?>
            <div class="post_photo">
              <?php echo $this->itemPhoto($post, 'thumb.profile'); ?>
            </div>
            <?php endif; ?>      
            <div class="post_content">
              <?php echo $this->partial('post/_feedback.tpl', 'post', array('post' => $post))?>
              <div class="post_title">
                <?php echo $this->htmlLink($post->getHref(), $this->radcodes()->text()->truncate($post->getTitle(), 72)); ?>
              </div>
              <div class="post_description">
                <?php echo $this->radcodes()->text()->truncate(str_replace("\n\n","\n",$post->getDescription()), 250); ?>
              </div>
              <div class="post_meta">
                <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 'shows' => 'date, owner, category, comments'))?>
              </div>
            </div>
          </div>  
        <?php endforeach; ?>  
      </div>
    </div>
    <?php if ($this->use_slideshow): ?>
      <p class="radcodes_slideshow_buttons" id="<?php echo $this->widget_name?>_buttons">
        <span id="<?php echo $this->widget_name?>_prev" class="radcodes_slideshow_button_prev"><span><?php echo $this->translate('&lt;&lt; Previous'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_play" class="radcodes_slideshow_button_play"><span><?php echo $this->translate('Play &gt;'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_stop" class="radcodes_slideshow_button_stop"><span><?php echo $this->translate('Stop'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_next" class="radcodes_slideshow_button_next"><span><?php echo $this->translate('Next &gt;&gt;'); ?></span></span>
      </p>      
    <?php endif; ?>
  </div>
  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
      var <?php echo $this->widget_name?>_width = $('<?php echo $this->widget_name?>').getSize().x;
      //alert(<?php echo $this->widget_name?>_width);
      $$('#<?php echo $this->widget_name?> div.post_featured_slide').each(function(el){
        el.setStyle('width', <?php echo $this->widget_name?>_width - 30);
      });
    	var <?php echo $this->widget_name?> = new radcodesNoobSlide({
    		box: $('<?php echo $this->widget_name?>'),
    		items: $$('#<?php echo $this->widget_name?> div.post_featured_slide'),
    		size: <?php echo $this->widget_name?>_width,
    		autoPlay: true,
    		interval: 8000,
    		addButtons: {
    			previous: $('<?php echo $this->widget_name?>_prev'),
    			play: $('<?php echo $this->widget_name?>_play'),
    			stop: $('<?php echo $this->widget_name?>_stop'),
    			next: $('<?php echo $this->widget_name?>_next')
    		},
    		onWalk: function(currentItem,currentHandle){
    		}
    	});
    });
    </script>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no featured posts.');?>
    </span>
  </div>
<?php endif; ?>