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
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Sponsored Post?") ?></h3>
      <p>
        <?php echo $this->translate("Would you like to mark this post as sponsored?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->post->getIdentity()?>" />

        <button type='submit' name="sponsored" value="yes"><?php echo $this->translate("Yes") ?></button>
        <button type='submit' name="sponsored" value="no"><?php echo $this->translate("No") ?></button>

        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>