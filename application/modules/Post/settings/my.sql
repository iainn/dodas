
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


-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_posts`
--

DROP TABLE IF EXISTS `engine4_post_posts`;
CREATE TABLE `engine4_post_posts` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  
  `media` varchar(32) NOT NULL DEFAULT 'topic',
  
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,  
  `keywords` varchar(255) DEFAULT NULL,

  `url` varchar(255) DEFAULT NULL,
  `source` varchar(128) DEFAULT NULL,  
  `thumb` varchar(255) DEFAULT NULL,
  
  `status` varchar(32) NOT NULL DEFAULT 'approved',
  `status_date` datetime NOT NULL DEFAULT '0000-00-00',

  `approved_date` datetime DEFAULT NULL,
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  
  `hotness` decimal(21,9) NOT NULL default '0.000000000',
  
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  
  `vote_count` int(11) NOT NULL default '0',
  `point_count` int(11) NOT NULL default '0',
  `helpful_count` int(11) NOT NULL default '0',
  `nothelpful_count` int(11) NOT NULL default '0',
  `helpfulness` int(11) NOT NULL default '0',

  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  
  PRIMARY KEY (`post_id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  
  KEY `hotness` (`hotness`),
  KEY `point_count` (`point_count`),
  
  KEY `media` (`media`),
  KEY `source` (`source`),
  KEY `status` (`status`),
  KEY `featured` (`featured`),
  KEY `sponsored` (`sponsored`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_votes`
--

DROP TABLE IF EXISTS `engine4_post_votes`;
CREATE TABLE `engine4_post_votes` (
  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `helpful` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `post_user` (`post_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------


--
-- Table structure for table `engine4_post_categories`
--

DROP TABLE IF EXISTS `engine4_post_categories`;
CREATE TABLE `engine4_post_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_name` varchar(128) NOT NULL,
  `description` TEXT NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) NOT NULL DEFAULT '999',
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_post_categories`
--

INSERT IGNORE INTO `engine4_post_categories` (`category_id`, `order`, `user_id`, `category_name`, `description`) VALUES
(1, 1, 0, 'Ask It',''),
(2, 2, 0, 'Funny',''),
(3, 3, 0, 'News',''),
(4, 4, 0, 'Technology',''),
(5, 5, 0, 'Science',''),
(6, 6, 0, 'Gaming',''),
(7, 7, 0, 'WTF','')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_fields_maps`
--

DROP TABLE IF EXISTS `engine4_post_fields_maps`;
CREATE TABLE `engine4_post_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_fields_meta`
--

DROP TABLE IF EXISTS `engine4_post_fields_meta`;
CREATE TABLE `engine4_post_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `show` tinyint(1) unsigned NOT NULL default '1',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_fields_options`
--

DROP TABLE IF EXISTS `engine4_post_fields_options`;
CREATE TABLE `engine4_post_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_post_fields_options`
--

-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_fields_values`
--

DROP TABLE IF EXISTS `engine4_post_fields_values`;
CREATE TABLE `engine4_post_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_post_fields_search`
--

DROP TABLE IF EXISTS `engine4_post_fields_search`;
CREATE TABLE `engine4_post_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

DELETE FROM engine4_core_menus WHERE name LIKE 'post_%';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('post_main', 'standard', 'Post Main Navigation Menu'),
('post_admin_main', 'standard', 'Post Admin Main Navigation Menu'),
('post_create', 'standard', 'Post Create (List) Navigation Menu'),
('post_quick', 'standard', 'Post Create (Modal) Navigation Menu'),
('post_dashboard', 'standard', 'Post Dashboard Navigation Menu')
;

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'post';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_post', 'post', 'Posts', '', '{"route":"post_general"}', 'core_main', '', 4),
('core_sitemap_post', 'post', 'Posts', '', '{"route":"post_general"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_post', 'post', 'Posts', '', '{"route":"admin_default","module":"post","controller":"settings"}', 'core_admin_main_plugins', '', 2),

