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
 
 
 
class Post_Installer extends Engine_Package_Installer_Module
{


  public function addUserProfileTab()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // post.profile-posts
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'post.member-profile-posts')
      ;
    $info = $select->query()->fetch();
    if( empty($info) ) {
    
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'post.member-profile-posts',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Posts","titleCount":true,"max":10,"order":"recent"}',
      ));

    }
  }
  // addUserProfileTab
  
  public function addHomePage()
  {
    // post Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'post_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'post_index_index',
        'displayname' => 'Post - Home Page',
        'title' => 'Post Home Page',
        'description' => 'This is the home page for posts.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
      // ------ MAIN :: LEFT WIDGETS  

      
      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-post',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.categories',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"","linkto":"hot"}',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.sponsored-posts',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Sponsored Posts"}',
      ));
      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.top-submitters',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Top Posters","max":5}',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.top-voters',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Top Voters","max":5}',
      ));        
     
      
      // ------ MAIN :: MIDDLE WIDGETS   
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.featured-posts',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Featured Posts"}',
      ));

      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.list-posts',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Trending Posts","max":15,"order":"hotness","display_style":"wide"}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.list-posts',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Upcoming Posts","max":15,"order":"recent","display_style":"wide"}',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.list-posts',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Top of The Month","max":15,"order":"mostvote","display_style":"wide","period":"month"}',
      ));       
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.popular-tags',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags"}',
      )); 
            
    }
  } 
  // addHomePage
  
  public function addManagePage()
  {
    $page_name = 'post_index_manage';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Manage Page',
        'title' => 'Manage Posts',
        'description' => 'This is the manage page for my posts.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
    
  
    
      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS   
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addManagePage
  
  public function addBrowsePage()
  {
    $page_name = 'post_index_browse';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Browse Posts Page',
        'title' => 'Browse Posts',
        'description' => 'This is the browse page for posts.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
    
  
    
      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS   
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addBrowsePage
  
  
  public function addHotPage()
  {
    $page_name = 'post_index_hot';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Hot Posts Page',
        'title' => 'Browse Hot Posts',
        'description' => 'This is the browse hot posts page.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');

      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS       
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addHotPage  
  
  
  public function addNewPage()
  {
    $page_name = 'post_index_new';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - New Posts Page',
        'title' => 'Browse New Posts',
        'description' => 'This is the browse new posts page.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');

      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS       
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addNewPage  
  
  
  public function addTopPage()
  {
    $page_name = 'post_index_top';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Top Posts Page',
        'title' => 'Browse Top Posts',
        'description' => 'This is the browse top posts page.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');

      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS       
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addTopPage 
    
  
  public function addFavoritesPage()
  {
    $page_name = 'post_index_favorites';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - My Favorites',
        'title' => 'Browse Favorited Posts',
        'description' => 'This is the my favorites page for posts.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
    
  
    
      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

    
      // ------ MAIN :: MIDDLE WIDGETS      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
    }
  }
  // addFavoritesPage  
  
  

  public function addProfilePage()
  {
    $page_name = 'post_profile_index';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Profile Page',
        'title' => 'Post Profile Page',
        'description' => 'This is a post profile page.',
        'custom' => 0,
      );
      $db     = $this->getDb();
      
      $db->insert('engine4_core_pages', $page_data);
      $page_id = $db->lastInsertId('engine4_core_pages');
  
      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');
  
      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');

    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
    
  
    
      // ------ MAIN :: RIGHT WIDGETS

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-submitter',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-social-shares',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '',
      ));      
      
     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-tools',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '',
      ));           
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-related-posts',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Related Posts","max":10}',
      ));
            
      // ------ MAIN :: MIDDLE WIDGETS   

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-notice',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-preview',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-title',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-description',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-details',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Details"}',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-keywords',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-meta',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.profile-options',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));           
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.comments',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '',
      ));      
      
    }
  }
  // addProfilePage
    

  
  public function addCreatePage()
  {
    $page_name = 'post_index_create';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Create New Page',
        'title' => 'Create New Post',
        'description' => 'This is the creae new post page.',
        'custom' => 0,
      );

      $content_data = $this->createStandardContentPage($page_data, array('main_right_content'=>false));
      @extract($content_data);
      
      $db     = $this->getDb();
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
  
    }    
  }
  // addCreatePage
  
  public function addEditPage()
  {
    $page_name = 'post_post_edit';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Post - Edit Page',
        'title' => 'Manage/Edit Post',
        'description' => 'This is the manage/edit page for post.',
        'custom' => 0,
      );

      $content_data = $this->createStandardContentPage($page_data, array('main_right_content'=>false));
      @extract($content_data);
      
      $db     = $this->getDb();
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.edit-info',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.edit-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));  
    }
  }
  // addEditPage

  

    
  
  public function createStandardContentPage($page_data, $options = array())
  {
    $db     = $this->getDb();
    
    $db->insert('engine4_core_pages', $page_data);
    $page_id = $db->lastInsertId('engine4_core_pages');

    $i = 1;
    
    // CONTAINERS (TOP / MAIN)
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => $i++,
      'params' => '',
    ));
    $top_id = $db->lastInsertId('engine4_core_content');
    
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => $i++,
      'params' => '',
    ));
    $main_id = $db->lastInsertId('engine4_core_content');

    // ---------- CONTAINER TOP & WIDGET MENU -----------
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'order' => $i++,
      'params' => '',
    ));
    $top_middle_id = $db->lastInsertId('engine4_core_content');
    
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'post.main-menu',
      'parent_content_id' => $top_middle_id,
      'order' => $i++,
      'params' => '{"title":""}',
    ));
    $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      
    if (!isset($options['right_column']) || $options['right_column'] !== false)
    {
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
    }
    
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $main_id,
      'order' => $i++,
      'params' => '',
    ));
    $main_middle_id = $db->lastInsertId('engine4_core_content');
    
  
    
    // ------ MAIN :: RIGHT WIDGETS
    if ($main_right_id && (!isset($options['main_right_content']) || $options['main_right_content'] !== false))
    {
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.packages-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'post.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    }
    
    // ------ MAIN :: MIDDLE WIDGETS   
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $i++,
      'params' => '',
    ));
    
    $content_data = array(
      'page_id' => $page_id,
      'top_id' => $top_id,
      'top_middle_id' => $top_middle_id,  
      'main_id' => $main_id,
      'main_right_id' => $main_right_id,  
      'main_middle_id' => $main_middle_id,  
      'order' => $i,
      'i' => $i,
    );
    
    return $content_data;    
  }
  // createStandardContentPage
  
  
  public function createSimpleContentPage($page_data, $options = array())
  {
    $db     = $this->getDb();
    
    $db->insert('engine4_core_pages', $page_data);
    $page_id = $db->lastInsertId('engine4_core_pages');

    $i = 1;
    
    // CONTAINERS (MAIN)
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => $i++,
      'params' => '',
    ));
    $main_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $main_id,
      'order' => $i++,
      'params' => '',
    ));
    $main_middle_id = $db->lastInsertId('engine4_core_content');

    
    // ------ MAIN :: MIDDLE WIDGETS   
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $i++,
      'params' => '',
    ));
    $db->lastInsertId('engine4_core_content');
    
    $content_data = array(
      'page_id' => $page_id,
      'main_id' => $main_id,
      'main_middle_id' => $main_middle_id,  
      'order' => $i,
      'i' => $i,  
    );
    
    return $content_data;
  }
  // createSimpleContentPage
  
  private function getPageByName($page_name)
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', $page_name)
      ->limit(1);
      ;
    $info = $select->query()->fetch();
    
    return $info;
  }
  // getPageByName
  
  public function onInstall()
  {
    $this->addBrowsePage();
    $this->addCreatePage();
    $this->addEditPage();
    $this->addFavoritesPage();
    $this->addHomePage();
    $this->addHotPage();
    $this->addManagePage(); 
    $this->addNewPage();
    $this->addProfilePage();
    $this->addTopPage();
    $this->addUserProfileTab();

    parent::onInstall();
  }
}
