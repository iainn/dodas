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
class Post_Form_Post_Create_Video extends Post_Form_Post_Create_Abstract
{
  protected $_media = Post_Model_Post::MEDIA_VIDEO;
  
  public function init()
  {
    parent::init();
    
    $field_order = 10000;
    
    $view = Zend_Registry::get('Zend_View');
    
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Post New Video')
      ->setDescription('Please fill out the form below to post a new video.');
   
    $this->addErrorJsField();  
    $this->addUrlField(array(
      'label' => 'Video URL',
      'description' => 'Paste the link to your video here',
    ));  
    //$this->addPhotoField();
    $this->addThumbField();
    $this->addMainFields();
    $this->addPostSubform('Post_Form_Post_Create_Video');
    $this->addPrivacyFields();
    $this->addActionButtons();
  }
  
}
