/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.seaocores.com/license/
 * @version    $Id: my.sql 2010-11-18 9:40:21Z Seaocores $
 * @author     Seaocores
 */

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_plugins_Seaocore', 'seaocore', 'SocialEngineAddOns', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"upgrade"}', 'core_admin_main_plugins', '', 999),
('seaocore_admin_upgrade', 'seaocore', 'Plugin Upgrades', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"upgrade"}', 'seaocore_admin_main', '', 0),
('seaocore_admin_news', 'seaocore', 'News', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"news"}', 'seaocore_admin_main', '', 2),
('seaocore_admin_info', 'seaocore', 'Plugins Information', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"information"}',
'seaocore_admin_main', '', 1),
('seaocore_admin_main_infotooltip', 'seaocore', 'Info Tooltip Settings', '',
'{"route":"admin_default","module":"seaocore","controller":"infotooltip"}', 'seaocore_admin_main', '', 3);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('seaocore.display.lightbox',1),
('seaocore.lightbox.option.display',''),
('seaocore.tag.type', 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('seaocore_admin_main_lightbox', 'seaocore', 'Photos Lightbox Viewer', '', '{"route":"admin_default","module":"seaocore","controller":"settings","action":"lightbox"}', 'seaocore_admin_main', '', 1, 0, 4),
( 'seaocore_admin_helpInvite', 'seaocore', 'Invite Services', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings","action":"help-invite"}', 'seaocore_admin_main', NULL, '1', '0', '6'),
( 'seaocore_admin_map', 'seaocore', 'Maps', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings","action":"map"}', 'seaocore_admin_main', NULL, '1', '0', '7');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.tooltip.bgcolor', '#FFFFFF');

-- --------------------------------------------------------

UPDATE  `engine4_core_menuitems` SET  `label` =  'SocialEngineAddOns-Old Version' WHERE  `engine4_core_menuitems`.`name` =  'core_admin_plugins_Socialengineaddon';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_seaocore_locationitems`
--

DROP TABLE IF EXISTS `engine4_seaocore_locationitems`;
CREATE TABLE IF NOT EXISTS `engine4_seaocore_locationitems` (
  `locationitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `location` text COLLATE utf8_unicode_ci,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `formatted_address` text COLLATE utf8_unicode_ci,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zoom` int(11) NOT NULL,
  PRIMARY KEY (`locationitem_id`),
  UNIQUE KEY `resource_id` (`resource_id`,`resource_type`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------


/* This query was removed for changes in 4.2.8 */

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
VALUES ('share', 'activity', '{item:$subject} shared {item:$object}''s {var:$type}.\r\n{body:$body}', 1, 5, 1, 1, 0, 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)
VALUES ('shared', 'activity', '{item:$subject} has shared your {item:$object:$label}.', 0, '', 1);

-- --------------------------------------------------------
