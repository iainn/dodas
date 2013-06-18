<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' => array(
        'type' => 'module',
        'name' => 'seaocore',
        'version' => '4.5.0p3',
        'path' => 'application/modules/Seaocore',
        'repository' => 'null',
        'title' => 'SocialEngineAddOns Core Plugin',
        'description' => 'SocialEngineAddOns Core Plugin',
        'author' => 'SocialEngineAddOns',
        'date' => 'Thu, 18 Nov 2010 18:33:08 +0000',
        'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Seaocore/settings/install.php',
            'class' => 'Seaocore_Installer',
        ),
        'directories' => array(
            'application/modules/Seaocore',
        ),
        'files' => array(
            'application/languages/en/seaocore.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'seaocore',
        'seaocore_tab',
        'seaocore_locationitems',
        'seaocore_reply',
        'seaocore_follow',
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            // 'event' => 'addActivity',
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Seaocore_Plugin_Core'
        ),
//         array(
//             'event' => 'onItemDeleteBefore',
//             'resource' => 'Seaocore_Plugin_Core',
//         ),
//         array(
//             'event' => 'onCoreCommentCreateAfter',
//             'resource' => 'Seaocore_Plugin_Core',
//         ),
        array(
            'event' => 'onCoreCommentDeleteBefore',
            'resource' => 'Seaocore_Plugin_Core',
        ),
    ),
    'routes' => array(
			'seaocore_image_specific' => array(
					'route' => 'seaocore/photo/view/*',
					'defaults' => array(
							'module' => 'seaocore',
							'controller' => 'photo',
							'action' => 'view'
					),
					'reqs' => array(
							'action' => '(view)',
					),
			),
			'seaocore_viewmap' => array(
					'route' => 'seaocore/index/view-map/:id/*',
					'defaults' => array(
							'module' => 'seaocore',
							'controller' => 'index',
							'action' => 'view-map'
					)
			),
			'seaocore_like' => array(
				'route' => 'seaocore/like/:action/*',
				'defaults' => array(
						'module' => 'seaocore',
						'controller' => 'like',
						//'action' => 'index',
				),
				'reqs' => array(
						'action' => '(global-likes)',
				),
			),
      'seaocore_resend_invite' => array(
          'route' => 'seaocore/invite/resendinvite',
          'defaults' => array(
              'module' => 'seaocore',
              'controller' => 'invite',
              'action' => 'resendinvite'
          )
      ),
    )
);
?>
