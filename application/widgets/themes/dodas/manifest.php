<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'dodas',
    'version' => NULL,
    'revision' => '$Revision: 9714 $',
    'path' => 'application/themes/dodas',
    'repository' => 'socialengine.com',
    'title' => 'dodas',
    'thumb' => 'thumb.png',
    'author' => 'Dodas',
    'changeLog' => 
    array (
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/kandy-limeorange',
    ),
    'description' => 'dodas',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>