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

class galleryController extends galleryModel {
	public function index() {
		$name = Io::GetVar("GET","title","[^a-zA-Z0-9\-]");
		$cat = Io::GetVar("GET","cat","[^a-zA-Z0-9\-]");
		$sec = Io::GetVar("GET","sec","[^a-zA-Z0-9\-]");
		
			 if (!empty($name)) $this->_view($name);
		else if (!empty($cat)) $this->_browsecat($sec,$cat);
		else if (!empty($sec)) $this->_browsesec($sec);
		else $this->_index();
	}
	
	public function rss() {
		$this->_rss();
	}
	
	public function comment() {
		$this->_comment();
	}
	
	public function delcomment() {
		$this->_delcomment();
	}
	
	public function incimg() {
		global $Db;
		//$id = Io::GetVar('POST','id','int');
		//if ($id) $Db->Query("UPDATE #__gallery SET hits=hits+1 WHERE id=".intval($id));
		/*
		 * Could be used to increment image hits with fancybox callback.
		 * Add the following code in the gallery_index fancybox ajax initialization to activate:
		   'titleFormat'	:	function(title, currentArray, currentIndex, currentOpts) {
								var id = currentOpts.orig.attr('alt');
								$.ajax({
									type: "POST",
									dataType: "html",
									url: "{/literal}index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&op=incimg{literal}",
									data: "id="+id
								});
								
								return title;
							}
		 *
		 */
	}
}

?>