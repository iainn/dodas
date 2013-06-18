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
class Mobi_Widget_MobiMenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('mobi_main');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }

  public function getCacheKey()
  {
    //return Engine_Api::_()->user()->getViewer()->getIdentity();
  }
}