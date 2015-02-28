<?php

//========================================================================
// MemHT Portal
// 
// Copyright (C) 2008-2013 by Miltenovikj Manojlo <dev@miltenovik.com>
// http://www.memht.com
// 
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your opinion) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License along
// with this program; if not, see <http://www.gnu.org/licenses/> (GPLv2)
// or write to the Free Software Foundation, Inc., 51 Franklin Street,
// Fifth Floor, Boston, MA02110-1301, USA.
//========================================================================

/**
 * @author      Miltenovikj Manojlo <dev@miltenovik.com>
 * @copyright	Copyright (C) 2008-2013 Miltenovikj Manojlo. All rights reserved.
 * @license     GNU/GPLv2 http://www.gnu.org/licenses/
 */

//Deny direct access
defined("_LOAD") or die("Access denied");

class Setup {
	static function Install() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');
		
		//#__forums
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__forums` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `parent` int(10) NOT NULL DEFAULT '0',
					  `category` int(10) NOT NULL,
					  `title` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  `description` text NOT NULL,
					  `roles_read` text NOT NULL,
					  `roles_write` text NOT NULL,
					  `roles_moderate` text NOT NULL,
					  `position` int(4) NOT NULL,
					  `status` enum('active','inactive','locked') NOT NULL DEFAULT 'locked',
					  PRIMARY KEY (`id`),
					  KEY `category` (`category`),
					  KEY `parent` (`parent`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		//#__forums_categories
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__forums_categories` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  `description` text NOT NULL,
					  `position` int(4) NOT NULL,
					  `status` enum('active','inactive','locked') NOT NULL DEFAULT 'locked',
					  PRIMARY KEY (`id`),
					  KEY `name` (`name`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
                    
		//#__forums_posts
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__forums_posts` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `parent` int(10) NOT NULL DEFAULT '0',
					  `lastchild` int(10) NOT NULL,
					  `forum` int(10) NOT NULL,
					  `title` varchar(255) NOT NULL,
					  `author` int(10) NOT NULL,
					  `text` text NOT NULL,
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `modauthor` int(10) NOT NULL,
					  `hits` int(11) NOT NULL DEFAULT '0',
					  `flag` tinyint(1) NOT NULL DEFAULT '0',
					  `status` enum('active','inactive','locked') NOT NULL DEFAULT 'locked',
					  PRIMARY KEY (`id`),
					  KEY `forum` (`forum`),
					  KEY `created` (`created`),
					  KEY `status` (`status`),
					  KEY `parent` (`parent`),
					  KEY `fpc` (`forum`,`parent`,`created`),
					  KEY `cfs` (`created`,`flag`,`status`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		//#__forums_userdata
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__forums_userdata` (
					  `uid` int(10) NOT NULL,
					  `posts` int(10) NOT NULL,
					  `lastpost` int(10) NOT NULL,
					  `lastpostread` int(10) NOT NULL,
					  UNIQUE KEY `uid` (`uid`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		//ACP Menu link
		$Db->Query("INSERT INTO `#__menu_acp` (`title`, `uniqueid`, `url`, `icon`, `menu`, `submenu`, `quickicons`, `status`)
					VALUES ('Forums', 'forums_main', 'admin.php?cont=forums', 'cloud.png', 'content', 1, 1, 'active');");
		
		return _t("INSTALLED");
	}

	static function Uninstall() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');        
        // forums
        $Db->Query("DROP TABLE #__forums");
        // forums categories
        $Db->Query("DROP TABLE #__forums_categories");
        // forums posts
        $Db->Query("DROP TABLE #__forums_posts");
        // forums userdata
        $Db->Query("DELETE FROM #__forums_userdata");

		return _t("UNINSTALLED");
	}
}
	
?>