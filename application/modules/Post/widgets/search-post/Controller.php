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

class Post_Widget_SearchPostController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    
    $this->view->form = $form = new Post_Form_Filter_Post();
  	
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