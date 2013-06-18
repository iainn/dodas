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
 
 
 
class Post_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'post';
  
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
    

    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => $i++,
    ));

    
    $this->addElement('Hidden', 'character', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'tag', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'user', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'source', array(
      'order' => $i++,
    ));
    
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
  
    $categories = Engine_Api::_()->getItemTable('post_category')->getDoubleOptionsAssoc();
    $double_select = new Radcodes_Form_Element_DoubleSelect('category', array(
      'doubleOptions' => $categories,
      'defaultChildMessage' => '',
      'defaultParentMessage' => '',
      'label' => 'Category',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    $this->addElement($double_select);    
    
    $this->addElement('Select', 'media', array(
      'label' => 'Media Type',
      'multiOptions' => array('' => '') + Post_Model_Post::getMediaTypes(),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
   
    $this->addElement('Select', 'period', array(
      'label' => 'Period',
      'multiOptions' => array(
                    '' => 'All Time',
                    '24hrs' => 'Last 24 Hours',
                    'week' => '7 Days',
                    'month' => '30 Days',
                    'quarter' => '3 Months',
                    'year' => '12 Months',
                  ),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array('' => '') + Post_Form_Helper::getOrderOptions(),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('post.listorder', 'trending'),
    ));
    
    /*
    $seperator1 = $this->getElement('separator1');
    $this->removeElement('separator1');
    $seperator1->setOrder($i++);
    $this->addElement($seperator1);
		*/
    if (count($this->_fieldElements)) {
      $this->_order['separator1'] = $i++;
    }
    else {
      $this->removeElement('separator1');
    }
    
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
  
  
  public static function getDistanceOptions()
  {
    $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting('post.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE);
    
    $distances = array(''=>"");
    $distance_ranges = array(5,10,25,50,100,250,500);
    foreach ($distance_ranges as $distance) {
      $distances[$distance] = "$distance $unit";
    }
    
    return $distances;
  }
}