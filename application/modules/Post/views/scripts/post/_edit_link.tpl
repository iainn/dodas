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

<?php $this->headScript()->appendFile('application/modules/Post/externals/scripts/suggest.js') ?>


<?php echo $this->partial('index/_js_fields.tpl', 'post', array())?>
<?php echo $this->form->render($this);?>
  