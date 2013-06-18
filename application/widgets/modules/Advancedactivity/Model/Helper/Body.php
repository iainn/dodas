<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Body.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

class Advancedactivity_Model_Helper_Body extends Advancedactivity_Model_Helper_Abstract
{
  /**
   * Body helper
   * 
   * @param string $body
   * @return string
   */
  public function direct($body)
  {
    if( Zend_Registry::isRegistered('Zend_View') ) {
      $view = Zend_Registry::get('Zend_View');
      $body = $view->viewMore($body,null,null,null,false);
    }
    return '<span class="feed_item_bodytext">' . $body . '</span>';
  }
}