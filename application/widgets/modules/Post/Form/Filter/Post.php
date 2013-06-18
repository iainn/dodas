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
 
 
 
class Post_Form_Filter_Post extends Engine_Form
{
  public function init()
  {
    parent::init();

    $this->addDecorators(array(
      'FormElements',
      array('FormErrors', array('placement' => 'PREPEND')),  
      array(array('li' => 'HtmlTag'), array('tag' => 'ul')),
      array('HtmlTag', array('tag' => 'div', 'class' => 'field_search_criteria')),
      'Form',
    ));    
    
    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'posts_browse_filters field_search_criteria',
      ))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browseposts_criteria posts_browse_filters');
    

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'define'),'post_general',true));
    
    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;

    $this->addElement('Text', 'post', array(
      'label' => 'Search Post',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
  

    $j = 10000000;  
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
  }
}