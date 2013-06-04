<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Membership.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Network_Model_DbTable_Membership extends Core_Model_DbTable_Membership
{
  protected $_type = 'network';

  public function isUserApprovalRequired()
  {
    return false;
  }

  public function isResourceApprovalRequired()
  {
    return false;
  }

  protected function _delete(){

  }
}
