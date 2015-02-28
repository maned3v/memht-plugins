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
 * @copyright	Copyright (C) 2008-2012 Miltenovikj Manojlo. All rights reserved.
 * @license     GNU/GPLv2 http://www.gnu.org/licenses/
 */

//Deny direct access
defined("_LOAD") or die("Access denied");

//Mod-rewrite querystring rebuild map

//index.php?node=plugin&sec=SECTION&cat=CATEGORY&title=FILENAME
$map['index'] = array("sec","cat","title");

//index.php?node=plugin&op=browse&sec=SECTION&cat=CATEGORY&page=PAGE
$map['browse'] = array("op","sec","cat","page");

//index.php?node=plugin&op=get&id=FILEID
$map['get'] = array("op","id");

//index.php?node=plugin&op=related&tag=TAG&sec=SECTION&cat=CATEGORY
$map['related'] = array("op","tag","sec","cat");

//index.php?node=plugin&op=rate&id=FILEID&vote=VOTE&rand=RAND
$map['rate'] = array("op","id","vote","rand");

//index.php?node=plugin&op=rss&sec=SECTION&cat=CATEGORY
$map['rss'] = array("op","sec","cat");

?>