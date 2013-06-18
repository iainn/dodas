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
class Post_Form_Post_Edit extends Post_Form_Post_Create
{
  public $_error = array();
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }
  
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Post')
         ->setDescription('Edit your post below, then click "Save Changes" to save your post.');

    $user = Engine_Api::_()->user()->getViewer();       

    
    $this->submit->setLabel('Save Changes');

    $this->cancel->setLabel('view');
    $this->cancel->setAttrib('href', $this->_item->getHref());
  }
}