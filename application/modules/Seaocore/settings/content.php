<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-09-26 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$content_array = array(
    array(
        'title' => $view->translate('Photos Lightbox Viewer'),
        'description' => $view->translate('This widget displays the lightbox on your site. It is recommended to not to remove this widget from the header.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.seaocores-lightbox',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'hidden',
                    'execute',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'hidden',
                    'cancel',
                    array(
                        'label' => ''
                    )
                ),
            )
        ),
    ),
    array(
        'title' => $view->translate('SocialEngineAddOns Comments'),
        'description' => $view->translate('Shows the comments about an item.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.seaocores-comments',
        'defaultParams' => array(
            'title' => $view->translate('Comments')
        ),
        'requirements' => array(
            'subject',
        ),
    ),
// 		array(
// 			'title' => $view->translate('Nested Comments'),
// 			'description' => $view->translate('Shows the nested comment about an item.'),
// 			'category' => 'SocialEngineAddOns-Core',
// 			'type' => 'widget',
// 			'name' => 'seaocore.seaocores-nestedcomments',
// 			'defaultParams' => array(
// 				'title' => 'Nested Comments'
// 			),
// 			'requirements' => array(
// 				'subject',
// 			),
// 		),
    array(
        'title' => $view->translate('Scroll To Top'),
        'description' => $view->translate('This widget displays a "Scroll To Top" button when users scroll down to the bottom of the page. This widget should be placed at the height of your page where you want the user to be scrolled-to upon clicking.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'Text',
                    'mouseOverText',
                    array(
                        'label' => $view->translate('Enter the HTML title that you want to display when users mouse-over on "Scroll to Top" button.'),
                        'value' => $view->translate('Scroll to Top'),
                    )
                ),                
            )
        )
    ),
    array(
        'title' => $view->translate('Column Width'),
        'description' => $view->translate('This widget is used to set the width of the columns on a page. You can enter the width from the Edit Settings of this widget.'),
        'category' => 'SocialEngineAddOns-Core',
        'type' => 'widget',
        'name' => 'seaocore.layout-width',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'text',
                    'layoutWidth',
                    array(
                        'label' => $view->translate('Enter the width of this column.'),
                         'validators' => array(
                          array('Int', true),
                          array('GreaterThan', true, array(1)),
                      ),
                    )
                ),
                array(
                    'select',
                    'layoutWidthType',
                    array(
                        'label' => $view->translate('Selete the type of width'),
                    'multiOptions' => array(
                                  'px' => 'px',
                                  '%' => '%',
                              ),
                     'value' => 'px',
                    )
                ),
            )
        )
    ),
);
//if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')){
$content_array[] = array(
    'title' => $view->translate('SocialEngineAddOns Activity Feed'),
    'description' => $view->translate('Displays the activity feed.'),
    'category' => 'SocialEngineAddOns-Core',
    'type' => 'widget',
    'name' => 'seaocore.feed',
    'defaultParams' => array(
        'title' => $view->translate('What\'s New'),
    ),
);
//}
return $content_array;
?>