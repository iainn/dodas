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

class Post_Widget_SearchFormController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    
    switch ($params['action'])
    {
      case 'hot':
        $form_class = 'Post_Form_Filter_Hot';
        break;
      
      case 'top':
        $form_class = 'Post_Form_Filter_Top';
        break;
        
      case 'new':
        $form_class = 'Post_Form_Filter_New';
        break;
        
      case 'favorites':
        $form_class = 'Post_Form_Filter_Favorites';
        break;
        
      default: 
        $form_class = 'Post_Form_Filter_Browse';
    }
    
    $this->view->form = $form = new $form_class();
  	
  	foreach (array('action','module','controller','rewrite') as $system_key) {
  	  unset($params[$system_key]);
  	}    
    
    // Populate form data
    if( $form->isValid($params) )
    {
      $params = $form->getValues();
    }    

  }

}