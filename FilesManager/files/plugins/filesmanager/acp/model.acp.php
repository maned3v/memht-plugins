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
defined("_ADMINCP") or die("Access denied");

class filesmanagerModel {
	function Main() {
		global $Db,$config_sys;
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		?>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _PLUGIN_TITLE; ?></div>
                        <div class="body">

        <?php
        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
			echo "<thead>\n";
				echo "<tr>\n";
					echo "<th colspan='6'>"._t("LAST_X",MB::strtolower(_t("FILES")))."</th>\n";
				echo "</tr>\n";
			echo "</thead>\n";
            echo "<thead>\n";
			echo "<tr>\n";
				echo "<th width='27%'>"._t("TITLE")."</th>\n";
				echo "<th width='3%' style='text-align:center;'>"._t("TYPE")."</th>\n";
				echo "<th width='10%' style='text-align:right;'>"._t("SIZE")."&nbsp;&nbsp;</th>\n";
				echo "<th width='20%'>"._t("AUTHOR")."</th>\n";
				echo "<th width='20%'>"._t("UPLOADED")."</th>\n";
				echo "<th width='20%'>"._t("LINK")."</th>\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody>\n";

            if ($result = $Db->GetList("SELECT f.*,u.name AS autname FROM #__filemgr AS f JOIN #__user AS u ON f.author=u.uid ORDER BY f.id DESC LIMIT 5")) {

				$preroles = Ram::Get("roles");
				$preroles['ALL']['name'] = _t("EVERYONE");

				foreach ($result as $row) {
					$id			= Io::Output($row['id'],"int");
					$name		= Io::Output($row['title']);
					$file_name	= Io::Output($row['file_name']);
					$file_ext	= Io::Output($row['file_ext']);
					$size		= Io::Output($row['size'],"int");
					$author		= Io::Output($row['autname']);
					$uploaded	= Time::Output(Io::Output($row['uploaded']));
					$roles		= Utils::Unserialize(Io::Output($row['roles']));
					if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');

					$ronames = array();
					foreach ($roles as $role) if (isset($preroles[$role]['name'])) $ronames[] = $preroles[$role]['name'];
					$roles = _t("WHO_ACCESS_THE_X",MB::strtolower("FILE")).": ".implode(", ",$ronames);

                    echo "<tr>\n";
						echo "<td>\n";
							echo "<a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'><strong>$name</strong></a>\n";
						echo "</td>\n";
						echo "<td style='text-align:center;'><img src='plugins"._DS._PLUGIN._DS."icons"._DS."$file_ext.png' alt='$id' title='$file_ext' /></td>\n";
						echo "<td style='text-align:right;'>".Utils::Bytes2str($size)."&nbsp;&nbsp;</td>\n";
						echo "<td>$author</td>\n";
						echo "<td>$uploaded</td>\n";
						echo "<td class='roles' style='white-space:nowrap;'><span title='".CleanTitleAtr($roles)."'>&nbsp;</span><input type='text' value='".RewriteUrl("index.php?"._NODE."="._PLUGIN."&amp;op=get&amp;id=$id")."' class='sys_form_text' onclick='this.select()' /></td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td style='text-align:center;' colspan='6'>"._t("LIST_EMPTY")."</td>\n";
				echo "</tr>\n";
			}
		?>
			</tbody>
        </table>
		<br />
        <?php

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
			echo "<thead>\n";
				echo "<tr>\n";
					echo "<th colspan='5'>"._t("CATEGORIES")."</th>\n";
				echo "</tr>\n";
			echo "</thead>\n";
			if ($cresult = $Db->GetList("SELECT c.id,c.title,c.name,(SELECT COUNT(f.id) AS tot FROM #__filemgr AS f WHERE f.category=c.id) AS files FROM #__filemgr_categories AS c
										ORDER BY c.title")) {

				echo "<thead>\n";
				echo "<tr>\n";
					echo "<th width='60%'>"._t("TITLE")."</th>\n";
					echo "<th width='30%'>"._t("NAME")."</th>\n";
					echo "<th width='10%' style='text-align:right;'>"._t("FILES")."</th>\n";
				echo "</tr>\n";
				echo "</thead>\n";
				echo "<tbody>\n";

				foreach ($cresult as $crow) {
					$id		= Io::Output($crow['id'],"int");
					$title		= Io::Output($crow['title']);
					$name		= Io::Output($crow['name']);
					$files	= Io::Output($crow['files'],"int");

					echo "<tr>\n";
						echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=browse&amp;id=$id' title='"._t("BROWSE_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$title</strong></a></td>\n";
						echo "<td>$name</td>\n";
						echo "<td style='text-align:right;'>$files</td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td colspan='3' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
				echo "</tr>\n";
			}
		?>
                                </tbody>
                            </table>
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

	function BrowseCategory() {
		global $Db,$config_sys;
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		$cid = Io::GetVar('GET','id','int');

		?>

		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				//Delete permanently
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X",MB::strtolower(_t("FILES"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "xml",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=delete",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=browse&id=<?php echo $cid; ?>';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("FILE"))); ?>');
					}
				});
			});
		</script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

		<?php

		$cid = Io::GetVar('GET','id','int');
		if ($row = $Db->GetRow("SELECT * FROM #__filemgr_categories WHERE id=".intval($cid))) {
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
				<tr>
					<td style="vertical-align:top;">
						<div class="widget ui-widget-content ui-corner-all">
							<div class="ui-widget-header"><?php echo _t("BROWSE_X",MB::strtolower(_t("CATEGORY"))); ?></div>
							<div class="body">

			<?php

			echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
				//Delete permanently
				echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
			echo "</div>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
				echo "<thead>\n";
					echo "<tr>\n";
						echo "<th colspan='7'>"._t("FILES")."</th>\n";
					echo "</tr>\n";
				echo "</thead>\n";
				echo "<thead>\n";
				echo "<tr>\n";
					echo "<th width='1%' style='text-align:right;'><input type='checkbox' id='selectall' /></th>\n";
					echo "<th width='26%'>"._t("TITLE")."</th>\n";
					echo "<th width='3%' style='text-align:center;'>"._t("TYPE")."</th>\n";
					echo "<th width='10%' style='text-align:right;'>"._t("SIZE")."&nbsp;&nbsp;</th>\n";
					echo "<th width='20%'>"._t("AUTHOR")."</th>\n";
					echo "<th width='20%'>"._t("UPLOADED")."</th>\n";
					echo "<th width='20%'>"._t("LINK")."</th>\n";
				echo "</tr>\n";
				echo "</thead>\n";
				echo "<tbody>\n";

				//Options
				$sortby = $Db->_e(Io::GetVar("GET","sortby",false,true,"id"));
				$order = $Db->_e(Io::GetVar("GET","order",false,true,"DESC"));
				$limit = Io::GetVar("GET","limit","int",false,10);

				//Pagination
				$page = Io::GetVar("GET","page","int",false,1);
				if ($page<=0) $page = 1;
				$from = ($page * $limit) - $limit;

				if ($result = $Db->GetList("SELECT f.*,u.name AS autname FROM #__filemgr AS f JOIN #__user AS u ON f.author=u.uid
											WHERE f.category='".intval($cid)."'
											ORDER BY f.{$sortby} $order
											LIMIT ".intval($from).",".intval($limit))) {

					$preroles = Ram::Get("roles");
					$preroles['ALL']['name'] = _t("EVERYONE");

					foreach ($result as $row) {
						$id			= Io::Output($row['id'],"int");
						$name		= Io::Output($row['title']);
						$file_name	= Io::Output($row['file_name']);
						$file_ext	= Io::Output($row['file_ext']);
						$size		= Io::Output($row['size'],"int");
						$author		= Io::Output($row['autname']);
						$uploaded	= Time::Output(Io::Output($row['uploaded']));
						$roles		= Utils::Unserialize(Io::Output($row['roles']));
						if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');

						$ronames = array();
						foreach ($roles as $role) if (isset($preroles[$role]['name'])) $ronames[] = $preroles[$role]['name'];
						$roles = _t("WHO_ACCESS_THE_X",MB::strtolower("FILE")).": ".implode(", ",$ronames);

						echo "<tr>\n";
							echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
							echo "<td>\n";
								echo "<a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'><strong>$name</strong></a>\n";
							echo "</td>\n";
							echo "<td style='text-align:center;'><img src='plugins"._DS._PLUGIN._DS."icons"._DS."$file_ext.png' alt='$id' title='$file_ext' /></td>\n";
							echo "<td style='text-align:right;'>".Utils::Bytes2str($size)."&nbsp;&nbsp;</td>\n";
							echo "<td>$author</td>\n";
							echo "<td>$uploaded</td>\n";
							echo "<td class='roles' style='white-space:nowrap;'><span title='".CleanTitleAtr($roles)."'>&nbsp;</span><input type='text' value='".RewriteUrl("index.php?"._NODE."="._PLUGIN."&amp;op=get&amp;id=$id")."' class='sys_form_text' onclick='this.select()' /></td>\n";
						echo "</tr>\n";
					}
				} else {
					echo "<tbody>\n";
					echo "<tr>\n";
						echo "<td style='text-align:center;' colspan='7'>"._t("LIST_EMPTY")."</td>\n";
					echo "</tr>\n";
				}
			?>
				</tbody>
			</table>

			<?php
			include_once(_PATH_ACP_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
			$Pag = new Pagination();
			$Pag->page = $page;
			$Pag->limit = $limit;
			$Pag->query = "SELECT COUNT(f.id) AS tot FROM #__filemgr AS f JOIN #__user AS u ON f.author=u.uid WHERE f.category='".intval($cid)."'";
			$Pag->url = "admin.php?cont="._PLUGIN."&amp;op=browse&amp;id=$cid&amp;page={PAGE}";
			echo $Pag->Show();
			?>

							</div>
						</div>
					</td>
				</tr>
			</table>
			<?php
		} else {
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
			<tr>
				<td style="vertical-align:top;">
					<div class="widget ui-widget-content ui-corner-all">
						<div class="ui-widget-header"><?php echo _t("BROWSE_X",MB::strtolower(_t("CATEGORY"))); ?></div>
						<div class="body">
						<?php
						Error::Trigger("USERERROR",_t("X_NOT_FOUND",_t("CATEGORY")));
						?>
						</div>
					</div>
				</td>
			</tr>
			</table>
			<?php
		}

		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();
	}

	function UploadFile() {
		global $Db,$config_sys,$User,$Router;

		//Initialize and show site header
		Layout::Header(array("editor"=>true));
		//Start buffering content
		Utils::StartBuffering();

		?>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("UPLOAD"); ?></div>
                        <div class="body">

						<?php

                        if (!isset($_POST['upload'])) {
							$form = new Form();
							$form->action = "admin.php?cont="._PLUGIN."&amp;op=upload";
							$form->enctype = "multipart/form-data";
							$form->Open();

                            //Title
							$form->AddElement(array("element"	=>"text",
													"label"		=>_t("TITLE"),
													"width"		=>"300px",
													"name"		=>"title",
													"id"		=>"title"));

							//Category
							$select = array();
							$result = $Db->GetList("SELECT id,title FROM #__filemgr_categories ORDER by title");
							foreach ($result as $row) {
								$id = Io::Output($row['id'],"int");
								$title = Io::Output($row['title']);

								foreach ($result as $row) {
									$select[Io::Output($row['title'])] = Io::Output($row['id'],"int");
								}
							}
							$form->AddElement(array("element"	=>"select",
													"label"		=>_t("CATEGORY"),
													"name"		=>"category",
													"values"	=>$select));

							$max_size = $Router->GetOption("file_size",3145728);
							$max_size /= 1024;
							
							//File
							$form->AddElement(array("element"	=>"file",
													"label"		=>_t("FILE"),
													"name"		=>"file",
													"size"		=>30,
													"info"		=>_t("ACCEPTED_FILE_TYPES_X","Gif, Jpg, Png, Zip, Rar, Pdf")."<br />"._t("MAX_FILESIZE_X",$max_size."Kb")));

							?>
									</div>
								</div>
                            </td>
							<td class="sidebar">
								<div class="widget ui-widget-content ui-corner-all">
                                  	<div class="ui-widget-header"><?php echo _t("AUTHORIZATION_MANAGER"); ?></div>
									<div class="body">
                            <?php

                            //Required roles
							$result = $Db->GetList("SELECT title,label FROM #__rba_roles ORDER BY rid");
							$rba = array();
							$rba[_t("EVERYONE")] = "ALL";
							foreach ($result as $row) $rba[Io::Output($row['title'])] = Io::Output($row['label']);
							$form->AddElement(array("element"	=>"select",
													"label"		=>_t("WHO_ACCESS_THE_X",MB::strtolower("FILE")),
													"name"		=>"roles[]",
													"multiple"	=>true,
													"values"	=>$rba,
													"selected"	=>"ALL",
													"info"		=>_t("MULTIPLE_CHOICES_ALLOWED")));
                            ?>
									</div>
								</div>
                            <?php
                            //Upload
							$form->AddElement(array("element"	=>"submit",
													"name"		=>"upload",
													"inline"	=>true,
													"value"		=>_t("UPLOAD")));

                            $form->Close();
                        } else {
                            //Check token
							if (Utils::CheckToken()) {
								//Get POST data
								$title = Io::GetVar('POST','title','fullhtml');
								$category = Io::GetVar('POST','category','int');
								$roles = Io::GetVar('POST','roles','nohtml',true,array());

								$errors = array();
								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
								if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));

								if (!sizeof($errors)) {
									//Upload
									include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.filemgr.class.php");
									$Up = new UploadFile();
									$Up->field = "file";
									$Up->max_size = $Router->GetOption("file_size",3145728);
									if (!$filename = $Up->Upload()) $errors[] = implode(",",$Up->GetErrors());
									$ext = $Up->ext;
									$name = $Up->name;
									$size = $Up->size;
								}

								if (!sizeof($errors)) {
									if (in_array("ALL",$roles)) $roles = array();
									$roles = Utils::Serialize($roles);

									$Db->Query("INSERT INTO #__filemgr (category,title,file_name,file_ext,size,author,uploaded,ip,roles)
												VALUES ('".intval($category)."','".$Db->_e($title)."','".$Db->_e($name)."','".$Db->_e($ext)."','".$Db->_e($size)."','".intval($User->Uid())."',NOW(),'".$Db->_e(Utils::Ip2num($User->Ip()))."','".$Db->_e($roles)."')");

									Error::Trigger("INFO",_t("UPLOADED"));
									Utils::Redirect("admin.php?cont="._PLUGIN,4000);
								} else {
									Error::Trigger("USERERROR",implode("<br />",$errors));
								}
							} else {
								Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
							}
                        ?>
                            </div>
                        </div>
                        <?php
                        }

						?>
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

	function EditFile() {
		global $Db,$config_sys,$User,$Router;

		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

		<?php
		echo "<div style='clear:both;'>\n";
		
		$id = Io::GetVar('GET','id','int');
		if ($dbrow = $Db->GetRow("SELECT * FROM #__filemgr WHERE id=".intval($id))) {
			if (!isset($_POST['save'])) {
				//Get values from db
				$category 	= Io::Output($dbrow['category'],"int");
				$title 		= Io::Output($dbrow['title']);
				$roles		= Utils::Unserialize(Io::Output($dbrow['roles']));

				$form = new Form();
				$form->action = "admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id";
				$form->enctype = "multipart/form-data";
				$form->Open();

				?>

				<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
				<tr>
					<td style="vertical-align:top;">
						<div class="widget ui-widget-content ui-corner-all">
							<div class="ui-widget-header"><?php echo _t("EDIT_X",MB::strtolower(_t("FILE"))); ?></div>
							<div class="body">
								<?php

								//Title
								$form->AddElement(array("element"	=>"text",
														"label"		=>_t("TITLE"),
														"width"		=>"300px",
														"name"		=>"title",
														"id"		=>"title",
														"value"		=>$title));

								//Category
								$select = array();
								$result = $Db->GetList("SELECT id,title FROM #__filemgr_categories ORDER by title");
								foreach ($result as $row) {
									$id = Io::Output($row['id'],"int");
									$title = Io::Output($row['title']);

									foreach ($result as $row) {
										$select[Io::Output($row['title'])] = Io::Output($row['id'],"int");
									}
								}
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("CATEGORY"),
														"name"		=>"category",
														"selected"	=>$category,
														"values"	=>$select));

								$max_size = $Router->GetOption("file_size",3145728);
								$max_size /= 1024;
								
								//File
								$form->AddElement(array("element"	=>"file",
														"label"		=>_t("FILE"),
														"name"		=>"file",
														"size"		=>30,
														"info"		=>_t("ACCEPTED_FILE_TYPES_X","Gif, Jpg, Png, Zip, Rar, Pdf")."<br />"._t("MAX_FILESIZE_X",$max_size."Kb")));

								?>
							</div>
						</div>
					</td>
					<td class="sidebar">
                        <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("AUTHORIZATION_MANAGER"); ?></div>
							<div class="body">
								<?php

								//Required roles
								$result = $Db->GetList("SELECT title,label FROM #__rba_roles ORDER BY rid");
								$rba = array();
								$rba[_t("EVERYONE")] = "ALL";
								if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');
								foreach ($result as $row) $rba[Io::Output($row['title'])] = Io::Output($row['label']);
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("WHO_ACCESS_THE_X",MB::strtolower("ARTICLE")),
														"name"		=>"roles[]",
														"multiple"	=>true,
														"values"	=>$rba,
														"selected"	=>$roles,
														"info"		=>_t("MULTIPLE_CHOICES_ALLOWED")));

								?>
							</div>
						</div>
						<div>
							<?php

							//Save
							$form->AddElement(array("element"	=>"submit",
													"name"		=>"save",
													"inline"	=>true,
													"value"		=>_t("SAVE")));

							?>
						</div>
					</td>
				</tr>
				</table>
				<?php

				$form->Close();
			} else {
				//Check token
				if (Utils::CheckToken()) {
					//Get POST data
					$title = Io::GetVar('POST','title','fullhtml');
					$category = Io::GetVar('POST','category','int');
					$roles = Io::GetVar('POST','roles','nohtml',true,array());
					
					$errors = array();
					if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
					if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));

					if (!sizeof($errors)) {
						//Upload
						include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.filemgr.class.php");
						$Up = new UploadFile();
						$Up->field = "file";
						$Up->max_size = $Router->GetOption("file_size",3145728);
						if ($filename = $Up->Upload()) {
							$ext = $Up->ext;
							$name = $Up->name;
							$size = $Up->size;

							//Get values from db
							$file = Io::Output($dbrow['file_name']).".zip";
							
							@unlink($Up->path.$file);
						} else if(!$Up->Selected()) {
							//Get values from db
							$ext = Io::Output($dbrow['file_ext']);
							$name = Io::Output($dbrow['file_name']);
							$size = Io::Output($dbrow['size'],"int");
						} else {
							$errors[] = implode(",",$Up->GetErrors());
						}
					}

					if (!sizeof($errors)) {
						if (in_array("ALL",$roles)) $roles = array();
						$roles = Utils::Serialize($roles);

						$Db->Query("UPDATE #__filemgr SET category='".intval($category)."',title='".$Db->_e($title)."',file_name='".$Db->_e($name)."',file_ext='".$Db->_e($ext)."',size='".intval($size)."',roles='".$Db->_e($roles)."' WHERE id=".intval($id));

						Utils::Redirect("admin.php?cont="._PLUGIN);
					} else {
						?>
						<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
						<tr>
							<td style="vertical-align:top;">
								<div class="widget ui-widget-content ui-corner-all">
									<div class="ui-widget-header"><?php echo _t("EDIT"); ?></div>
									<div class="body">
									<?php
									Error::Trigger("USERERROR",implode("<br />",$errors));
									?>
									</div>
								</div>
							</td>
							</tr>
						</table>
						<?php
					}
				} else {
					?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
					<tr>
						<td style="vertical-align:top;">
							<div class="widget ui-widget-content ui-corner-all">
								<div class="ui-widget-header"><?php echo _t("EDIT"); ?></div>
								<div class="body">
								<?php
								Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
								?>
								</div>
							</div>
						</td>
					</tr>
					</table>
					<?php
				}
			}
		} else {
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
			<tr>
				<td style="vertical-align:top;">
					<div class="widget ui-widget-content ui-corner-all">
						<div class="ui-widget-header"><?php echo _t("EDIT"); ?></div>
						<div class="body">
						<?php
						Error::Trigger("USERERROR",_t("X_NOT_FOUND",_t("FILE")));
						?>
						</div>
					</div>
				</td>
			</tr>
			</table>
			<?php
		}
		echo "</div>\n";
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();
	}

	function DeleteFiles() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);
		
		$files = array();
		$result = $Db->GetList("SELECT file_name FROM #__filemgr WHERE id IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/files/".$config_sys['files_path']._DS.Io::Output($row['file_name']).".zip");

		$result = $Db->Query("DELETE FROM #__filemgr WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = '<?xml version="1.0" encoding="utf-8"?>\n';
		$xml .= '<response>\n';
			$xml .= '<result>\n';
				$xml .= '<query>'.$result.'</query>\n';
				$xml .= '<rows>'.$total.'</rows>\n';
			$xml .= '</result>\n';
		$xml .= '</response>';
		return $xml;
	}

	function FilesCategories() {
		global $Db,$config_sys;

		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

        <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
                //Create
				$('input#create').click(function() {
                    window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=createcat';
                });

				//Delete permanently
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X",MB::strtolower(_t("CATEGORIES"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "xml",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=deletecat",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=categories';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("CATEGORY"))); ?>');
					}
				});
			});
		</script>

        <div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("MANAGE_CATEGORIES"); ?></div>
                        <div class="body">

        <?php
		echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
            //Create category
			echo "<input type='button' name='create' value='"._t("CREATE_NEW_X",MB::strtolower(_t("CATEGORY")))."' style='margin:2px 0;' class='sys_form_button' id='create' />\n";
		echo "</div>\n";
		echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
			//Delete permanently
			echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
		echo "</div>\n";

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
			if ($result = $Db->GetList("SELECT c.id,c.title,c.name,(SELECT COUNT(f.id) AS tot FROM #__filemgr AS f WHERE f.category=c.id) AS files FROM #__filemgr_categories AS c ORDER BY c.title")) {
				echo "<thead>\n";
				echo "<tr>\n";
					echo "<th width='1%' style='text-align:right;'></th>\n";
					echo "<th width='45%'>"._t("TITLE")."</th>\n";
					echo "<th width='45%'>"._t("NAME")."</th>\n";
					echo "<th width='9%' style='text-align:right;'>"._t("ARTICLES")."</th>\n";
				echo "</tr>\n";
				echo "</thead>\n";
				echo "<tbody>\n";

				foreach ($result as $row) {
					$id		= Io::Output($row['id'],"int");
					$title	= Io::Output($row['title']);
					$name	= Io::Output($row['name']);
					$files	= Io::Output($row['files'],"int");

					echo "<tr>\n";
						echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
						echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("CATEGORY")))."'>$title</a></td>\n";
						echo "<td>$name</td>\n";
						echo "<td style='text-align:right;'>$files</td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td colspan='4' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
				echo "</tr>\n";
			}
		?>
                                </tbody>
                            </table>
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

	function DeleteFilesCategory() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$res = $Db->Query("DELETE FROM #__filemgr_categories WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		
		$files = array();
		$result = $Db->GetList("SELECT file_name FROM #__filemgr WHERE category IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/files/".$config_sys['files_path']._DS.Io::Output($row['file_name']).".zip");
		
		$Db->Query("DELETE FROM #__filemgr WHERE category IN (".$Db->_e($items).")");

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = '<?xml version="1.0" encoding="utf-8"?>\n';
		$xml .= '<response>\n';
			$xml .= '<result>\n';
				$xml .= '<query>'.$res.'</query>\n';
				$xml .= '<rows>'.$total.'</rows>\n';
			$xml .= '</result>\n';
		$xml .= '</response>';
		return $xml;
	}

	function CreateFilesCategory() {
		global $Db,$User,$config_sys;

		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

        <script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>alphanumeric<?php echo _DS; ?>jquery.alphanumeric.js"></script>
        <script type="text/javascript">
        	$(document).ready(function() {
				$('#urlvalidname').alphanumeric({
					allow:"-",
					nocaps:true
				});
				$('#autoname').click(function(){
					$.ajax({
						type: "POST",
						dataType: "html",
						url: "admin.php?cont=internal&op=cleanchar&lowercase=1",
						data: "string="+$('#title').val(),
						success: function(data,textStatus,XMLHttpRequest){
							$('#urlvalidname').val(data);
						},
						error: function(XMLHttpRequest,textStatus,errorThrown) {
							$('#urlvalidname').val('Error');
						}
					});
				});
			});
        </script>

        <div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("CREATE_NEW_X",MB::strtolower(_t("CATEGORY"))); ?></div>
                        <div class="body">

						<?php

						if (!isset($_POST['create'])) {
								$form = new Form();
								$form->action = "admin.php?cont="._PLUGIN."&amp;op=createcat";

								$form->Open();

								//Title
								$form->AddElement(array("element"	=>"text",
														"label"		=>_t("TITLE"),
														"width"		=>"300px",
														"name"		=>"title",
														"id"		=>"title"));

								//Name
								$form->AddElement(array("element"	=>"text",
														"label"		=>_t("LINK_NAME"),
														"name"		=>"name",
														"width"		=>"300px",
														"id"		=>"urlvalidname",
														"suffix"	=>"<input type='button' id='autoname' value='"._t("AUTO")."' class='sys_form_button' />",
														"info"		=>_t("NUM_LOWCASE_LATIN_CHARS_DASH_ONLY")));

								?>

                                <div style="padding:2px;"></div>
                                <?php

								//Create
								$form->AddElement(array("element"	=>"submit",
														"name"		=>"create",
														"inline"	=>true,
														"value"		=>_t("CREATE")));

								?>
											</div>
										</div>
									</td>
								</tr>
							</table>
							<?php

							$form->Close();

						} else {
							//Check token
							if (Utils::CheckToken()) {
								//Get POST data
								$title = Io::GetVar('POST','title','fullhtml');
								$name = Io::GetVar('POST','name','[^a-zA-Z0-9\-]');

								$errors = array();
								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
								if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

								if (!sizeof($errors)) {
									$Db->Query("INSERT INTO #__filemgr_categories (title,name)
												VALUES ('".$Db->_e($title)."','".$Db->_e($name)."')");

									Utils::Redirect("admin.php?cont="._PLUGIN."&op=categories");
								} else {
									Error::Trigger("USERERROR",implode("<br />",$errors));
								}
							} else {
								Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
							}

							?>
											</div>
										</div>
									</td>
								</tr>
							</table>

							<?php
						}


		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();
	}

	function EditFilesCategory() {
		global $Db,$User,$config_sys;

		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

        <script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>alphanumeric<?php echo _DS; ?>jquery.alphanumeric.js"></script>
        <script type="text/javascript">
        	$(document).ready(function() {
				$('#urlvalidname').alphanumeric({
					allow:"-",
					nocaps:true
				});
				$('#autoname').click(function(){
					$.ajax({
						type: "POST",
						dataType: "html",
						url: "admin.php?cont=internal&op=cleanchar&lowercase=1",
						data: "string="+$('#title').val(),
						success: function(data,textStatus,XMLHttpRequest){
							$('#urlvalidname').val(data);
						},
						error: function(XMLHttpRequest,textStatus,errorThrown) {
							$('#urlvalidname').val('Error');
						}
					});
				});
			});
        </script>

        <div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("FILES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."files.png' alt='"._t("FILES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=upload' title='"._t("UPLOAD")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."upload.png' alt='"._t("UPLOAD")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("EDIT"); ?></div>
                        <div class="body">

						<?php

						$id = Io::GetVar('GET','id','int');
						if ($valrow = $Db->GetRow("SELECT * FROM #__filemgr_categories WHERE id=".intval($id))) {

							if (!isset($_POST['save'])) {
								$form = new Form();
								$form->action = "admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$id";

								$form->Open();

								//Title
								$form->AddElement(array("element"	=>"text",
														"label"		=>_t("TITLE"),
														"width"		=>"300px",
														"value"		=>Io::Output($valrow['title']),
														"name"		=>"title",
														"id"		=>"title"));

								//Name
								$form->AddElement(array("element"	=>"text",
														"label"		=>_t("LINK_NAME"),
														"name"		=>"name",
														"value"		=>Io::Output($valrow['name']),
														"width"		=>"300px",
														"id"		=>"urlvalidname",
														"suffix"	=>"<input type='button' id='autoname' value='"._t("AUTO")."' class='sys_form_button' />",
														"info"		=>_t("NUM_LOWCASE_LATIN_CHARS_DASH_ONLY")));

								?>

                                <div style="padding:2px;"></div>
                                <?php

								//Save
								$form->AddElement(array("element"	=>"submit",
														"name"		=>"save",
														"inline"	=>true,
														"value"		=>_t("SAVE")));

								$form->Close();
							} else {
								//Check token
								if (Utils::CheckToken()) {
									//Get POST data
									$title = Io::GetVar('POST','title','fullhtml');
									$name = Io::GetVar('POST','name','[^a-zA-Z0-9\-]');
									
									$errors = array();
									if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
									if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

									if (!sizeof($errors)) {
										$Db->Query("UPDATE #__filemgr_categories SET title='".$Db->_e($title)."',
																					name='".$Db->_e($name)."' WHERE id=".intval($id));

										Utils::Redirect("admin.php?cont="._PLUGIN."&op=categories");
									} else {
										Error::Trigger("USERERROR",implode("<br />",$errors));
									}
								} else {
									Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
								}
							}
						} else {
							Error::Trigger("USERERROR",_t("X_NOT_FOUND",_t("CATEGORY")));
						}
						?>

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

?>