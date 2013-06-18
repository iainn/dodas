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
class Post_Form_Post_Edit_Photo extends Post_Form_Post_Create_Photo
{

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Photo Post')
      ->setDescription('Please fill out the form below to edit this photo.');
   
    
    $this->photo->setLabel('New Photo');
    $this->photo->setDescription(null);
    $this->removeElement('url');
    $this->removeElement('thumb');      
      
    $this->adjustEditButtons();  
  }
  
  public function isValid($data)
  {
    $valid = Post_Form_Post_Create_Abstract::isValid($data);
    return $valid;
  }  
  
}
