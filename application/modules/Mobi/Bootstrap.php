<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Bootstrap.php 9747 2012-07-26 02:08:08Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mobi_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function _bootstrap()
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Mobi_Plugin_Core);
  }
}