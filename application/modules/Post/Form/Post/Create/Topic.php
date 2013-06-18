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
class Post_Form_Post_Create_Topic extends Post_Form_Post_Create_Abstract
{
  protected $_media = Post_Model_Post::MEDIA_TOPIC;
  
  public function init()
  {
    parent::init();
    
    $field_order = 10000;
    
    $view = Zend_Registry::get('Zend_View');
    
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Post New Topic')
      ->setDescription('Please fill out the form below to post a new topic.');
   
    $this->addMainFields();
    $this->addPostSubform('Post_Form_Post_Create_Topic');
    $this->addPrivacyFields();
    $this->addActionButtons();
    
    $this->description->setLabel('Your Message');

  }
  
  
}
