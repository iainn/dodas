<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_Form_Admin_Map extends Engine_Form
{
	public function init() {
	    //GENERAL HEADING
    $this
            ->setTitle('Google Maps Settings')
            ->setDescription('Here, you can customize the settings for Google Maps on your site. (Note: These settings will only affect the Google Maps that comes in plugins from SocialEngineAddOns installed on your site.)');
            
    //ENTER THE GOOGLE MAP API KEY
    $this->addElement('Text', 'seaocore_google_map_key', array(
        'label' => 'Google Places API Key',
        'description' => 'The Google Places API Key for your website. [Please visit the "Guidelines for configuring Google Places API key" mentioned   above to see how to obtain these credentials.]',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key'),
        'required' => true
    ));

    $this->getElement('seaocore_google_map_key')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    //COLOR VALUE FOR BACKGROUND COLOR
    $this->addElement('Text', 'seaocore_tooltip_bgcolor', array(
			'decorators' => array(
				array('ViewScript', array(
					'viewScript' => 'admin-settings/rainbow-color/_formImagerainbowTooltipBg.tpl',
					'class' => 'form element'
				)
		  ))
    ));
    
    //SUBMIT BUTTON
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}