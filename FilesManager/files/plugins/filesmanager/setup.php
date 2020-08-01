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
		
		//#__filemgr
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__filemgr` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `category` int(10) NOT NULL,
					  `title` varchar(255) NOT NULL,
					  `file_name` varchar(14) NOT NULL,
					  `file_ext` varchar(4) NOT NULL,
					  `size` int(10) NOT NULL,
					  `author` int(10) NOT NULL,
					  `uploaded` datetime NOT NULL,
					  `ip` varbinary(32) NOT NULL,
					  `roles` text NOT NULL,
					  `hits` int(10) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `category` (`category`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		
		//#__filemgr_categories
		$Db->Query("CREATE TABLE IF NOT EXISTS `#__filemgr_categories` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		
		//ACP Menu link
		$Db->Query("INSERT INTO `#__menu_acp` (`title`, `uniqueid`, `url`, `icon`, `menu`, `submenu`, `quickicons`, `status`)
					VALUES ('Files manager', 'filesmgr_main', 'admin.php?cont=filesmanager', 'disk.png', 'content', '1', '0', 'active');");
		
		return _t("INSTALLED");
	}

	static function Uninstall() {
		global $Db,$User;
		
		if (!$User->IsAdmin()) die('Access denied!');
        //#__filemgr
        $Db->Query("DROP TABLE #__filemgr");
        //#__filemgr_categories
        $Db->Query("DROP TABLE #__filemgr_categories");
		//ACP Menu link
		$Db->Query("DELETE FROM `#__menu_acp` WHERE `uniqueid`='filesmgr_main'");
		
		return _t("UNINSTALLED");
	}
}
	
?>