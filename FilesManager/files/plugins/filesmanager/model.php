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

class filesmanagerModel extends Views {
	public function _index() {
		global $config_sys;

		Utils::Redirect($config_sys['site_url']);
	}

	public function DownloadFile() {
		global $Db,$config_sys,$User;

		$download = false;
		$message = _t("FILE_NOT_FOUND");
		
		$id = Io::GetVar("GET","id","[^a-zA-Z0-9\-]");
		if ($row = $Db->GetRow("SELECT * FROM #__filemgr WHERE id='".intval($id)."'")) {
			$file_name = Io::Output($row['file_name']);
			$file_ext = Io::Output($row['file_ext']);
			$file = "assets"._DS."files"._DS.$config_sys['files_path']._DS.$file_name.".zip";
			$name = Io::Output($row['title']);
			$size = Io::Output($row['size']);
			$roles = Utils::Unserialize(Io::Output($row['roles']));

			if (file_exists($file)) $download = true;
			if (!$User->CheckRole($roles) && !$User->IsAdmin()) {
				$download = false;
				$message = _t("NOT_AUTH_TO_ACCESS_X",MB::strtolower(_t("FILE")));
			}
		}
		
		if ($download===true) {
			$Db->Query("UPDATE #__filemgr SET hits=hits+1 WHERE id='".intval($id)."'");
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=\"$name.$file_ext\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: $size");
			readfile($file) or die(_t("FILE_NOT_FOUND"));
			exit();
			die();
		} else {
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();

			Error::Trigger("USERERROR",$message);

			//Assign captured content to the template engine and clean buffer
			Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,
												 "showtitle"=>_PLUGIN_SHOWTITLE,
												 "url"=>"index.php?"._NODE."="._PLUGIN,
												 "content"=>Utils::GetBufferContent("clean"),
												 "before"=>_PLUGIN_BEFORE,
												 "after"=>_PLUGIN_AFTER));
			//Draw site template
			Template::Draw();
			//Initialize and show site footer
			Layout::Footer();
		}
	}
}

?>