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
class Post_Form_Post_Edit_Video extends Post_Form_Post_Create_Video
{

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Video Post')
      ->setDescription('Please fill out the form below to edit this video.');
      
    $this->url->setAttrib('readonly', 'readonly');
    $this->url->setAttrib('class', 'uneditable');
    $this->url->setDescription('You are not allowed to change URL.');      
      
    $this->removeElement('thumb');    
    
    $this->adjustEditButtons();
  }
  
}
