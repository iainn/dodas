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
<div class="post_profile_tools">
  <?php echo $this->htmlLink(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'post', 'id' => $this->post->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'post_meta_action icon_post_share smoothbox')); ?>
  <?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->post->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'post_meta_action icon_post_report smoothbox')); ?>
</div>