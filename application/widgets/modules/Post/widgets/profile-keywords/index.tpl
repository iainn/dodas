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
<div class="post_profile_keywords">
  <?php echo $this->partial('index/_keywords.tpl', 'post', array('post' => $post))?>
</div>