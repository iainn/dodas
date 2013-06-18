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
$tags = $this->post->getKeywordsArray();
?>
<?php if (!empty($tags) && count($tagMaps = $this->post->tags()->getTagMaps()) > 0): ?>
  <?php foreach ($tagMaps as $tagMap): $tag = $tagMap->getTag(); ?>
    <?php if (!empty($tag->text)): ?>
      <?php echo $this->htmlLink(array('route'=>'post_general', 'action'=>'browse', 'tag'=>$tag->tag_id), $tag->text, array('class'=>'post_item_tag')); ?>
    <?php endif;?>
  <?php endforeach; ?>
<?php endif;?>