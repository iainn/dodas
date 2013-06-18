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

<?php if (count($this->voters)): ?>

  <?php 
    // speed this up :-)
    Engine_Api::_()->user()->getUserMulti(array_keys($this->voters));
  ?>

  <ul class='post_top_voters'>
    <?php foreach ($this->voters as $voter): ?>
      <?php $user = $this->user($voter['user_id']); if (!$user->getIdentity()) continue; ?>
      <li>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class'=>'post_voter_photo')); ?>
        <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class'=>'post_voter_title'))?>
        <span class="post_voter_total">
         <?php echo $this->translate(array('%d vote', '%d votes', $voter['total']), $this->locale()->toNumber($voter['total']))?>
        </span> 
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no voters yet.');?>
    </span>
  </div>
<?php endif; ?>
