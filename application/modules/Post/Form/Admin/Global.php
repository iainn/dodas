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
 
 
 
class Post_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'post_license', array(
      'label' => 'Post License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('post.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'validators' => array(
        new Radcodes_Lib_Validate_License('post'),
      ),
    ));
     
      
    $this->addElement('Radio', 'post_timefactor', array(
      'label' => 'Voting Time Factor',
      'description' => 'Based on the engagement of your members, how many votes a hottest post will get in a day?',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('post.timefactor', 30),
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(
        50 => 'Barely any votes',
        40 => 'A few votes',
        30 => 'Dozens of votes',
        20 => 'Hundreds of votes',
        10 => 'Thousands of votes' 
      ),
    ));

    $this->addElement('Text', 'post_postperpage', array(
      'label' => 'Posts Per Page',
      'description' => 'How many posts will be shown per page? (Enter a number between 1 and 100)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('post.postperpage', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));    

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}