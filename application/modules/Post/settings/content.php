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



return array(

  // ------- categories
  
  array(
    'title' => 'Post: Categories',
    'description' => 'Displays a list of post categories (support narrow / wide mode).',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.categories',
    'defaultParams' => array(
      'title' => 'Categories',
      'display_style' => 'narrow',
      'showphoto' => 1,
      'descriptionlength' => 255,
    ),   
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Categories')),
        Post_Form_Helper::getContentField('display_style', array('value' => 'narrow')),
        Post_Form_Helper::getContentField('showphoto'),
        Post_Form_Helper::getContentField('descriptionlength', array('value' => 255)),
        Post_Form_Helper::getContentField('categorylinkto'),
      ),
    ),    
  ), 
  
  // ------- create menu 
  array(
    'title' => 'Post: Create Menu Links',
    'description' => 'Displays menu navigation links to create new post (topic, link, photo, video)',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.create-menu',
  ),   
  
  // ------- create new post
  array(
    'title' => 'Post: Create New Post',
    'description' => 'Displays a quick navigation link to create new post (modal popup) and optional mouse over sub-menu (beta)',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.create-new',
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('submenu'),
      ),
    ),
  ),   
  
  // ------- edit info
  array(
    'title' => 'Post: Post Edit - Info',
    'description' => 'Displays post info block on editing pages',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.edit-info',
  ), 

  // ------- edit menu
  array(
    'title' => 'Post: Post Edit - Menu',
    'description' => 'Displays post edit menu navigation links on editing pages',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.edit-menu',
  ), 
  
  
  // ------- featured posts
  
  array(
    'title' => 'Post: Featured Posts',
    'description' => 'Displays slideshow of featured posts with different filtering options (wide mode)',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.featured-posts',
    'defaultParams' => array(
      'title' => 'Featured Posts',
      'max' => 5,
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'post_widget_form'
      ),    
      'elements' => array(
      
        Post_Form_Helper::getContentField('title', array('value' => 'Featured Posts')),
        Post_Form_Helper::getContentField('max', array('value' => 5)),
        Post_Form_Helper::getContentField('order', array('value' => 'random')),
        Post_Form_Helper::getContentField('period'),
        Post_Form_Helper::getContentField('media'),
        Post_Form_Helper::getContentField('category'),
        Post_Form_Helper::getContentField('user'),
        Post_Form_Helper::getContentField('keyword'),
                   
      ),
    ),    
  ),   
  

  
  // ------- list posts
  
  array(
    'title' => 'Post: List Posts',
    'description' => 'Displays a list of posted posts with different filtering options (can be used to build variety of post listings such as Recent Posts, Most Commented by XYZ user with specified keyword etc..)',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.list-posts',
    'defaultParams' => array(
      'title' => 'Listing Posts',
      'max' => 10,
      'order' => 'recent',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'post_widget_form'
      ),
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'List Posts')),
        Post_Form_Helper::getContentField('max', array('value' => 10)),
        Post_Form_Helper::getContentField('order', array('value' => 'recent')),
        Post_Form_Helper::getContentField('period'),
        Post_Form_Helper::getContentField('media'),
        Post_Form_Helper::getContentField('category'),
        Post_Form_Helper::getContentField('user'),
        Post_Form_Helper::getContentField('keyword'),

        Post_Form_Helper::getContentField('featured'),
        Post_Form_Helper::getContentField('sponsored'),
        
        Post_Form_Helper::getContentField('display_style'),
        
        Post_Form_Helper::getContentField('showemptyresult'),
      ),
    ),    
  ),  
    
  
  
  // ------- top menu nav
  array(
    'title' => 'Post: Menu Top Navigation',
    'description' => 'Displays a post main menu navigation (Hot, New, Top, Browse, Manage etc..).',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.main-menu',
  ),  
  

  // ------- on user profile tab

  array(
    'title' => 'Member Profile Posts',
    'description' => 'Displays a member\'s posts on their profile. It also supports displaying posts that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows posts created by the group owner. If you set User=VIEWER mode, then the widget will displays posts created by current logged in member.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.member-profile-posts',
    'defaultParams' => array(
      'title' => 'Posts',
      'titleCount' => true,
      'max' => 10,
      'user_type' => 'owner',
      'order' => 'recent',
    ),
    'adminForm' => array(
      'description' => 'Displays a member\'s posts on their profile. It also supports displaying posts that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows posts created by the group owner. If you set User=VIEWER mode, then the widget will displays posts created by current logged in member.',
      'attribs' => array(
        'class' => 'post_widget_form'
      ),
      'elements' => array(
      
        Post_Form_Helper::getContentField('title', array('value' => 'Posts')),
        Post_Form_Helper::getContentField('max', array('value' => 5)),
        Post_Form_Helper::getContentField('user_type', array('value' => 'owner')),
        Post_Form_Helper::getContentField('order'),
        Post_Form_Helper::getContentField('period'),
        Post_Form_Helper::getContentField('media'),
        Post_Form_Helper::getContentField('category'),
        Post_Form_Helper::getContentField('user'),
        Post_Form_Helper::getContentField('keyword'),

        Post_Form_Helper::getContentField('featured'),
        Post_Form_Helper::getContentField('sponsored'),
        
        Post_Form_Helper::getContentField('display_style'),

        Post_Form_Helper::getContentField('showmemberitemlist'),        
               
      ),
    ),     
  ),  


  // ------- popular tags
  
  array(
    'title' => 'Post: Popular Tags',
    'description' => 'Displays post popular tags.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.popular-tags',
    'defaultParams' => array(
      'title' => 'Popular Tags',
      'max' => 100,
      'order' => 'text',
    ),
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Popular Tags')),
        Post_Form_Helper::getContentField('max', array('label' => 'Max Tags', 'value' => 100)),
        Post_Form_Helper::getContentField('order', array('value' => 'text', 'multiOptions' => array('text' => 'Tag Name','total' => 'Total Count'))),              
      ),
    ),     
  ),
  

  
  // ========================= POST PROFILE WIDGETS (post view page) ===========================
  
  
  // ------- post profile description
  array(
    'title' => 'Post Profile - Description',
    'description' => 'Displays a post\'s full description content body.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-description',    
    'defaultParams' => array(
      'title' => 'Post Description',
    ),  
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Post Description')),
      ),
    ),    
  ), 
  
  // ------- post profile details
  array(
    'title' => 'Post Profile - Details',
    'description' => 'Displays a post\'s details (customized question/field data) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-details',    
    'defaultParams' => array(
      'title' => 'Post Details',
    ),  
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Post Details')),
      ),
    ),    
  ),
  
  // ------- post profile keywords
  array(
    'title' => 'Post Profile - Keywords',
    'description' => 'Displays a post\'s keywords (tags) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-keywords',       
  ),
  
  // ------- post profile meta
  array(
    'title' => 'Post Profile - Meta',
    'description' => 'Displays a post\'s meta data (date, owner, category, stats etc.) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-meta',       
  ),
  
  // ------- post profile notice
  array(
    'title' => 'Post Profile - Notice',
    'description' => 'Displays a post\'s system notice such as approval status, expiration, preview message etc..',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-notice', 
  ),
  
  // ------- post profile options
  array(
    'title' => 'Post Profile - Options',
    'description' => 'Displays a post\'s options (Edit | Delete) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-options',        
  ),
  
  // ------- post profile preview
  array(
    'title' => 'Post Profile - Preview',
    'description' => 'Displays a post\'s preview photo or video (if supported) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-preview',        
  ),
  
  // ------- post profile related posts
  array(
    'title' => 'Post: Profile - Related Posts',
    'description' => 'Displays a post\'s related posts (by tags) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-related-posts',    
    'defaultParams' => array(
      'title' => 'Related Posts',
      'max' => 20,
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'post_widget_form'
      ),
      'elements' => array(
      
        Post_Form_Helper::getContentField('title', array('value' => 'Related Posts')),
        Post_Form_Helper::getContentField('max', array('value' => 20)),    

      ),
    ),    
  ), 
  
  // ------- post profile social shares
  array(
    'title' => 'Post Profile - Social Shares',
    'description' => 'Displays a post\'s social shares such as Facebook, Twitter, Digg using AddThis service on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-social-shares',       
  ),  
  
  
  // ------- post profile submitter
  array(
    'title' => 'Post Profile - Submitter',
    'description' => 'Displays a post\'s submitter on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-submitter',        
  ),
  
  // ------- post profile title
  array(
    'title' => 'Post Profile - Title',
    'description' => 'Displays a post\'s title on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-title',       
  ),
  
  // ------- post profile tools
  array(
    'title' => 'Post Profile - Tools',
    'description' => 'Displays a post\'s tools (Share | Report) on its profile.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.profile-tools',       
  ),  
  
  // ========================= END POST PROFILE WIDGETS (post view page) ===========================
  

  // ------- search form
  
  array(
    'title' => 'Post: Search Form',
    'description' => 'Displays search posts form.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.search-form',
  ), 
  
  
  // ------- search post
  
  array(
    'title' => 'Post: Search Quick',
    'description' => 'Displays search keyword simple quick form.',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.search-post',
  ), 
  

  // ------- sponsored posts
  
  array(
    'title' => 'Post: Sponsored Posts',
    'description' => 'Displays ticker-news of sponsored posts with different filtering options (side bar)',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.sponsored-posts',
    'defaultParams' => array(
      'title' => 'Sponsored Posts',
      'max' => 5,
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'post_widget_form'
      ),    
      'elements' => array(
      
        Post_Form_Helper::getContentField('title', array('value' => 'Sponsored Posts')),
        Post_Form_Helper::getContentField('max', array('value' => 5)),
        Post_Form_Helper::getContentField('order', array('value' => 'random')),
        Post_Form_Helper::getContentField('period'),
        Post_Form_Helper::getContentField('media'),
        Post_Form_Helper::getContentField('category'),
        Post_Form_Helper::getContentField('user'),
        Post_Form_Helper::getContentField('keyword'),
                 
      ),
    ),    
  ),    


  // ------- top submitters
  
  array(
    'title' => 'Post: Top Submitters',
    'description' => 'Displays list of top post\'s submitters',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.top-submitters',
    'defaultParams' => array(
      'title' => 'Top Submitters',
      'max' => 5,
    ),  
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Top Submitters')),
        Post_Form_Helper::getContentField('max', array('label' => 'Max Items')),
        Post_Form_Helper::getContentField('period'),
      ),
    ),    
  ),   
 

  // ------- top voters
  
  array(
    'title' => 'Post: Top Voters',
    'description' => 'Displays list of top post\'s voters',
    'category' => 'Posts',
    'type' => 'widget',
    'name' => 'post.top-voters',
    'defaultParams' => array(
      'title' => 'Top Voters',
      'max' => 5,
    ),  
    'adminForm' => array(
      'elements' => array(
        Post_Form_Helper::getContentField('title', array('value' => 'Top Voters')),
        Post_Form_Helper::getContentField('max', array('label' => 'Max Items')),
      ),
    ),    
  ),
);

