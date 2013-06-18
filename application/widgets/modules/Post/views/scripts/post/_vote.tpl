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
$viewer = $this->viewer();
$post = $this->post;

$this->headScript()
    ->appendFile($this->baseUrl().'/application/modules/Post/externals/scripts/core.js');

if ($viewer->getIdentity()) {
  $vote = $post->votes()->getVote($viewer);
  if ($vote instanceof Post_Model_Vote) {
    $class = "post_feedback_voted_" . ($vote->helpful ? 'up' : 'down');
  }
  else {
    $class = "post_feedback_voted_none";
  }
}
else {
  $class = "post_feedback_voted_none";
}

?>
  <ul class="post_feedback_actions <?php echo $class;?>">
  <?php /*if ($this->success && false): ?>
    <li class="post_feedback_message">
      <?php if ($this->new): ?>
        <?php echo $this->translate('Saved. Thanks!')?>
      <?php else: ?>
        <?php if ($this->same): ?>
          <?php echo $this->translate('Already voted!')?>
        <?php else: ?>
          <?php echo $this->translate('Vote updated!')?>
        <?php endif; ?>
      <?php endif; ?>  
    </li>
  <?php endif; */ ?>
    <li class="post_feedback_up">
      <a href="javascript:void(0)" onclick="en4.postvote.vote(this, <?php echo $post->getIdentity()?>, '<?php echo $post->getHref(array('action'=>'vote', 'helpful'=>1)) ?>')">
        <span><?php echo $post->helpful_count; ?></span>
      </a>
    </li>
    <li class="post_feedback_point">
      <span><?php echo $post->point_count; ?></span>
    </li>
    <li class="post_feedback_down">
      <a href="javascript:void(0)" onclick="en4.postvote.vote(this, <?php echo $post->getIdentity()?>, '<?php echo $post->getHref(array('action'=>'vote', 'helpful'=>0)) ?>')">
        <span><?php echo $post->nothelpful_count; ?></span>
      </a>
    </li>
  </ul>