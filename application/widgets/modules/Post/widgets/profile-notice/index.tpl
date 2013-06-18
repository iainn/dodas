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
    <div class="tip">
      <span>
<?php if (!$this->post->isApprovedStatus()):?>
  <?php echo $this->translate('This post is not yet approved, and not viewable by public.')?>
<?php endif; ?>
      </span>
    </div>  