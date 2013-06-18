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
class Post_Form_Post_Create_Photo extends Post_Form_Post_Create_Abstract
{
  protected $_media = Post_Model_Post::MEDIA_PHOTO;
  
  public function init()
  {
    parent::init();
    
    $field_order = 10000;
    
    $view = Zend_Registry::get('Zend_View');

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Post New Photo')
      ->setDescription('Please fill out the form below to post a new photo.');
   
    $or = $view->translate('or');
    $upload_a_photo = $or . ' <a id="post_create_photo_toggle_upload">'.$view->translate('upload a photo').'</a>';  
    $add_a_photo_link = $or . ' <a id="post_create_photo_toggle_add">'.$view->translate('add a photo link').'</a>';
    
    $this->addErrorJsField();  
    $this->addUrlField(array(
      'label' => 'Photo URL',
      //'description' => 'Paste a link (URL) to your photo here'
      'description' => $upload_a_photo,
      'allowEmpty' => true,
      'required' => false,
    ));  
    $this->addPhotoField(array(
      'label' => 'Photo File',
      'description' => $add_a_photo_link,    
    ));
    $this->addThumbField();
    $this->addMainFields();
    $this->addPostSubform('Post_Form_Post_Create_Photo');
    $this->addPrivacyFields();
    $this->addActionButtons();

    $this->url->getDecorator("Description")->setOption("escape", false); 
    $this->photo->getDecorator("Description")->setOption("escape", false); 
  }
  
  public function isValid($data)
  {
    if (!$this->photo->getValue() && empty($data['url'])) {
      //$this->addError('Photo or Thumb is required.');
      if (!$this->photo->hasErrors()) {
        $this->photo->addError('Please select a file, or enter a photo URL.');
      }
    }
    
    $valid = parent::isValid($data);
    
    if ($valid) {
      if ($this->photo->getValue()) {
        $this->url->setValue('');
        $this->thumb->setValue('');
      }
    }
    
    return $valid;
  }
}
