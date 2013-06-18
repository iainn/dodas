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
$post = $this->post; 
?>
<div class="post_edit_info">
  <ul>
    <li>
      <?php echo $this->translate('ID: %s', $post->getIdentity())?>
    </li>
    <li>
      <?php echo $this->translate('Post: %s', $this->htmlLink($post->getHref(), $post->getTitle()))?>
    </li>
    <li>
      <?php echo $this->translate('Status: %s', $post->getStatusText())?>
    </li>
    <li>
      <?php echo $this->translate('Created: %s', $this->locale()->toDate($post->creation_date)); ?>
    </li>
    <li>
      <?php echo $this->translate('Comments: %s', $post->comment_count); ?>
    </li>
    <li>
      <?php echo $this->translate('Views: %s', $post->view_count); ?>
    </li>    
    <li>
      <?php echo $this->translate('Thumb Up: %s', $post->helpful_count); ?>
    </li>
    <li>
      <?php echo $this->translate('Thumb Down: %s', $post->nothelpful_count); ?>
    </li>    
  </ul>
</div>