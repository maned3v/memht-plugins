<?php

//========================================================================
// MemHT Portal
// 
// Copyright (C) 2008-2012 by Miltenovikj Manojlo <dev@miltenovik.com>
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
 * @author		Paulo Ferreira <sisnox@gmail.com>
 * @copyright	Copyright (C) 2008-2012 Miltenovikj Manojlo. All rights reserved.
 * @license     GNU/GPLv2 http://www.gnu.org/licenses/
 */

//Deny direct access
defined("_LOAD") or die("Access denied");

class Setup {
	static function Install() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');
		
		//#__links
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__links` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `category` int(10) NOT NULL,
					  `title` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  `url` varchar(255) NOT NULL,
					  `description` longtext NOT NULL,
					  `image` varchar(255) NOT NULL,
					  `status` varchar(30) NOT NULL DEFAULT 'off',
					  `roles` text NOT NULL,
					  `hits` int(10) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `name` (`name`),
					  KEY `status` (`status`),
					  KEY `category` (`category`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
		
		//#__links_categories
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__links_categories` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `name` (`name`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
		
		//ACP Menu link
		$Db->Query("INSERT INTO `#__menu_acp` (`title`, `uniqueid`, `url`, `icon`, `menu`, `submenu`, `quickicons`, `status`)
					VALUES ('Links', 'links_main', 'admin.php?cont=links', 'link.png', 'content', '1', '0', 'active');");
		
		return _t("INSTALLED");
	}

	static function Uninstall() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');
        // links
        $Db->Query("DROP TABLE #__links");
        // links categories
        $Db->Query("DROP TABLE #__links_categories");		
		//ACP Menu link
		$Db->Query("DELETE FROM `#__menu_acp` WHERE `uniqueid`='links_main'");
		
		return _t("UNINSTALLED");
	}
}
	
?>