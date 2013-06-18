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
<?php $post = $this->post; ?>
<div class="post_profile_options">
  <?php if ($this->canEdit): ?>
    <?php echo $this->htmlLink($post->getHref(array('action'=>'edit')), $this->translate('Edit Post'), array('class'=>'buttonlink icon_post_edit'))?>
  <?php endif; ?>
  <?php if ($this->canDelete): ?>
    <?php echo $this->htmlLink($post->getHref(array('action'=>'delete')), $this->translate('Delete Post'), array('class'=>'buttonlink icon_post_delete'))?>
  <?php endif; ?>
</div>
