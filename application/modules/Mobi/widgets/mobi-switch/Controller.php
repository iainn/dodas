<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mobi_Widget_MobiSwitchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $session = new Zend_Session_Namespace('mobile');
    $this->view->mobile = $session->mobile;
  }

}