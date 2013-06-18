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
class Post_Form_Post_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $field_order = 10000;
    
    $view = Zend_Registry::get('Zend_View');
    
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Post New Post')
      ->setDescription('All the definitions here were written by other members just like you. Now is your chance to add your own!')
      ->setAttrib('name', 'posts_create');
   

    $this->addElement('Text', 'title', array(
      'label' => 'Post',
      //'description' => '',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StringTrim',
      )
    ));
    $this->title->getDecorator("Description")->setOption("placement", "append");       


    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Definition',
      'description' => 'Be creative, and funny to attract more votes',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        //new Engine_Filter_Censor(),
      ),      
    ));
    $this->description->getDecorator("Description")->setOption("placement", "append");        
    
    $this->addElement('Textarea', 'example', array(
      'label' => 'Examples',
      'description' => 'Please provide some example usages',
      //'allowEmpty' => false,
      //'required' => true,
      'filters' => array(
        'StripTags',
        //new Engine_Filter_Censor(),
      ),      
    ));
    $this->example->getDecorator("Description")->setOption("placement", "append");       
    
    $this->addElement('File', 'photo', array(
      'label' => 'Post Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');    
    
    
    $this->addElement('Text', 'keywords',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'List other synonyms, antonyms, related posts and misspellings, separated by commas.',
      'filters' => array(
        //new Engine_Filter_Censor(),
      ),
    ));
    $this->keywords->getDecorator("Description")->setOption("placement", "append");    

    
    // Add subforms
    if( !$this->_item ) {
      $customFields = new Post_Form_Custom_Fields();
    } else {
      $customFields = new Post_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Post_Form_Post_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
    
    
    // View
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );
    
    /*
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('post', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    // View
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => $field_order++,));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this definition?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    */
    
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('post', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    // Comment
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions), 'order' => $field_order++,));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this definition?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
		
    
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Show this post in browse and search results',
      'value' => 1
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Definition',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

  
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit','cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

  }

 
}