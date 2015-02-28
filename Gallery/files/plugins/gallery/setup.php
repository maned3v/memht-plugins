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
		
		//#__gallery
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__gallery` (
                      `id` int(10) NOT NULL AUTO_INCREMENT,
                      `category` int(10) NOT NULL,
                      `title` varchar(255) NOT NULL,
                      `name` varchar(255) NOT NULL,
                      `author` int(10) NOT NULL,
                      `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `file` varchar(255) NOT NULL,
                      `thumb` varchar(255) NOT NULL,
                      `description` text NOT NULL,
                      `start` datetime NOT NULL DEFAULT '2001-01-01 00:00:00',
                      `end` datetime NOT NULL DEFAULT '2199-01-01 00:00:00',
                      `options` longtext NOT NULL,
                      `usecomments` tinyint(1) NOT NULL,
                      `comments` int(10) NOT NULL,
                      `hits` int(10) NOT NULL,
                      `roles` text NOT NULL,
                      `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
                      PRIMARY KEY (`id`),
                      KEY `category` (`category`),
                      KEY `status` (`status`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		//#__gallery_categories
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__gallery_categories` (
                      `id` int(10) NOT NULL AUTO_INCREMENT,
                      `section` int(10) NOT NULL,
                      `parent` int(10) NOT NULL DEFAULT '0',
                      `title` varchar(255) NOT NULL,
                      `name` varchar(255) NOT NULL,
                      `file` varchar(255) NOT NULL,
                      `description` text NOT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `name` (`name`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
                    
		//#__gallery_sections
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__gallery_sections` (
                      `id` int(10) NOT NULL AUTO_INCREMENT,
                      `title` varchar(255) NOT NULL,
                      `name` varchar(255) NOT NULL,
                      `file` varchar(255) NOT NULL,
                      `description` text NOT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `name` (`name`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");                                         
		
		//ACP Menu link
		$Db->Query("INSERT INTO `#__menu_acp` (`title`, `uniqueid`, `url`, `icon`, `menu`, `submenu`, `quickicons`, `status`)
					VALUES ('Gallery', 'gallery_main', 'admin.php?cont=gallery', 'picture.png', 'content', 1, 0, 'active');");
		
		return _t("INSTALLED");
	}

	static function Uninstall() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');        
        // gallery
        $Db->Query("DROP TABLE #__gallery");
        // gallery categories
        $Db->Query("DROP TABLE #__gallery_categories");
        // gallery sections
        $Db->Query("DROP TABLE #__gallery_sections");
        // gallery comments
        $Db->Query("DELETE FROM `#__comments` WHERE `controller`='gallery'");
        // gallery rate
		$Db->Query("DELETE FROM `#__ratings` WHERE `controller`='gallery'");
		//ACP Menu link
		$Db->Query("DELETE FROM `#__menu_acp` WHERE `uniqueid`='gallery_main'");
                
		return _t("UNINSTALLED");
	}
}
	
?>