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
 
 
 
class Post_Form_Filter_Favorites extends Post_Form_Search
{
  public function init()
  {
    parent::init();

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'favorites'),'post_general',true));
  }
}