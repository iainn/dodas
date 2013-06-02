<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<h2>
  <?php echo $this->translate('%s\'s Polls', $this->htmlLink($this->owner, $this->owner->getTitle())) ?>
</h2>

<div class='polls_view'>
  <h3>
    <?php echo $this->poll->title ?>

    <?php if( $this->poll->closed ): ?>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
    <?php endif ?>
  </h3>

  <div class="poll_desc">
    <?php echo $this->poll->description ?>
  </div>

  <?php
    // poll, pollOptions, canVote, canChangeVote, hasVoted, showPieChart
    echo $this->render('_poll.tpl')
  ?>
</div>


<script type="text/javascript">
  $$('.core_main_poll').getParent().addClass('active');
</script>
