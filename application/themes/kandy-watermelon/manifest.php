<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Kandy Theme
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9714 2012-05-07 23:17:50
 * @author     
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'kandy-watermelon',
    'version' => '4.2.5',
    'revision' => '$Revision: 9714 $',
    'path' => 'application/themes/kandy-watermelon',
    'repository' => 'socialengine.com',
    'title' => 'Kandy Watermelon',
    'thumb' => 'thumb.png',
    'author' => 'Webligo Developments',
    'changeLog' => array(
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/kandy-watermelon',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>