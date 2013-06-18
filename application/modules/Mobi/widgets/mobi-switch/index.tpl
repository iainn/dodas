<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Charlotte
 */
?>
<?php if( $this->mobile ) { ?>
  <?php echo $this->htmlLink($this->url().'?mobile=0', $this->translate('Full Site'))?>
<?php } else { ?>
  <?php echo $this->htmlLink($this->url().'?mobile=1', $this->translate('Mobile Site'))?>
<?php } ?>