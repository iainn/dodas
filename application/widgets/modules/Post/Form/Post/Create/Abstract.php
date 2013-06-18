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
abstract class Post_Form_Post_Create_Abstract extends Engine_Form
{
  public $_error = array();
  protected $_media;
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }
  
  public function init()
  {
    $view = Zend_Registry::get('Zend_View');
    
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Add New Post')
      ->setDescription('Please fill out the form below to create a new post.')
      ->setAttrib('name', 'posts_create')
      ->setAttrib('class', 'global_form posts_create')
      ->setAttrib('id', 'posts_create_'.$this->getMedia());
  }
  
  protected function addErrorJsField()
  {
    $this->addElement('Dummy', 'error_js', array(
      'content' => '',
      'decorators' => array(
        array('HtmlTag', array('tag'=>'div', 'id'=>'posts_post_create_error_js'))
      )
    ));    
  }

  protected function addUrlField($params = array())
  {
//     Link URL /
    $this->addElement('Text', 'url', array_merge(array(
      'label' => 'URL',
      'description' => 'Paste or type in the link URL you want to share',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
      //  'NotEmpty',
        new Engine_Validate_Callback(array($this, 'validateUrl')),
      ),
    ), $params));
    $this->url->getDecorator("Description")->setOption("placement", "append"); 
  }
  
  protected function addPhotoField($params = array())
  {
    $this->addElement('File', 'photo', array_merge(array(
      'label' => 'Photo'
    ), $params));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg'); 
    $this->photo->getDecorator("Description")->setOption("placement", "append"); 
    

  }
  
  protected function addThumbField($params = array())
  {
    $this->addElement('Hidden', 'thumb',array(
      'label'=>'Thumb',
      'autocomplete' => 'off',     
      'order' => 3000,
    ));
  }
  
  protected function addMainFields()
  {

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
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
      'label' => 'Description',
      //'description' => 'Be creative, and funny to attract more votes',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),      
    ));
    $this->description->getDecorator("Description")->setOption("placement", "append");        
    
    
    $this->addElement('Text', 'keywords',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate each tag with commas.',
      'filters' => array(
        //new Engine_Filter_Censor(),
      ),
    ));
    $this->keywords->getDecorator("Description")->setOption("placement", "append"); 

    $categories = Engine_Api::_()->getItemTable('post_category')->getDoubleOptionsAssoc();    
		$double_select = new Radcodes_Form_Element_DoubleSelect('category_id', array(
		  'doubleOptions' => $categories,
		  'defaultChildMessage' => 'Please select a sub-category',
		  'defaultParentMessage' => '',
			'label' => 'Category',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        new Engine_Validate_Callback(array($this, 'validateCategory')),
      ),
		));
		$this->addElement($double_select);  

		$this->addElement('Hidden', 'media', array(
		  'value' => $this->getMedia()
		));
  }
  

  
  protected function addPostSubform($class)
  {
    // Add subforms
    if( !$this->_item ) {
      $customFields = new Post_Form_Custom_Fields();
    } else {
      $customFields = new Post_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == $class ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
  }
  
  protected function addPrivacyFields()
  {
    $field_order = 1000;
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;   
    // View
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );
    
    
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
            'description' => 'Who may see this post?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
    
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
            'description' => 'Who may post comments on this post?',
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
    
  }
  
  protected function addActionButtons()
  {
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Publish Post',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $create_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'create'),'post_general',true);
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Start Over',
      'link' => true,
      'href' => $create_url,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit','cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
  }
  
  
  public function validateCategory($value)
  {
    $multiChildOptions = $this->category_id->getMultiChildOptions();
    if ($value && array_key_exists($value, $multiChildOptions)) {
      $this->category_id->getValidator('Engine_Validate_Callback')->setMessage('Please select a sub-category - it is required.');
      return false;
    }
    
    return true;
  }
  
  public function validateUrl($value)
  {
    $validator = $this->url->getValidator('Engine_Validate_Callback');
    
    // Not string?
    if ( !is_string($value) || empty($value) ) {
      $validator->setMessage('Please complete this field - it is required.');
      return false;
    }

    if (!Zend_Uri::check($value)) {
      $validator->setMessage('The URL appears to be invalid.');
      return false;
    }    
    
    $validate_url_format = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.validateurl', '1');
    
    $reciprocal = (int) Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'link', 'reciprocal');    
    if ($validate_url_format || $reciprocal)
    {
      try {
        $client = new Zend_Http_Client($value, array(
          'maxredirects' => 2,
          'timeout'      => 15,
        ));
  
        // Try to mimic the requesting user's UA
        $client->setHeaders(array(
          'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
          'X-Powered-By' => 'Zend Framework'
        ));
  
        $response = $client->request();
        
        if ($reciprocal)
        {
          $request = Zend_Controller_Front::getInstance()->getRequest();
          $domain = $request->getHttpHost();
          $domain = str_replace('www.', '', $domain);
          $body = $response->getBody();
          if (!$this->containLink($body, $domain)) {
            $validator->setMessage('Could not find a reciprocal link to this site on inputted URL.');
            return false;
          }
        }
        
      }
      catch(Exception $e)
      {
        $validator->setMessage('Could not connect to URL server.');
        return false;
      }    
    }

    $duplicate_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.duplicateurl', '0');

    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ($duplicate_check) {
      $params = array(
        'url' => $value,
      );
      if ($duplicate_check == 1) {
        $params['user'] = $viewer;
        $error_message = 'You have already submited this URL.';
      }
      else {
        $error_message = 'This URL has already been submitted.';
      }
      
      $paginator = Engine_Api::_()->link()->getLinksPaginator($params);
      if ($paginator->getTotalItemCount() > 0) {
        $validator->setMessage($error_message);
      }
    }
    
    $blacklist_domains = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.blacklisteddomains', '');
    $blacklist_domains = explode(',', str_replace(" ", "", $blacklist_domains));
    
    $host = @parse_url($value, PHP_URL_HOST);
    if (in_array($host, $blacklist_domains)) {
      $validator->setMessage('This domain name is not allowed to submit.');
      return false;
    }
    
    return true;
  }  
  
  public function getMedia()
  {
    return $this->_media;
  }
  
  protected function adjustEditButtons()
  {
    $this->submit->setLabel('Save Changes');

    $this->cancel->setLabel('view');
    $this->cancel->setAttrib('href', $this->_item->getHref());
  }
}