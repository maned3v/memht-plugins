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

$result = $Db->GetList("SELECT f.title AS ftitle,f.name AS fname,c.name AS cname,s.name AS sname,f.created
					   	FROM #__downloads AS f FORCE INDEX(cs) JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s ON f.category=c.id AND s.id=c.section
						WHERE f.status='active' ORDER BY f.created DESC LIMIT 0,20");

//Controller-name match
$plugmatch = Ram::Get("plugmatch");
$plugname = isset($plugmatch['downloads']) ? $plugmatch['downloads'] : "downloads" ;

if (defined("_XML")) {
	foreach ($result as $row) {
		$fname = Io::Output($row['fname']);
		$sname = Io::Output($row['sname']);
		$cname = Io::Output($row['cname']);
		$created = Io::Output($row['created']);

        $lastmod = Time::Output($created,"%Y-%m-%d"," ",true);

		echo "\t<url>\n";
			echo "\t\t<loc>".RewriteUrl($config_sys['site_url']._DS."index.php?"._NODE."=$plugname&sec=$sname&cat=$cname&title=$fname")."</loc>\n";
			echo "\t\t<lastmod>$lastmod</lastmod>\n";
			echo "\t\t<changefreq>weekly</changefreq>\n";
			echo "\t\t<priority>0.5</priority>\n";
		echo "\t</url>\n";
	}
} else {
	foreach ($result as $row) {
		$ftitle = Io::Output($row['ftitle']);
		$fname = Io::Output($row['fname']);
		$sname = Io::Output($row['sname']);
		$cname = Io::Output($row['cname']);

		echo "<div>&nbsp;&nbsp;&nbsp;&nbsp;<a href='".RewriteUrl($config_sys['site_url']._DS."index.php?"._NODE."=$plugname&sec=$sname&cat=$cname&title=$fname")."' title='".CleanTitleAtr($ftitle)."'>$ftitle</a></div>\n";
	}
}

?>