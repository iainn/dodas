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
 
 
 
class Post_Form_Admin_Manage_Filter extends Engine_Form
{
  
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'post_admin_manage_filter',
        'class' => 'global_form_box',
      ));

    $this->addElement('Text', 'user', array(
      'label' => 'User',
    )); 
    
    
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
    ));     
    
    $this->addElement('Select', 'media', array(
      'label' => 'Media',
      'multiOptions' => array(
        '' => '',
      ) + Post_Model_Post::getMediaTypes(),      
    )); 
    
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => '',
      ) + Post_Model_Post::getStatusTypes(),      
    ));     
    
    
    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
      ),      
    ));      

    $this->addElement('Select', 'sponsored', array(
      'label' => 'Sponsored',
      'multiOptions' => array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
      ),      
    ));     
    
    
    
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array(
        '' => '',
      ) + Post_Form_Helper::getOrderOptions(),
    ));
    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
    
    $submit = new Engine_Form_Element_Button('filtersubmit', array('type' => 'submit'));
    $submit
      ->setIgnore(true)
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement($submit);
      /*
    $this->addElement('Hidden', 'order', array(
      'order' => 1001,
    ));
*/
    $this->addElement('Hidden', 'order_direction', array(
      'order' => 1002,
    ));
      
      
          
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'post', 'controller'=>'manage'), 'admin_default', true));
      
  }
  
}