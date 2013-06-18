<?php defined('_ENGINE') or die('Access Denied'); return array (
  'adapter' => 'mysqli',
  'params' => 
  array (
    'host' => 'dodas-dev.cksgjld851ua.us-west-2.rds.amazonaws.com',
    'username' => 'dodas_dev',
    'password' => 'Starwars123',
    'dbname' => 'dodas_dev',
    'charset' => 'UTF8',
    'adapterNamespace' => 'Zend_Db_Adapter',
  ),
  'isDefaultTableAdapter' => true,
  'tablePrefix' => 'engine4_',
  'tableAdapterClass' => 'Engine_Db_Table',
); ?>
