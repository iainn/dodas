<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Default
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9378 2011-10-13 22:50:30Z john $
 * @author     Alex
 */
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'modern',
    'version' => '4.5.0',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/modern',
    'repository' => 'socialengine.com',
    'title' => 'Modern',
    'thumb' => 'theme.jpg',
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
      'application/themes/modern',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
) ?>