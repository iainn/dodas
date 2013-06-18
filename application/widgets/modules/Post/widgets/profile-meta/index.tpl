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
<div class="post_profile_meta">
  <?php echo $this->partial('index/_meta.tpl', 'post', array('post' => $post, 
  	'show_owner'=>true, 
  	'show_date'=>true,
    'show_category'=>true,
  	'show_share'=>false, 
  	'show_report'=>false,
    'show_likes'=>false,
    'show_views'=>true,
    'show_comments'=>false,
    'show_upvotes'=>true,
    'show_downvotes'=>true,
  ))?>
</div>