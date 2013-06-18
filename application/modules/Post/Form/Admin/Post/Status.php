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
 
 
 
class Post_Form_Admin_Post_Status extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Update Post Status')
      ->setDescription("This form allows you to modify the post's status.")
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Radio', 'status', array(
      'label' => 'Status',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => Post_Model_Post::getStatusTypes(),
    ));

    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'post' => true,
      'prependText' => ' or ',
      'href' => '',
      'link' => true,
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}