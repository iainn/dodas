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
class Post_Form_Post_Edit_Topic extends Post_Form_Post_Create_Topic
{

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Topic Post')
      ->setDescription('Please fill out the form below to edit this topic.');

    $this->adjustEditButtons();
  }
  
  
}
