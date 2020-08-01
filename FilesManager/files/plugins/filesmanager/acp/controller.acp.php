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

class filesmanagerController extends filesmanagerModel {
	public function index() {
		global $config_sys;
		
		if (is_writable("assets/files/".$config_sys['files_path'])) {
			$this->Main();
		} else {
			//Load plugin language
			Language::LoadPluginFile(_PLUGIN_CONTROLLER);
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();
			
			?>
			<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>

			<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
				<tr>
					<td style="vertical-align:top;">
						<div class="widget ui-widget-content ui-corner-all">
							<div class="ui-widget-header"><?php echo _t("NOTICE"); ?></div>
							<div class="body">
								<?php MemErr::Trigger("USERERROR",_t("CREATE_FOLDER"),"assets/files/<strong>".$config_sys['files_path']."</strong>"); ?>
							</div>
						</div>
					</td>
				</tr>
			</table>

			<?php
			
			//Assign captured content to the template engine and clean buffer
			Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
			//Draw site template
			Template::Draw();
			//Initialize and show site footer
			Layout::Footer();
		}
	}
	public function browse() {
		$this->BrowseCategory();
	}
	public function upload() {
		$this->UploadFile();
	}
	public function edit() {
		$this->EditFile();
	}
	public function delete() {
		$this->DeleteFiles();
	}
	//Categories
	public function categories() {
		$this->FilesCategories();
	}
	public function deletecat() {
		$this->DeleteFilesCategory();
	}
	public function createcat() {
		$this->CreateFilesCategory();
	}
	public function editcat() {
		$this->EditFilesCategory();
	}
}

?>