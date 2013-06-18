<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     Charlotte
 */
return array(
  array(
    'title' => 'Mobile Main Menu',
    'description' => 'Shows the mobile main menu. You can edit its contents in your menu editor.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobi.mobi-menu-main',
  ),
  array(
    'title' => 'Mobile Footer Menu',
    'description' => 'Shows the mobile footer menu. You can edit its contents in your menu editor.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobi.mobi-footer',
  ),
  array(
    'title' => 'Mobile/Full Site Switcher',
    'description' => 'Shows the link to the mobile or full site.',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobi.mobi-switch',
  ),
  array(
    'title' => 'Mobile Profile Options',
    'description' => 'Displays a list of actions that can be performed on a member on their mobile profile (add as friend, etc).',
    'category' => 'Mobile',
    'type' => 'widget',
    'name' => 'mobi.mobi-profile-options',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
) ?>