('post_main_hot', 'post', 'Hot', 'Post_Plugin_Menus', '{"route":"post_general","action":"hot"}', 'post_main', '', 1),
('post_main_new', 'post', 'New', 'Post_Plugin_Menus', '{"route":"post_general","action":"new"}', 'post_main', '', 2),
('post_main_top', 'post', 'Top', 'Post_Plugin_Menus', '{"route":"post_general","action":"top"}', 'post_main', '', 3),
('post_main_browse', 'post', 'Browse', 'Post_Plugin_Menus', '{"route":"post_general","action":"browse","id":"post-main-browse"}', 'post_main', '', 4),
('post_main_manage', 'post', 'My Posts', 'Post_Plugin_Menus', '{"route":"post_general","action":"manage"}', 'post_main', '', 5),
('post_main_favorites', 'post', 'My Favorites', 'Post_Plugin_Menus', '{"route":"post_general","action":"favorites"}', 'post_main', '', 6),
('post_main_create', 'post', 'Create New Post', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","class":"smoothbox"}', 'post_main', '', 7),

('post_create_topic', 'post', 'Post New Topic', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","params":{"media":"topic"},"class":"buttonlink icon_post_create_topic"}', 'post_create', '', 1),
('post_create_link', 'post', 'Post New Link', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","params":{"media":"link"},"class":"buttonlink icon_post_create_link"}', 'post_create', '', 2),
('post_create_photo', 'post', 'Post New Photo', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","params":{"media":"photo"},"class":"buttonlink icon_post_create_photo"}', 'post_create', '', 3),
('post_create_video', 'post', 'Post New Video', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","params":{"media":"video"},"class":"buttonlink icon_post_create_video"}', 'post_create', '', 4),

('post_quick_create', 'post', 'Create New Post', 'Post_Plugin_Menus', '{"route":"post_general","action":"create","class":"buttonlink icon_post_new smoothbox","id":"post-create-quick"}', 'post_quick', '', 1),

('post_dashboard_view', 'post', 'View Post', 'Post_Plugin_Menus', '{"route":"post_profile","action":"index","class":"buttonlink icon_post_view"}', 'post_dashboard', '', 1),
('post_dashboard_edit', 'post', 'Edit This Post', 'Post_Plugin_Menus', '{"route":"post_specific","action":"edit","class":"buttonlink icon_post_edit"}', 'post_dashboard', '', 2),
('post_dashboard_delete', 'post', 'Delete This Post', 'Post_Plugin_Menus', '{"route":"post_specific","action":"delete","class":"buttonlink icon_post_delete"}', 'post_dashboard', '', 7),

('post_admin_main_manage', 'post', 'View Posts', '', '{"route":"admin_default","module":"post","controller":"manage"}', 'post_admin_main', '', 1),
('post_admin_main_settings', 'post', 'Global Settings', '', '{"route":"admin_default","module":"post","controller":"settings"}', 'post_admin_main', '', 2),
('post_admin_main_level', 'post', 'Member Level Settings', '', '{"route":"admin_default","module":"post","controller":"level"}', 'post_admin_main', '', 3),
('post_admin_main_fields', 'post', 'Post Questions', '', '{"route":"admin_default","module":"post","controller":"fields"}', 'post_admin_main', '', 4),
('post_admin_main_categories', 'post', 'Categories', '', '{"route":"admin_default","module":"post","controller":"categories"}', 'post_admin_main', '', 5),
('post_admin_main_faq', 'post', 'FAQ', '', '{"route":"admin_default","module":"post","controller":"faq"}', 'post_admin_main', '', 9)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

DELETE FROM `engine4_core_modules` WHERE name = 'post';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('post', 'Post / Voting & Sharing / Buzz Plugin', 'This plugin let you run a social news and entertainment website where members submit content in the form of either a link or a text post.', '4.0.1', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
DELETE FROM `engine4_core_settings` WHERE name LIKE 'post.%';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('post.license','XXXX-XXXX-XXXX-XXXX'),
('post.timefactor','30'),
('post.postperpage','20');


-- --------------------------------------------------------
DELETE FROM `engine4_activity_actiontypes` WHERE module = 'post';

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('post_new_topic', 'post', '{item:$subject} posted a new topic:', 1, 5, 1, 3, 1, 1),
('post_new_photo', 'post', '{item:$subject} posted a new photo:', 1, 5, 1, 3, 1, 1),
('post_new_video', 'post', '{item:$subject} posted a new video:', 1, 5, 1, 3, 1, 1),
('post_new_link', 'post', '{item:$subject} posted a new link:', 1, 5, 1, 3, 1, 1),
('comment_post', 'post', '{item:$subject} commented on {item:$owner}''s {item:$object:post}: {body:$body}', 1, 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
DELETE FROM `engine4_activity_notificationtypes` WHERE module = 'post';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('post_status_update', 'post', 'Your {item:$object} post status has been updated to {var:$status}.', 0, '')
;

--
-- Dumping data for table `engine4_core_mailtemplates`
--
DELETE FROM `engine4_core_mailtemplates` WHERE module = 'post';

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_post_status_update', 'post', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'post';


-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'auth_photo' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'max_posts' as `name`,
    3 as `value`,
    9999 as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');  
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'approval' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');    
  
-- create, style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');    
  
-- ADMIN, MODERATOR
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

  
-- USER
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'post' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


