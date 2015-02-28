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

class SearchPlugin extends searchModel {
	public function InPlugin($query) {
		global $Db;

		//Controller-name match
		$plugmatch = Ram::Get("plugmatch");
		$plugname = isset($plugmatch['forums']) ? $plugmatch['forums'] : "forums" ;

		$results = Io::GetVar('POST','results','int');
		$author = Io::GetVar('POST','author');
		$language = Io::GetVar('POST','language');
		$start = Io::GetVar('POST','start',false,true,'2000-01-01 00:00:00');
		$end = Io::GetVar('POST','end',false,true,'2199-02-01 00:00:00');
		if ($results<=0 || $results>100) $results = 20;

		//Filter
		$where = array();
		//...by query
		$query = $Db->_e($query);
		$where[] = "(p.title LIKE '%".$query."%' OR p.text LIKE '%".$query."%')";
		//...by author
		if (strlen($author)>=4) $where[] = "u.name LIKE '%".$Db->_e($author)."%'";

		$where[] = "p.status='active'";
		//Build query
		$where = " WHERE ".implode(" AND ",$where) ;

		$searchres = false;
		if ($result = $Db->GetList("SELECT f.name AS forum_name,f.title AS forum_title,u.name AS author_name,p.*
									FROM #__forums_posts AS p FORCE INDEX(cfs) JOIN #__forums AS f JOIN #__user AS u
									ON p.forum=f.id AND p.author=u.uid
									{$where}
									LIMIT ".intval($results))) {
			foreach ($result as $row) {
				$post       = Io::Output($row['id'],"int");
				$fname		= Io::Output($row['forum_name']);
				$ftitle     = Io::Output($row['forum_title']);
				$title 		= Io::Output($row['title']);
				$aid		= Io::Output($row['author'],"int");
				$author		= Io::Output($row['author_name']);
				$created	= Time::Output(Io::Output($row['created']),"d");
				$parent     = Io::Output($row['parent'],"int");

				$t = ($parent>0) ? $parent : $post ;

				$searchres[] = array('title'	=>$title,
									 'url'		=>"<a href='index.php?"._NODE."=$plugname&amp;f=$fname&amp;t=$t' title='".CleanTitleAtr($title)."'>$title</a>",
									 'subtitle'	=>_t("WRITTEN_IN_X_BY_Y_ON_Z",
												  $ftitle,
												  "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
												  "$created"));
			}
		}
		return $searchres;
	}
}

?>