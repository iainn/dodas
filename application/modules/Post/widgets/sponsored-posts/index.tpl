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

  <?php $this->headScript()->appendFile('application/modules/Radcodes/externals/scripts/ticker.js') ?>
  
  <div class="post_sponsored_posts">
    <ul id="<?php echo $this->widget_name?>" class="post_sponsored_slides">
      <?php foreach ($this->paginator as $post): ?>
        <li>
          <?php if ($post->photo_id): ?>
          <div class="post_photo">
            <?php echo $this->itemPhoto($post, 'thumb.normal'); ?>
          </div>
          <?php endif; ?>    
          <div class="post_content">
            <div class="post_title">
              <?php echo $this->htmlLink($post->getHref(), $this->radcodes()->text()->truncate($post->getTitle(), 56)); ?>
            </div>
            <div class="post_meta">
              <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 'shows' => 'date, owner'))?>
            </div>
          </div>
        </li>  
      <?php endforeach; ?>  
    </ul>
    
  </div>

  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
    	<?php echo $this->widget_name?>Ticker = new radcodesNewsTicker('<?php echo $this->widget_name?>', {speed:1000,delay:15000,direction:'vertical'});
    });
    </script>
    <div class="post_sponsored_posts_action">
      <a href="javascript: void(0);" onclick="<?php echo $this->widget_name?>Ticker.next(); return false;"><?php echo $this->translate("Next &raquo;")?></a>
    </div>    
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no sponsored posts.');?>
    </span>
  </div>
<?php endif; ?>