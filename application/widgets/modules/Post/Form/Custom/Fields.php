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
 
 
 
class Post_Form_Custom_Fields extends Fields_Form_Standard
{
  public $_error = array();

  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';

  public function init()
  { 
    // custom post fields
    if (!$this->_item){
      $post_item = new Post_Model_Post(array());
      $this->setItem($post_item);
    }
    parent::init();

    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ; //->addDecorator($decorator);
    }
  }
}