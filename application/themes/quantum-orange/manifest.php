<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Quantum Theme
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9714 2012-05-07 23:17:50
 * @author     
 */

return array (
  'package' => array (
    'type' => 'theme',
    'name' => 'quantum-orange',
    'version' => '4.3.0',
    'revision' => '$Revision: 9729 $',
    'path' => 'application/themes/quantum-orange',
    'repository' => 'socialengine.com',
    'title' => 'Quantum Orange',
    'thumb' => 'quantum_theme.png',
    'author' => 'Webligo Developments',
    'changelog' => array(    
    ),
    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
	'callback' => array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array (
      0 => 'application/themes/quantum-orange',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
); ?>