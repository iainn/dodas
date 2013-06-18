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

defined('RADCODES_ROUTE_POST_SINGLE')
  || define('RADCODES_ROUTE_POST_SINGLE', 'post');
  
defined('RADCODES_ROUTE_POST_PLURAL')  
  || define('RADCODES_ROUTE_POST_PLURAL', 'posts');

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'post',
    'version' => '4.0.1',
    'path' => 'application/modules/Post',
    'repository' => 'radcodes.com',

    'title' => 'Post / Voting & Sharing / Buzz Plugin',
    'description' => 'This plugin let you run a social news and entertainment website where members submit content in the form of either a link or a text post.',
    'author' => 'Radcodes LLC',  

    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Post/settings/install.php',
      'class' => 'Post_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.1.1'
      ),     
    ),
    'directories' => array(
      'application/modules/Post',
    ),
    'files' => array(
      'application/languages/en/post.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Post_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Post_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'post',
    'post_category',
    'post_vote',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'post_extended' => array(
      'route' => RADCODES_ROUTE_POST_PLURAL . '/:controller/:action/*',
      'defaults' => array(
        'module' => 'post',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'post_general' => array(
      'route' => RADCODES_ROUTE_POST_PLURAL . '/:action/*',
      'defaults' => array(
        'module' => 'post',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|hot|new|top|browse|create|manage|favorites|suggest-find-images|license)',
      )
    ),   
    'post_specific' => array(
      'route' => RADCODES_ROUTE_POST_PLURAL . '/item/:post_id/:action/*',
      'defaults' => array(
        'module' => 'post',
        'controller' => 'post',
      ),
      'reqs' => array(
        'action' => '(edit|delete|success|vote|permalink)',
        'post_id' => '\d+',
      )
    ),
    'post_profile' => array(
      'route' => RADCODES_ROUTE_POST_SINGLE . '/:post_id/:slug/*',
      'defaults' => array(
        'module' => 'post',
        'controller' => 'profile',
        'action' => 'index',
        'slug' => ''
      ),
      'reqs' => array(
        'post_id' => '\d+',
      )
    ),
       
  ),
);
