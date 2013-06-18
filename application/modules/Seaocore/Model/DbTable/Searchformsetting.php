<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchformsetting.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_DbTable_Searchformsetting extends Engine_Db_Table {

   public function getFieldsOptions($module, $name) {
     return $this->fetchRow(array('module = ?' => $module, 'name = ?' => $name));
   }
}

?>