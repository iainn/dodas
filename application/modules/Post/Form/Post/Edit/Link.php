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
class Post_Form_Post_Edit_Link extends Post_Form_Post_Create_Link
{

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Link Post')
      ->setDescription('Please fill out the form below to edit this link.');
   
    $this->url->setAttrib('readonly', 'readonly');
    $this->url->setAttrib('class', 'uneditable');
    $this->url->setDescription('You are not allowed to change URL.');

    $this->photo->setLabel('New Photo');
    
    $this->removeElement('thumb');
    
    $this->adjustEditButtons();
  }
  
}
