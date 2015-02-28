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

class DownloadsModel {
	function Main() {
		global $Db,$config_sys;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

		<script type="text/javascript" charset="utf-8">
			function showmenu(id) {
				$("#menu_"+id).toggle();
				$("#status_"+id).toggle();
			}
		</script>
		
		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>
        
        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("FILES"); ?></div>
                        <div class="body">

        <?php

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
            echo "<thead>\n";
			echo "<tr>\n";
				echo "<th colspan='9'>"._t("LAST_X",MB::strtolower(_t("FILES")))."</th>\n";
			echo "</tr>\n";
			echo "<tr>\n";
				echo "<th width='1%' style='text-align:right;'>&nbsp;</th>\n";
				echo "<th width='9%'>"._t("CREATED")."</th>\n";
				echo "<th width='32%'>"._t("TITLE")."</th>\n";
				echo "<th width='15%'>"._t("CATEGORY")."</th>\n";
				echo "<th width='14%'>"._t("AUTHOR")."</th>\n";
				echo "<th width='8%'>"._t("SIZE")."</th>\n";
				echo "<th width='2%' style='text-align:center;'>&nbsp;</th>\n";
				echo "<th width='4%' style='text-align:center;'>&nbsp;</th>\n";
				echo "<th width='15%'>"._t("STATUS")."</th>\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody>\n";

            if ($result = $Db->GetList("SELECT d.*,s.name AS sname, s.title AS stitle,c.name AS cname, c.title AS ctitle, u.name AS autname FROM #__downloads AS d FORCE INDEX(cs)
										JOIN #__downloads_sections AS s JOIN #__downloads_categories AS c JOIN #__user AS u
										ON d.category=c.id AND c.section=s.id AND d.author=u.uid
										ORDER BY d.id DESC LIMIT 5")) {
				
				$preroles = Ram::Get("roles");
				$preroles['ALL']['name'] = _t("EVERYONE");

				//Controller-name match
				$plugmatch = Ram::Get("plugmatch");
				$plugname = isset($plugmatch[_PLUGIN]) ? $plugmatch[_PLUGIN] : _PLUGIN ;

				foreach ($result as $row) {
					$id			= Io::Output($row['id'],"int");
					$title		= Io::Output($row['title']);
					$name		= Io::Output($row['name']);
					$category	= Io::Output($row['category'],"int");
					$sname		= Io::Output($row['sname']);
					$stitle		= Io::Output($row['stitle']);
					$cname		= Io::Output($row['cname']);
					$ctitle		= Io::Output($row['ctitle']);
					$author		= Io::Output($row['autname']);
					$created	= Time::Output(Io::Output($row['created']));
					$desc		= Io::Output($row['description']);
					$size		= Utils::Bytes2Str(Io::Output($row['size'],"int"));
					$options	= Utils::Unserialize(Io::Output($row['options']));
					$roles		= Utils::Unserialize(Io::Output($row['roles']));
					$comments	= Io::Output($row['comments'],"int");
					$status		= MB::ucfirst(Io::Output($row['status']));

					if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');

					//Tags
					$tags = array();
					$dbt = $Db->GetList("SELECT title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item='".intval($id)."'");
					foreach ($dbt as $t) $tags[] = Io::Output($t['title']);
					if (!sizeof($tags)) $tags[] = _t("NO_TAGS");
					$tags = implode(", ",$tags);

					echo "<tr onmouseover='javascript:showmenu($id);' onmouseout='javascript:showmenu($id);'>\n";
						echo "<td><img src='admin/templates/memht/icons/disk.png' alt='Disk{$id}' /><br />&nbsp;</td>\n";
						echo "<td>$created</td>\n";
						echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'><strong>$title</strong></a>\n";
							echo "<div id='menu_$id' style='display:none; margin-top:2px;'>\n";
							echo "<a href='index.php?"._NODE."=$plugname&amp;sec=$sname&amp;cat=$cname&amp;title=$name' title='"._t("VIEW_THIS_X",MB::strtolower(_t("FILE")))."' rel='external'>"._t("VIEW")."</a> - \n";
							echo "<a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'>"._t("EDIT")."</a>\n";
							$ronames = array();
							foreach ($roles as $role) if (isset($preroles[$role]['name'])) $ronames[] = $preroles[$role]['name'];
							$roles = _t("WHO_ACCESS_THE_X",MB::strtolower("FILE")).": ".implode(", ",$ronames);
							echo " - <a title='$roles'>"._t("ROLES")."</a>\n";
							echo "</div>\n";
						echo "</td>\n";
						echo "<td>$ctitle</td>\n";
						echo "<td>$author</td>\n";
						echo "<td>$size</td>\n";
						echo "<td style='text-align:center;' class='tags'><span title='".CleanTitleAtr($tags)."'>&nbsp;</span></td>\n";
						echo "<td style='text-align:center;' class='comments'><span><a href='admin.php?cont="._PLUGIN."&amp;op=comments&amp;id=$id' title='"._t("X_COMMENTS",$comments)."'>$comments</a></span></td>\n";
						echo "<td class='switch'>$status\n";
							/*echo "<div id='status_$id' style='display:none; margin-top:2px;'>\n";
							echo "</div>\n";*/
						echo "</td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td style='text-align:center;' colspan='9'>"._t("LIST_EMPTY")."</td>\n";
				echo "</tr>\n";
			}
		?>
			</tbody>
        </table>
		<br />
        <?php

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
			if ($result = $Db->GetList("SELECT id,title,name FROM #__downloads_sections ORDER BY title")) {
				foreach ($result as $row) {
					$sid	= Io::Output($row['id'],"int");
					$stitle	= Io::Output($row['title']);
					$sname	= Io::Output($row['name']);
					echo "<thead>\n";
						echo "<tr>\n";
							echo "<th colspan='5'>$stitle</th>\n";
						echo "</tr>\n";
					echo "</thead>\n";
					if ($cresult = $Db->GetList("SELECT c.id,c.title,c.name,c.image,c.description,(SELECT COUNT(d.id) AS tot FROM #__downloads AS d WHERE d.category=c.id) AS files FROM #__downloads_categories AS c
												WHERE c.section=$sid AND parent=0
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
							$cid		= Io::Output($crow['id'],"int");
							$ctitle		= Io::Output($crow['title']);
							$cname		= Io::Output($crow['name']);
							$cimage		= Io::Output($crow['image']);
							$cdesc		= Io::Output($crow['description']);
							$cfiles	= Io::Output($crow['files'],"int");

							echo "<tr>\n";
								echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=browsecat&amp;id=$cid' title='"._t("BROWSE_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$ctitle</strong></a></td>\n";
								echo "<td>$cname</td>\n";
								echo "<td style='text-align:right;'>$cfiles</td>\n";
							echo "</tr>\n";

							$csresult = $Db->GetList("SELECT c.id,c.title,c.name,c.image,c.description,(SELECT COUNT(d.id) AS tot FROM #__downloads AS d WHERE d.category=c.id) AS files FROM #__downloads_categories AS c
														WHERE c.parent=$cid
														ORDER BY c.title");
							foreach ($csresult as $csrow) {
								$csid		= Io::Output($csrow['id'],"int");
								$cstitle	= Io::Output($csrow['title']);
								$csname		= Io::Output($csrow['name']);
								$csimage		= Io::Output($csrow['image']);
								$csdesc		= Io::Output($csrow['description']);
								$csfiles	= Io::Output($csrow['files'],"int");

								echo "<tr>\n";
									echo "<td>&nbsp;&nbsp;<img src='images/core/bullet.png' alt='&gt;' />&nbsp;<a href='admin.php?cont="._PLUGIN."&amp;op=browsecat&amp;id=$csid' title='"._t("BROWSE_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$cstitle</strong></a></td>\n";
									echo "<td>$csname</td>\n";
									echo "<td style='text-align:right;'>$csfiles</td>\n";
								echo "</tr>\n";
							}
						}
					} else {
						echo "<tbody>\n";
						echo "<tr>\n";
							echo "<td colspan='3' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
						echo "</tr>\n";
					}
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
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
			function showmenu(id) {
				$("#menu_"+id).toggle();
				$("#status_"+id).toggle();
			}
		</script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

		<?php

		$cid = Io::GetVar('GET','id','int');
		if ($row = $Db->GetRow("SELECT * FROM #__downloads_categories WHERE id=".intval($cid))) {
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
						echo "<th colspan='9'>"._t("FILES")."</th>\n";
					echo "</tr>\n";
				echo "</thead>\n";
				echo "<thead>\n";
				echo "<tr>\n";
					echo "<th width='1%' style='text-align:right;'><input type='checkbox' id='selectall' /></th>\n";
					echo "<th width='9%'>"._t("CREATED")."</th>\n";
					echo "<th width='32%'>"._t("TITLE")."</th>\n";
					echo "<th width='15%'>"._t("CATEGORY")."</th>\n";
					echo "<th width='14%'>"._t("AUTHOR")."</th>\n";
					echo "<th width='8%'>"._t("SIZE")."</th>\n";
					echo "<th width='2%' style='text-align:center;'>&nbsp;</th>\n";
					echo "<th width='4%' style='text-align:center;'>&nbsp;</th>\n";
					echo "<th width='15%'>"._t("STATUS")."</th>\n";
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

				if ($result = $Db->GetList("SELECT d.*,s.name AS sname, s.title AS stitle,c.name AS cname, c.title AS ctitle, u.name AS autname FROM #__downloads AS d FORCE INDEX(cs)
										JOIN #__downloads_sections AS s JOIN #__downloads_categories AS c JOIN #__user AS u
										ON d.category=c.id AND c.section=s.id AND d.author=u.uid
										WHERE d.category='".intval($cid)."'
										ORDER BY d.{$sortby} $order
										LIMIT ".intval($from).",".intval($limit))) {
					$preroles = Ram::Get("roles");
					$preroles['ALL']['name'] = _t("EVERYONE");

					//Controller-name match
					$plugmatch = Ram::Get("plugmatch");
					$plugname = isset($plugmatch[_PLUGIN]) ? $plugmatch[_PLUGIN] : _PLUGIN ;

					foreach ($result as $row) {
						$id			= Io::Output($row['id'],"int");
						$title		= Io::Output($row['title']);
						$name		= Io::Output($row['name']);
						$category	= Io::Output($row['category'],"int");
						$sname		= Io::Output($row['sname']);
						$stitle		= Io::Output($row['stitle']);
						$cname		= Io::Output($row['cname']);
						$ctitle		= Io::Output($row['ctitle']);
						$author		= Io::Output($row['autname']);
						$created	= Time::Output(Io::Output($row['created']));
						$desc		= Io::Output($row['description']);
						$size		= Utils::Bytes2Str(Io::Output($row['size'],"int"));
						$options	= Utils::Unserialize(Io::Output($row['options']));
						$roles		= Utils::Unserialize(Io::Output($row['roles']));
						$comments	= Io::Output($row['comments'],"int");
						$status		= MB::ucfirst(Io::Output($row['status']));

						if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');

						//Tags
						$tags = array();
						$dbt = $Db->GetList("SELECT title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item='".intval($id)."'");
						foreach ($dbt as $t) $tags[] = Io::Output($t['title']);
						if (!sizeof($tags)) $tags[] = _t("NO_TAGS");
						$tags = implode(", ",$tags);

						echo "<tr onmouseover='javascript:showmenu($id);' onmouseout='javascript:showmenu($id);'>\n";
							echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /><br />&nbsp;</td>\n";
							echo "<td>$created</td>\n";
							echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'><strong>$title</strong></a>\n";
								echo "<div id='menu_$id' style='display:none; margin-top:2px;'>\n";
								echo "<a href='index.php?"._NODE."=$plugname&amp;sec=$sname&amp;cat=$cname&amp;title=$name' title='"._t("VIEW_THIS_X",MB::strtolower(_t("FILE")))."' rel='external'>"._t("VIEW")."</a> - \n";
								echo "<a href='admin.php?cont="._PLUGIN."&amp;op=edit&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FILE")))."'>"._t("EDIT")."</a>\n";
								$ronames = array();
								foreach ($roles as $role) if (isset($preroles[$role]['name'])) $ronames[] = $preroles[$role]['name'];
								$roles = _t("WHO_ACCESS_THE_X",MB::strtolower("FILE")).": ".implode(", ",$ronames);
								echo " - <a title='$roles'>"._t("ROLES")."</a>\n";
								echo "</div>\n";
							echo "</td>\n";
							echo "<td>$ctitle</td>\n";
							echo "<td>$author</td>\n";
							echo "<td>$size</td>\n";
							echo "<td style='text-align:center;' class='tags'><span title='".CleanTitleAtr($tags)."'>&nbsp;</span></td>\n";
							echo "<td style='text-align:center;' class='comments'><span><a href='admin.php?cont="._PLUGIN."&amp;op=comments&amp;id=$id' title='"._t("X_COMMENTS",$comments)."'>$comments</a></span></td>\n";
							echo "<td class='switch'>$status\n";
								/*echo "<div id='status_$id' style='display:none; margin-top:2px;'>\n";
								echo "</div>\n";*/
							echo "</td>\n";
						echo "</tr>\n";
					}
				} else {
					echo "<tbody>\n";
					echo "<tr>\n";
						echo "<td style='text-align:center;' colspan='9'>"._t("LIST_EMPTY")."</td>\n";
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
			$Pag->query = "SELECT COUNT(id) AS tot FROM #__downloads WHERE category='".intval($cid)."'";
			$Pag->url = "admin.php?cont="._PLUGIN."&amp;op=browsecat&amp;id=$cid&amp;page={PAGE}";
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

	function AddFile() {
        global $Db,$User,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
		//Start buffering content
		Utils::StartBuffering();

		?>

		<script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>alphanumeric<?php echo _DS; ?>jquery.alphanumeric.js"></script>
		<script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>cloudytag<?php echo _DS; ?>jquery.cloudytag.js"></script>
        <script type="text/javascript">
        	$(document).ready(function() {
				$('#urlvalidname').alphanumeric({
					allow:"-",
					nocaps:true
				});
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: 0
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
				$('#external').change(function() {
                    switch($('#external').val()) {
                        case "1":
                            $('#file').hide();
                            $('#extfile').show();
                            break;
                        default:
                            $('#file').show();
                            $('#extfile').hide();
                            break;
                    }
                });
				$('#tags').cloudyTag();
			});
        </script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>
		<?php
			if (!isset($_POST['add'])) {
				$form = new Form();
				$form->action = "admin.php?cont="._PLUGIN."&amp;op=add";
				$form->enctype = "multipart/form-data";
				$form->Open();
				?>
				        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
				        	<tr>
						       	<td style="vertical-align:top;">
									<div class="widget ui-widget-content ui-corner-all">
					                    <div class="ui-widget-header"><?php echo _t("ADD_NEW_X",MB::strtolower(_t("FILE"))); ?></div>
					                    <div class="body">
											<?php
											
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
											
											//Category
											$disabled = array(0);
											$select = array();
											$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
											foreach ($result as $row) {
												$sid = Io::Output($row['id'],"int");
												$stitle = Io::Output($row['title']);
											
												if ($cresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE section=$sid AND parent=0 ORDER BY title")) {
													$select[Io::Output($row['title'])] = 0;
													foreach ($cresult as $crow) {
														$cid = Io::Output($crow['id'],"int");
														$select["&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($crow['title'])] = $cid;
											
														$csresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE parent=$cid ORDER BY title");
														foreach ($csresult as $csrow) {
															$select["&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($csrow['title'])] = Io::Output($csrow['id'],"int");
														}
													}
												}
											}
											$form->AddElement(array("element"	=>"select",
													"label"		=>_t("CATEGORY"),
													"name"		=>"category",
													"values"	=>$select,
													"optdisabled"=>$disabled));
											
											//Select external
											$form->AddElement(array("element"	=>"select",
													"label"		=>_t("EXTERNAL"),
													"name"		=>"external",
													"id"		=>"external",
													"selected"	=>0,
													"values"	=>array(_t("YES") => 1,
															_t("NO") => 0)));
											
											echo "<div id='file'>\n";
												
											$max_size = $Router->GetOption("file_size",3145728);
											$max_size /= 1024;
												
											//File
											$form->AddElement(array("element"	=>"file",
													"label"		=>_t("FILE"),
													"name"		=>"file",
													"size"		=>30,
													"info"		=>_t("MAX_FILESIZE_X",$max_size."Kb")));
											
											$max_size = $Router->GetOption("img_size",512000);
											$max_size /= 1024;
											$max_w = $Router->GetOption("img_width",225);
											$max_h = $Router->GetOption("img_height",225);
												
											//Image
											$form->AddElement(array("element"	=>"file",
													"label"		=>_t("IMAGE"),
													"name"		=>"image",
													"size"		=>30,
													"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));
											echo "</div>\n";
											
												
											echo "<div id='extfile' style='display:none'>\n";
											//External path
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("FILE"),
													"width"		=>"300px",
													"name"		=>"extpath",
													"id"		=>"extpath",
													"info"		=>"http://..."));
											
											//External image
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("IMAGE"),
													"width"		=>"300px",
													"name"		=>"extimage",
													"info"		=>"http://..."));
											
											//Size
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("SIZE"),
													"width"		=>"150px",
													"name"		=>"size",
													"id"		=>"size",
													"info"		=>_t("VAL_IN_BYTES")));
											echo "</div>\n";
											
											//Version
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("VERSION"),
													"width"		=>"150px",
													"name"		=>"version",
													"id"		=>"version"));
											
											//End user license
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("END_USER_LICENSE"),
													"width"		=>"300px",
													"name"		=>"license",
													"id"		=>"license"));
											
											//Demo link
											$form->AddElement(array("element"	=>"text",
													"label"		=>_t("DEMO_LINK"),
													"width"		=>"300px",
													"name"		=>"demo",
													"id"		=>"demo",
													"info"		=>"http://..."));
											
											//Description
											$form->AddElement(array("element"	=>"textarea",
													"label"		=>_t("DESCRIPTION"),
													"name"		=>"description",
													"height"	=>"200px",
													"class"		=>"simple"));
											
											?>
									</div>
                            	</td>
								<td class="sidebar">
									<div class="widget ui-widget-content ui-corner-all">
										<div class="ui-widget-header"><?php echo _t("OPTIONS"); ?></div>
										<div class="body">
											<?php

											//Comments
											$form->AddElement(array("element"	=>"select",
																	"label"		=>_t("COMMENTS"),
																	"name"		=>"usecomments",
																	"values"	=>array(_t("ENABLED") => 1,
																						_t("DISABLED") => 0)));
	
											//Start
											$form->AddElement(array("element"	=>"text",
																	"label"		=>_t("START"),
																	"name"		=>"start",
																	"class"		=>"sys_form_text datepicker",
																	"width"		=>"150px",
																	"suffix"	=>"<img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."images"._DS."calendar.png' alt='Start' />"));
	
											//End
											$form->AddElement(array("element"	=>"text",
																	"label"		=>_t("END"),
																	"name"		=>"end",
																	"class"		=>"sys_form_text datepicker",
																	"width"		=>"150px",
																	"suffix"	=>"<img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."images"._DS."calendar.png' alt='End' />"));
	
											//Tags
											$form->AddElement(array("element"	=>"text",
																	"label"		=>_t("TAGS"),
																	"id"		=>"tags",
																	"width"		=>"110px",
																	"class"		=>"sys_form_text",
																	"suffix"	=>"<input type='button' id='addtag' value='"._t("ADD")."' class='sys_form_button' />"));
											
											//Status
											$form->AddElement(array("element"	=>"select",
																	"label"		=>_t("STATUS"),
																	"name"		=>"status",
																	"values"	=>array(_t("ACTIVE") => "active",
																						_t("INACTIVE") => "inactive")));
											
											?>
										</div>
									</div>
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
																	"label"		=>_t("WHO_ACCESS_THE_X",MB::strtolower("IMAGE")),
																	"name"		=>"roles[]",
																	"multiple"	=>true,
																	"values"	=>$rba,
																	"selected"	=>"ALL",
																	"info"		=>_t("MULTIPLE_CHOICES_ALLOWED")));
				                            ?>
	                        			</div>
                        			</div>
                        			<?php
		                            //Install
									$form->AddElement(array("element"	=>"submit",
															"name"		=>"add",
															"inline"	=>true,
															"value"		=>_t("ADD")));
									?>
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
					$category = Io::GetVar('POST','category','int');
					$external = Io::GetVar('POST','external','int');
					$extpath = Io::GetVar('POST','extpath');
					$extimage = Io::GetVar('POST','extimage');
					$size = Io::GetVar('POST','size','int');
					$version = Io::GetVar('POST','version','nohtml');
					$license = Io::GetVar('POST','license','nohtml');
					$demo = Io::GetVar('POST','demo');
                    $description = Io::GetVar('POST','description','fullhtml',false);
                    $usecomments = Io::GetVar('POST','usecomments','int');
                    $start = Io::GetVar('POST','start',false,true,'2001-01-01 00:00:00');
					$end = Io::GetVar('POST','end',false,true,'2199-01-01 00:00:00');
					$roles = Io::GetVar('POST','roles','nohtml',true,array());
					$tags = Io::GetVar('POST','tags','nohtml',true,array());
                    $status = Io::GetVar('POST','status','nohtml');
                            
					$errors = array();
					if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
					if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));
					if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));
					if ($external==1 && empty($extpath)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("FILE"));

					if (!sizeof($errors) && $external==0) {
						//Upload file
						include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.filemgr.class.php");
						$Up = new UploadFile();
						$Up->field = "file";
						$Up->max_size = $Router->GetOption("file_size",3145728);
						$Up->path = "assets/downloads/files/";
						if (!$filename = $Up->Upload()) $errors[] = implode(",",$Up->GetErrors());
						$ext = $Up->ext;
						$filename = $Up->name;
						$size = $Up->size;
	
						if (!sizeof($errors)) {
							//Upload image
							include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
							$Up = new UploadImg();
							$Up->path = "assets/downloads/images/";
							$Up->field = "image";
							$Up->max_size = $Router->GetOption("img_size",512000);
							$Up->max_w = $Router->GetOption("img_width",225);
							$Up->max_h = $Router->GetOption("img_height",225);
							$Up->resize_w = $Router->GetOption("img_resize_width",225);
							$Up->resize_h = $Router->GetOption("img_resize_height",225);
							if ($image = $Up->Upload()) {
								$image = "assets/downloads/images/".$image;
							} else {
								$image = "";
							}
						}
					} else if (!sizeof($errors)) {
						$ext = "";
						$filename = $extpath;
						$image = $extimage;
					}

					if (!sizeof($errors)) {
						$options = array();
						$options = Utils::Serialize($options);

						if (in_array("ALL",$roles)) $roles = array();
						$roles = Utils::Serialize($roles);

						$Db->Query("INSERT INTO #__downloads (category,title,name,author,created,file,ext,external,image,description,demo,license,version,size,start,end,options,usecomments,roles,status)
                                    VALUES ('".intval($category)."','".$Db->_e($title)."','".$Db->_e($name)."','".intval($User->Uid())."',NOW(),'".$Db->_e($filename)."','".$Db->_e($ext)."',
							    			'".intval($external)."','".$Db->_e($image)."','".$Db->_e($description)."','".$Db->_e($demo)."','".$Db->_e($license)."','".$Db->_e($version)."','".intval($size)."',
											'".$Db->_e($start)."','".$Db->_e($end)."','".$Db->_e($options)."','".intval($usecomments)."','".$Db->_e($roles)."','".$Db->_e($status)."')");

						//Tags
						if (sizeof($tags)) {
							$id = intval($Db->InsertId());
							$query = array();
							foreach ($tags as $tag) $query[] = "('".$Db->_e(_PLUGIN)."','".$id."','".$Db->_e($tag)."','".$Db->_e(Utils::CleanString($tag))."')";
							if (sizeof($query)) $Db->Query("INSERT INTO #__tags (controller,item,title,name) VALUES ".implode(",",$query));
						}
								
						Utils::Redirect("admin.php?cont="._PLUGIN);
					} else {
						Error::Trigger("USERERROR",implode("<br />",$errors));
					}
				} else {
					Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
				}
			}

		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();
    }

    function EditFile() {
        global $Db,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
		//Start buffering content
		Utils::StartBuffering();

		?>

		<script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>alphanumeric<?php echo _DS; ?>jquery.alphanumeric.js"></script>
		<script type="text/javascript" src="<?php echo $config_sys['site_url']._DS; ?>libraries<?php echo _DS; ?>jQuery<?php echo _DS; ?>plugins<?php echo _DS; ?>cloudytag<?php echo _DS; ?>jquery.cloudytag.js"></script>
        <script type="text/javascript">
        	$(document).ready(function() {
				$('#urlvalidname').alphanumeric({
					allow:"-",
					nocaps:true
				});
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: 0
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
				$('#external').change(function() {
                    switch($('#external').val()) {
                        case "1":
                            $('#file').hide();
                            $('#extfile').show();
                            break;
                        default:
                            $('#file').show();
                            $('#extfile').hide();
                            break;
                    }
                });
				$('#tags').cloudyTag();
			});
        </script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

		<?php

		echo "<div style='clear:both;'>\n";

		$id = Io::GetVar('GET','id','int');
		if ($row = $Db->GetRow("SELECT * FROM #__downloads WHERE id=".intval($id))) {
			if (!isset($_POST['save'])) {
				//Get values from db
				$title		= Io::Output($row['title']);
				$name		= Io::Output($row['name']);
				$category	= Io::Output($row['category'],"int");
				$external	= Io::Output($row['external'],"int");
				$file		= Io::Output($row['file']);
				$image		= Io::Output($row['image']);
				$desc		= Io::Output($row['description']);
				$size		= Io::Output($row['size'],"int");
				$version	= Io::Output($row['version']);
				$license	= Io::Output($row['license']);
				$demo		= Io::Output($row['demo']);
				$start		= Io::Output($row['start']);
				$end		= Io::Output($row['end']);
				$usecomments= Io::Output($row['usecomments'],"int");
				$options	= Utils::Unserialize(Io::Output($row['options']));
				$roles		= Utils::Unserialize(Io::Output($row['roles']));
				$comments	= Io::Output($row['comments'],"int");
				$status		= Io::Output($row['status']);
				
				//Tags
				$dbtags = $Db->GetList("SELECT title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN)."' AND item=".intval($id));
				$tags = array();
				foreach ($dbtags as $key => $value) $tags[] = $value['title'];
				$tags = implode(",",$tags);

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
															"value"		=>$title,
															"id"		=>"title"));

									//Name
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("LINK_NAME"),
															"name"		=>"name",
															"value"		=>$name,
															"width"		=>"300px",
															"id"		=>"urlvalidname",
															"suffix"	=>"<input type='button' id='autoname' value='"._t("AUTO")."' class='sys_form_button' />",
															"info"		=>_t("NUM_LOWCASE_LATIN_CHARS_DASH_ONLY")));

									//Category
									$disabled = array(0);
									$select = array();
									$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
									foreach ($result as $row) {
										$sid = Io::Output($row['id'],"int");
										$stitle = Io::Output($row['title']);

										if ($cresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE section=$sid AND parent=0 ORDER BY title")) {
											$select[Io::Output($row['title'])] = 0;
											foreach ($cresult as $crow) {
												$cid = Io::Output($crow['id'],"int");
												$select["&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($crow['title'])] = $cid;

												$csresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE parent=$cid ORDER BY title");
												foreach ($csresult as $csrow) {
													$select["&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($csrow['title'])] = Io::Output($csrow['id'],"int");
												}
											}
										}
									}
									$form->AddElement(array("element"	=>"select",
															"label"		=>_t("CATEGORY"),
															"name"		=>"category",
															"values"	=>$select,
															"selected"	=>$category,
															"optdisabled"=>$disabled));

									//Select external
									$form->AddElement(array("element"	=>"select",
															"label"		=>_t("EXTERNAL"),
															"name"		=>"external",
															"id"		=>"external",
															"selected"	=>$external,
															"values"	=>array(_t("YES") => 1,
																				_t("NO") => 0)));

									echo "<div id='file'".(($external==1) ? " style='display:none'" : "").">\n";
									$max_size = $Router->GetOption("file_size",3145728);
									$max_size /= 1024;
									
									//File
									$form->AddElement(array("element"	=>"file",
															"label"		=>_t("FILE"),
															"name"		=>"file",
															"size"		=>30,
															"info"		=>_t("MAX_FILESIZE_X",$max_size."Kb")));

									$max_size = $Router->GetOption("img_size",512000);
									$max_size /= 1024;
									$max_w = $Router->GetOption("img_width",225);
									$max_h = $Router->GetOption("img_height",225);
									
									//Image
									$form->AddElement(array("element"	=>"file",
															"label"		=>_t("IMAGE"),
															"name"		=>"image",
															"size"		=>30,
															"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));
									echo "</div>\n";

									echo "<div id='extfile'".(($external==0) ? " style='display:none'" : "").">\n";
									//External path
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("FILE"),
															"width"		=>"300px",
															"name"		=>"extpath",
															"value"		=>$file,
															"id"		=>"extpath",
															"info"		=>"http://..."));

									//External image
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("IMAGE"),
															"width"		=>"300px",
															"name"		=>"extimage",
															"value"		=>$image,
															"info"		=>"http://..."));

									//Size
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("SIZE"),
															"width"		=>"150px",
															"name"		=>"size",
															"value"		=>$size,
															"id"		=>"size",
															"info"		=>_t("VAL_IN_BYTES")));
									echo "</div>\n";

									//Version
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("VERSION"),
															"width"		=>"150px",
															"name"		=>"version",
															"value"		=>$version,
															"id"		=>"version"));

									//End User License
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("END_USER_LICENSE"),
															"width"		=>"300px",
															"name"		=>"license",
															"value"		=>$license,
															"id"		=>"license"));

									//Demo link
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("DEMO_LINK"),
															"width"		=>"300px",
															"name"		=>"demo",
															"value"		=>$demo,
															"id"		=>"demo",
															"info"		=>"http://..."));

									//Description
									$form->AddElement(array("element"	=>"textarea",
															"label"		=>_t("DESCRIPTION"),
															"name"		=>"description",
															"value"		=>$desc,
															"height"	=>"200px",
															"class"		=>"simple"));

									?>
								</div>
							</div>
						</td>
						<td class="sidebar">
							<div class="widget ui-widget-content ui-corner-all">
								<div class="ui-widget-header"><?php echo _t("OPTIONS"); ?></div>
								<div class="body">
								   <?php

									//Comments
									$form->AddElement(array("element"	=>"select",
															"label"		=>_t("COMMENTS"),
															"name"		=>"usecomments",
															"selected"	=>$usecomments,
															"values"	=>array(_t("ENABLED") => 1,
																				_t("DISABLED") => 0)));

									//Start
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("START"),
															"name"		=>"start",
															"value"		=>$start,
															"class"		=>"sys_form_text datepicker",
															"width"		=>"150px",
															"suffix"	=>"<img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."images"._DS."calendar.png' alt='Start' />"));

									//End
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("END"),
															"name"		=>"end",
															"value"		=>$end,
															"class"		=>"sys_form_text datepicker",
															"width"		=>"150px",
															"suffix"	=>"<img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."images"._DS."calendar.png' alt='End' />"));

									//Tags
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("TAGS"),
															"id"		=>"tags",
															"width"		=>"110px",
															"class"		=>"sys_form_text",
															"value"		=>$tags,
															"suffix"	=>"<input type='button' id='addtag' value='"._t("ADD")."' class='sys_form_button' />"));
									
									//Status
									$form->AddElement(array("element"	=>"select",
															"label"		=>_t("STATUS"),
															"name"		=>"status",
															"selected"	=>$status,
															"values"	=>array(_t("ACTIVE") => "active",
																				_t("INACTIVE") => "inactive")));

									?>
								</div>
							</div>
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
						$name = Io::GetVar('POST','name','[^a-zA-Z0-9\-]');
						$category = Io::GetVar('POST','category','int');
						$external = Io::GetVar('POST','external','int');
						$extpath = Io::GetVar('POST','extpath');
						$extimage = Io::GetVar('POST','extimage');
						$size = Io::GetVar('POST','size','int');
						$version = Io::GetVar('POST','version','nohtml');
						$license = Io::GetVar('POST','license','nohtml');
						$demo = Io::GetVar('POST','demo');
                        $description = Io::GetVar('POST','description','fullhtml',false);
                        $usecomments = Io::GetVar('POST','usecomments','int');
                        $start = Io::GetVar('POST','start',false,true,'2001-01-01 00:00:00');
						$end = Io::GetVar('POST','end',false,true,'2199-01-01 00:00:00');
						$roles = Io::GetVar('POST','roles','nohtml',true,array());
						$tags = Io::GetVar('POST','tags','nohtml',true,array());
                        $status = Io::GetVar('POST','status','nohtml');

						$errors = array();
						if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
						if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));
						if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));
						if ($external==1 && empty($extpath)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("FILE"));

						if (!sizeof($errors) && $external==0) {
							//Upload file
							include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.filemgr.class.php");
							$Up = new UploadFile();
							$Up->field = "file";
							$Up->max_size = $Router->GetOption("file_size",3145728);
							$Up->path = "assets/downloads/files/";
							if ($filename = $Up->Upload()) {
								$ext = $Up->ext;
								$filename = $Up->name;
								$size = $Up->size;

								//Get values from db
								$file = Io::Output($row['file']).".zip";

								@unlink($Up->path.$file);
							} else if (!$Up->Selected()) {
								//Get values from db
								$ext = Io::Output($row['ext']);
								$filename = Io::Output($row['file']);
								$size = Io::Output($row['size'],"int");
							} else {
								$errors[] = implode(",",$Up->GetErrors());
							}

							if (!sizeof($errors)) {
								//Upload image
								include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
								$Up = new UploadImg();
								$Up->path = "assets/downloads/images/";
								$Up->field = "image";
								$Up->max_size = $Router->GetOption("img_size",512000);
								$Up->max_w = $Router->GetOption("img_width",225);
								$Up->max_h = $Router->GetOption("img_height",225);
								$Up->resize_w = $Router->GetOption("img_resize_width",225);
								$Up->resize_h = $Router->GetOption("img_resize_height",225);
								if ($image = $Up->Upload()) {
									$image = "assets/downloads/images/".$image;

									//Get values from db
									$file = Io::Output($row['image']);

									@unlink($file);
								} else if (!$Up->Selected()) {
									$image = Io::Output($row['image']);
								} else {
									$image = "";
								}
							}
						} else if (!sizeof($errors)) {
							$ext = "";
							$filename = $extpath;
							$image = $extimage;
						}

						if (!sizeof($errors)) {
							$options = array();
							$options = Utils::Serialize($options);
							
							if (in_array("ALL",$roles)) $roles = array();
							$roles = Utils::Serialize($roles);

							$Db->Query("UPDATE #__downloads SET category='".intval($category)."',title='".$Db->_e($title)."',name='".$Db->_e($name)."',file='".$Db->_e($filename)."',ext='".$Db->_e($ext)."',
																external='".intval($external)."',image='".$Db->_e($image)."',description='".$Db->_e($description)."',demo='".$Db->_e($demo)."',
																license='".$Db->_e($license)."',version='".$Db->_e($version)."',size='".intval($size)."',start='".$Db->_e($start)."',end='".$Db->_e($end)."',options='".$Db->_e($options)."',
																usecomments='".intval($usecomments)."',roles='".$Db->_e($roles)."',status='".$Db->_e($status)."'
																WHERE id=".intval($id));

							//Tags
							if (sizeof($tags)) {
								$Db->Query("DELETE FROM #__tags WHERE controller='".$Db->_e(_PLUGIN)."' AND item='".intval($id)."'");
								$query = array();
								foreach ($tags as $tag) $query[] = "('".$Db->_e(_PLUGIN)."','".$id."','".$Db->_e($tag)."','".$Db->_e(Utils::CleanString($tag))."')";
								if (sizeof($query)) $Db->Query("INSERT INTO #__tags (controller,item,title,name) VALUES ".implode(",",$query));
							}
							
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

	function DeleteFile() {
        global $Db,$config_sys;

		$items = Io::GetVar("POST","items");

		$result = $Db->GetList("SELECT file FROM #__downloads WHERE external=0 AND id IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/downloads/files/".$config_sys['files_path']._DS.Io::Output($row['file']).".zip");

		$result = $Db->Query("DELETE FROM #__downloads WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		
		$Db->Query("DELETE FROM #__comments WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");
		$Db->Query("DELETE FROM #__ratings WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");

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

	function ShowComments() {
		global $Db,$config_sys,$User;

		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		$id = Io::GetVar("GET","id","int");

		?>
		
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				//Delete
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X",MB::strtolower(_t("COMMENT"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "html",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=delcomments&id=<?php echo $id; ?>",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=comments&id=<?php echo $id; ?>';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("COMMENT"))); ?>');
					}
				});
			});
		</script>
		
		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("COMMENTS"); ?></div>
                        <div class="body">
							<?php
							
							echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
								//Delete
								echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
							echo "</div>\n";

							echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
							echo "<thead>\n";
								echo "<tr>\n";
									echo "<th width='1%'><input type='checkbox' id='selectall' /></th>\n";
									echo "<th width='20%'>"._t("AUTHOR")."</th>\n";
									echo "<th width='45%'>"._t("TEXT")."</th>\n";
									echo "<th width='14%'>"._t("STATUS")."</th>\n";
								echo "</tr>\n";
							echo "</thead>\n";
							echo "<tbody>\n";

                            if ($result = $Db->GetList("SELECT * FROM #__comments WHERE controller='"._PLUGIN."' AND item=".intval($id)." ORDER BY id DESC")) {
								foreach ($result as $row) {
									$cid	= Io::Output($row['id'],"int");
									$author	= Io::Output($row['author']);
									$text	= BBCode::ToHtml(Io::Output($row['text']));
									$status	= MB::ucfirst(Io::Output($row['status']));

									$author = ($author>0) ? $User->Name($author) : Io::Output($row['author_name']) ;

									echo "<tr>\n";
										echo "<td><input type='checkbox' name='selected[]' value='$cid' class='cb' /></td>\n";
										echo "<td>$author</td>\n";
										echo "<td>$text</td>\n";
										echo "<td>$status</td>\n";
                                	echo "</tr>\n";
								}
                            } else {
								echo "<tr>\n";
									echo "<td colspan='4' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
                                echo "</tr>\n";
                            }

							echo "</tbody>\n";
							echo "</table>\n";
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
	
	function DeleteComments() {
		global $Db;

		$id = Io::GetVar("GET","id",false,true);
		$items = Io::GetVar("POST","items",false,true);

		if ($id==0 || $items==0) return;
			
		$result = $Db->Query("DELETE FROM #__comments WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		if ($total) $result = $Db->Query("UPDATE #__downloads SET comments=comments-".intval($total)." WHERE id=".intval($id)) ? 1 : 0 ;

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

	function DownloadsSections() {
		global $Db,$config_sys;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>

        <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				//Create
				$('input#create').click(function() {
                    window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=createsec';
                });

				//Delete permanently
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X_AND_CONTENT",MB::strtolower(_t("SECTIONS"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "xml",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=deletesec",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=sections';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("SECTION"))); ?>');
					}
				});
			});
		</script>

        <div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("MANAGE_SECTIONS"); ?></div>
                        <div class="body">

        <?php
		echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
            //Create section
			echo "<input type='button' name='create' value='"._t("CREATE_NEW_X",MB::strtolower(_t("SECTION")))."' style='margin:2px 0;' class='sys_form_button' id='create' />\n";
		echo "</div>\n";
		echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
			//Delete permanently
			echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
		echo "</div>\n";

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
            echo "<thead>\n";
			echo "<tr>\n";
				echo "<th width='1%' style='text-align:right;'></th>\n";
				echo "<th width='10%'>"._t("IMAGE")."</th>\n";
				echo "<th width='60%'>"._t("TITLE")."</th>\n";
				echo "<th width='29%'>"._t("NAME")."</th>\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody>\n";

            if ($result = $Db->GetList("SELECT * FROM #__downloads_sections ORDER BY title")) {
				foreach ($result as $row) {
					$id	= Io::Output($row['id'],"int");
					$title	= Io::Output($row['title']);
					$name	= Io::Output($row['name']);
					$image	= Io::Output($row['image']);
					$desc	= Io::Output($row['description']);

					$image = (!empty($image)) ? "<img src='assets/downloads/sections/$image' width='50' alt='".CleanTitleAtr($title)."' />" : "";
					
                    echo "<tr>\n";
						echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
						echo "<td>$image</td>\n";
						echo "<td>\n";
							echo "<a href='admin.php?cont="._PLUGIN."&amp;op=editsec&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("SECTION")))."'><strong>$title</strong></a>\n";
							echo "<br /><br /><em>$desc</em>";
						echo "</td>\n";
						echo "<td>$name</td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td style='text-align:center;' colspan='4'>"._t("LIST_EMPTY")."</td>\n";
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

	function DeleteDownloadsSection() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$result = $Db->GetList("SELECT image FROM #__downloads_sections WHERE id IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/downloads/sections"._DS.Io::Output($row['image']));

		$res = $Db->Query("DELETE FROM #__downloads_sections WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		
		$ids = array();
		$result = $Db->GetList("SELECT id,image FROM #__downloads_categories WHERE section IN (".$Db->_e($items).")");
		foreach ($result as $row) {
			$ids[] = Io::Output($row['id']);
			@unlink("assets/downloads/categories/".Io::Output($row['image']));
		}
		$items = implode(",",$ids);
		
		$Db->Query("DELETE FROM #__downloads_categories WHERE id IN (".$Db->_e($items).")");
		
		if (!empty($items)) {
			//Subcategories
			if ($result = $Db->GetList("SELECT id,image FROM #__downloads_categories WHERE parent IN (".$Db->_e($items).")")) {
				foreach ($result as $row) {
					$items .= ",".Io::Output($row['id']);
					@unlink("assets/downloads/categories/".Io::Output($row['image']));
				}
			}
			$Db->Query("DELETE FROM #__downloads_categories WHERE parent IN (".$Db->_e($items).")");
			
			//Download's data
			$ids = array();
			if ($result = $Db->GetList("SELECT id,file,image FROM #__downloads WHERE category IN (".$Db->_e($items).")")) {
				foreach ($result as $row) {
					$ids[] = Io::Output($row['id']);
					@unlink("assets/downloads/files/".$config_sys['files_path']._DS.Io::Output($row['file']).".zip");
					@unlink(Io::Output($row['image']));
				}
			}
			$items = implode(",",$ids);
			
			$Db->Query("DELETE FROM #__downloads WHERE id IN (".$Db->_e($items).")");
			$Db->Query("DELETE FROM #__comments WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");
			$Db->Query("DELETE FROM #__ratings WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");
		}
		
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

	function CreateDownloadsSection() {
		global $Db,$User,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
		?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
        	<tr>
		       	<td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("CREATE_NEW_X",MB::strtolower(_t("SECTION"))); ?></div>
                        <div class="body">

						<?php

						if (!isset($_POST['create'])) {
								$form = new Form();
								$form->action = "admin.php?cont="._PLUGIN."&amp;op=createsec";
								$form->enctype = "multipart/form-data";

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

								$max_size = $Router->GetOption("sec_size",512000);
								$max_size /= 1024;
								$max_w = $Router->GetOption("sec_width",225);
								$max_h = $Router->GetOption("sec_height",225);

								//Image
								$form->AddElement(array("element"	=>"file",
														"label"		=>_t("IMAGE"),
														"name"		=>"image",
														"size"		=>30,
														"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));

								//Description
								$form->AddElement(array("element"	=>"textarea",
														"label"		=>_t("DESCRIPTION"),
														"name"		=>"description",
														"height"	=>"200px",
														"class"		=>"simple"));

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
								$description = Io::GetVar('POST','description','fullhtml',false);

								$errors = array();
								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
								if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

								if (!sizeof($errors)) {
									//Upload
									include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
									$Up = new UploadImg();
									$Up->path = "assets/downloads/sections/";
									$Up->field = "image";
									$Up->max_size = $Router->GetOption("sec_size",512000);
									$Up->max_w = $Router->GetOption("sec_width",225);
									$Up->max_h = $Router->GetOption("sec_height",225);
									$Up->resize_w = $Router->GetOption("sec_resize_width",225);
									$Up->resize_h = $Router->GetOption("sec_resize_height",225);
									if (!$image = $Up->Upload()) $image = "";
								}

								if (!sizeof($errors)) {
									$Db->Query("INSERT INTO #__downloads_sections (title,name,image,description)
												VALUES ('".$Db->_e($title)."','".$Db->_e($name)."','".$Db->_e($image)."','".$Db->_e($description)."')");

									Utils::Redirect("admin.php?cont="._PLUGIN."&op=sections");
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

	function EditDownloadsSection() {
		global $Db,$User,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
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
						if ($valrow = $Db->GetRow("SELECT * FROM #__downloads_sections WHERE id=".intval($id))) {

							if (!isset($_POST['save'])) {
								$form = new Form();
								$form->action = "admin.php?cont="._PLUGIN."&amp;op=editsec&amp;id=$id";
								$form->enctype = "multipart/form-data";

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

								$max_size = $Router->GetOption("sec_size",512000);
								$max_size /= 1024;
								$max_w = $Router->GetOption("sec_width",225);
								$max_h = $Router->GetOption("sec_height",225);

								//Image
								$form->AddElement(array("element"	=>"file",
														"label"		=>_t("IMAGE"),
														"name"		=>"image",
														"size"		=>30,
														"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));

								//Description
								$form->AddElement(array("element"	=>"textarea",
														"label"		=>_t("DESCRIPTION"),
														"name"		=>"description",
														"value"		=>Io::Output($valrow['description']),
														"height"	=>"200px",
														"class"		=>"simple"));

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
									$description = Io::GetVar('POST','description','fullhtml',false);

									$errors = array();
									if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
									if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

									if (!sizeof($errors)) {
										//Upload
										include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
										$Up = new UploadImg();
										$Up->path = "assets/downloads/sections/";
										$Up->field = "image";
										$Up->max_size = $Router->GetOption("sec_size",512000);
										$Up->max_w = $Router->GetOption("sec_width",225);
										$Up->max_h = $Router->GetOption("sec_height",225);
										$Up->resize_w = $Router->GetOption("sec_resize_width",225);
										$Up->resize_h = $Router->GetOption("sec_resize_height",225);
										if ($image = $Up->Upload()) {
											//Delete previous data/files if necessary
											@unlink("assets/downloads/sections/".Io::Output($valrow['image']));
										} else if (!$Up->Selected()) {
											$image = Io::Output($valrow['image']);
										} else {
											$image = "";
										}
									}

									if (!sizeof($errors)) {
										$Db->Query("UPDATE #__downloads_sections
                                                    SET title='".$Db->_e($title)."',name='".$Db->_e($name)."',image='".$Db->_e($image)."',description='".$Db->_e($description)."'
                                                    WHERE id=".intval($id));

										Utils::Redirect("admin.php?cont="._PLUGIN."&op=sections");
									} else {
										Error::Trigger("USERERROR",implode("<br />",$errors));
									}
								} else {
									Error::Trigger("USERERROR",_t("INVALID_TOKEN"));
								}
							}
						} else {
							Error::Trigger("USERERROR",_t("X_NOT_FOUND",_t("SECTION")));
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

	function DownloadsCategories() {
		global $Db,$config_sys;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
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
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X_AND_CONTENT",MB::strtolower(_t("CATEGORIES"))); ?>')) {
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
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
			if ($result = $Db->GetList("SELECT id,title,name FROM #__downloads_sections ORDER BY title")) {
				foreach ($result as $row) {
					$sid	= Io::Output($row['id'],"int");
					$stitle	= Io::Output($row['title']);
					$sname	= Io::Output($row['name']);
					echo "<thead>\n";
						echo "<tr>\n";
							echo "<th colspan='5'>$stitle</th>\n";
						echo "</tr>\n";
					echo "</thead>\n";
					if ($cresult = $Db->GetList("SELECT c.id,c.title,c.name,c.image,c.description,(SELECT COUNT(d.id) AS tot FROM #__downloads AS d WHERE d.category=c.id) AS files FROM #__downloads_categories AS c
												WHERE c.section=$sid AND parent=0
												ORDER BY c.title")) {

						echo "<thead>\n";
						echo "<tr>\n";
							echo "<th width='1%' style='text-align:right;'></th>\n";
							echo "<th width='10%'>"._t("IMAGE")."</th>\n";
							echo "<th width='55%'>"._t("TITLE")."</th>\n";
							echo "<th width='25%'>"._t("NAME")."</th>\n";
							echo "<th width='9%' style='text-align:right;'>"._t("FILES")."</th>\n";
						echo "</tr>\n";
						echo "</thead>\n";
						echo "<tbody>\n";

						foreach ($cresult as $crow) {
							$cid		= Io::Output($crow['id'],"int");
							$ctitle		= Io::Output($crow['title']);
							$cname		= Io::Output($crow['name']);
							$cimage		= Io::Output($crow['image']);
							$cdesc		= Io::Output($crow['description']);
							$cfiles		= Io::Output($crow['files'],"int");

							$cimage = (!empty($image)) ? "<img src='assets/downloads/categories/$cimage' width='50' alt='".CleanTitleAtr($ctitle)."' />" : "";
							
							echo "<tr>\n";
								echo "<td><input type='checkbox' name='selected[]' value='$cid' class='cb' /></td>\n";
								echo "<td>$cimage</td>\n";
								echo "<td>\n";
									echo "<a href='admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$cid' title='"._t("EDIT_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$ctitle</strong></a>\n";
									echo "<br /><br /><em>$cdesc</em>";
								echo "</td>\n";
								echo "<td>$cname</td>\n";
								echo "<td style='text-align:right;'>$cfiles</td>\n";
							echo "</tr>\n";

							$csresult = $Db->GetList("SELECT c.id,c.title,c.name,c.image,c.description,(SELECT COUNT(d.id) AS tot FROM #__downloads AS d WHERE d.category=c.id) AS files FROM #__downloads_categories AS c
														WHERE c.parent=$cid
														ORDER BY c.title");
							foreach ($csresult as $csrow) {
								$csid		= Io::Output($csrow['id'],"int");
								$cstitle	= Io::Output($csrow['title']);
								$csname		= Io::Output($csrow['name']);
								$csimage	= Io::Output($csrow['image']);
								$csdesc		= Io::Output($csrow['description']);
								$csfiles	= Io::Output($csrow['files'],"int");
								
								$csimage = (!empty($image)) ? "<img src='assets/downloads/categories/$csimage' width='50' alt='".CleanTitleAtr($cstitle)."' />" : "";

								echo "<tr>\n";
									echo "<td><input type='checkbox' name='selected[]' value='$csid' class='cb' /></td>\n";
									echo "<td></td>\n";
									echo "<td>\n";
										echo "$cimage\n";
										echo "<div style='margin-left:90px;'><a href='admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$csid' title='"._t("EDIT_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$cstitle</strong></a>\n";
										echo "<br /><br /><em>$csdesc</em></div>";
									echo "</td>\n";
									echo "<td>$csname</td>\n";
									echo "<td style='text-align:right;'>$csfiles</td>\n";
								echo "</tr>\n";
							}
						}
					} else {
						echo "<tbody>\n";
						echo "<tr>\n";
							echo "<td colspan='5' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
						echo "</tr>\n";
					}
				}
			} else {
				echo "<tbody>\n";
				echo "<tr>\n";
					echo "<td style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
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

	function DeleteDownloadsCategory() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$result = $Db->GetList("SELECT image FROM #__downloads_categories WHERE id IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/downloads/categories/".Io::Output($row['image']));

		$res = $Db->Query("DELETE FROM #__downloads_categories WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		
		//Subcategories
		if ($result = $Db->GetList("SELECT id,image FROM #__downloads_categories WHERE parent IN (".$Db->_e($items).")")) {
			foreach ($result as $row) {
				$items .= ",".Io::Output($row['id']);
				@unlink("assets/downloads/categories/".Io::Output($row['image']));
			}
		}
		$Db->Query("DELETE FROM #__downloads_categories WHERE parent IN (".$Db->_e($items).")");
		
		//Download's data
		$ids = array();
		if ($result = $Db->GetList("SELECT id,file,image FROM #__downloads WHERE category IN (".$Db->_e($items).")")) {
			foreach ($result as $row) {
				$ids[] = Io::Output($row['id']);
				@unlink("assets/downloads/files/".$config_sys['files_path']._DS.Io::Output($row['file']).".zip");
				@unlink(Io::Output($row['image']));
			}
		}
		$items = implode(",",$ids);

		$Db->Query("DELETE FROM #__downloads WHERE id IN (".$Db->_e($items).")");
		$Db->Query("DELETE FROM #__comments WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");
		$Db->Query("DELETE FROM #__ratings WHERE controller='"._PLUGIN."' AND item IN (".$Db->_e($items).")");

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

	function CreateDownloadsCategory() {
		global $Db,$User,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
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
								$form->enctype = "multipart/form-data";

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

								$select = array();
								$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
								foreach ($result as $row) {
									$sid = Io::Output($row['id'],"int");
									$stitle = Io::Output($row['title']);

									$select[Io::Output($row['title'])] = $sid;
								}

								//Section
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("SECTION"),
														"name"		=>"section",
														"values"	=>$select));

								$disabled = array("disabled");
								$select = array();
								$select[_t("MAIN_X",MB::strtolower(_t("CATEGORY")))] = 0;
								$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
								foreach ($result as $row) {
									$sid = Io::Output($row['id'],"int");
									$stitle = Io::Output($row['title']);

									if ($cresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE section=$sid AND parent=0 ORDER BY title")) {
										$select[Io::Output($row['title'])] = "disabled";
										foreach ($cresult as $crow) {
											$cid = Io::Output($crow['id'],"int");
											$select["&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($crow['title'])] = $cid;
										}
									}
								}

								//Parent category
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("PARENT_X",MB::strtolower(_t("CATEGORY"))),
														"name"		=>"category",
														"values"	=>$select,
														"optdisabled"=>$disabled));

								$max_size = $Router->GetOption("cat_size",512000);
								$max_size /= 1024;
								$max_w = $Router->GetOption("cat_width",225);
								$max_h = $Router->GetOption("cat_height",225);

								//Image
								$form->AddElement(array("element"	=>"file",
														"label"		=>_t("IMAGE"),
														"name"		=>"image",
														"size"		=>30,
														"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));

								//Description
								$form->AddElement(array("element"	=>"textarea",
														"label"		=>_t("DESCRIPTION"),
														"name"		=>"description",
														"height"	=>"200px",
														"class"		=>"simple"));

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
								$section = Io::GetVar('POST','section','int');
								$category = Io::GetVar('POST','category','int');
								$description = Io::GetVar('POST','description','fullhtml',false);

								if ($category>0) {
									//Fix section id
									$row = $Db->GetRow("SELECT section FROM #__downloads_categories WHERE id=".intval($category));
									$sec = Io::Output($row['section'],"int");
									if ($sec!==$section) $section = $sec;
								}

								$errors = array();
								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
								if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

								if (!sizeof($errors)) {
									//Upload
									include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
									$Up = new UploadImg();
									$Up->path = "assets/downloads/categories/";
									$Up->field = "image";
									$Up->max_size = $Router->GetOption("cat_size",512000);
									$Up->max_w = $Router->GetOption("cat_width",225);
									$Up->max_h = $Router->GetOption("cat_height",225);
									$Up->resize_w = $Router->GetOption("cat_resize_width",225);
									$Up->resize_h = $Router->GetOption("cat_resize_height",225);
									if (!$image = $Up->Upload()) $image = "";
								}

								if (!sizeof($errors)) {
									$Db->Query("INSERT INTO #__downloads_categories (section,parent,title,name,image,description)
												VALUES ('".intval($section)."','".intval($category)."','".$Db->_e($title)."',
														'".$Db->_e($name)."','".$Db->_e($image)."','".$Db->_e($description)."')");

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

	function EditDownloadsCategory() {
		global $Db,$User,$config_sys,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("editor"=>true));
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=add' title='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("ADD_NEW_X",MB::strtolower(_t("FILE")))."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=sections' title='"._t("SECTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."sections.png' alt='"._t("SECTIONS")."' /></a>\n";
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
						if ($valrow = $Db->GetRow("SELECT * FROM #__downloads_categories WHERE id=".intval($id))) {

							if (!isset($_POST['save'])) {
								$form = new Form();
								$form->action = "admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$id";
								$form->enctype = "multipart/form-data";

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

								$select = array();
								$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
								foreach ($result as $row) {
									$sid = Io::Output($row['id'],"int");
									$stitle = Io::Output($row['title']);

									$select[Io::Output($row['title'])] = $sid;
								}

								//Section
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("SECTION"),
														"name"		=>"section",
														"selected"	=>Io::Output($valrow['section'],"int"),
														"values"	=>$select));

								$disabled = array("disabled");
								$select = array();
								$select[_t("MAIN_X",MB::strtolower(_t("CATEGORY")))] = 0;
								$result = $Db->GetList("SELECT id,title FROM #__downloads_sections ORDER by title");
								foreach ($result as $row) {
									$sid = Io::Output($row['id'],"int");
									$stitle = Io::Output($row['title']);

									if ($cresult = $Db->GetList("SELECT id,title FROM #__downloads_categories WHERE section=$sid AND parent=0 ORDER BY title")) {
										$select[Io::Output($row['title'])] = "disabled";
										foreach ($cresult as $crow) {
											$cid = Io::Output($crow['id'],"int");
											$select["&nbsp;&nbsp;&nbsp;&nbsp;".Io::Output($crow['title'])] = $cid;
										}
									}
								}

								//Parent category
								$form->AddElement(array("element"	=>"select",
														"label"		=>_t("PARENT_X",MB::strtolower(_t("CATEGORY"))),
														"name"		=>"category",
														"values"	=>$select,
														"selected"	=>Io::Output($valrow['parent'],"int"),
														"optdisabled"=>$disabled));

								$max_size = $Router->GetOption("cat_size",512000);
								$max_size /= 1024;
								$max_w = $Router->GetOption("cat_width",225);
								$max_h = $Router->GetOption("cat_height",225);

								//Image
								$form->AddElement(array("element"	=>"file",
														"label"		=>_t("IMAGE"),
														"name"		=>"image",
														"size"		=>30,
														"info"		=>_t("IMAGE_TYPE_INFO_X_Y",$max_size."Kb",$max_w."px x ".$max_h."px")));

								//Description
								$form->AddElement(array("element"	=>"textarea",
														"label"		=>_t("DESCRIPTION"),
														"name"		=>"description",
														"value"		=>Io::Output($valrow['description']),
														"height"	=>"200px",
														"class"		=>"simple"));

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
									$section = Io::GetVar('POST','section','int');
									$category = Io::GetVar('POST','category','int');
									$description = Io::GetVar('POST','description','fullhtml',false);

									if ($category>0) {
										//Fix section id
										$row = $Db->GetRow("SELECT section FROM #__downloads_categories WHERE id=".intval($category));
										$sec = Io::Output($row['section'],"int");
										if ($sec!==$section) $section = $sec;
									}


									$errors = array();
									if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
									if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

									if (!sizeof($errors)) {
										//Upload
										include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
										$Up = new UploadImg();
										$Up->path = "assets/downloads/categories/";
										$Up->field = "image";
										$Up->max_size = $Router->GetOption("cat_size",512000);
										$Up->max_w = $Router->GetOption("cat_width",225);
										$Up->max_h = $Router->GetOption("cat_height",225);
										$Up->resize_w = $Router->GetOption("cat_resize_width",225);
										$Up->resize_h = $Router->GetOption("cat_resize_height",225);
										if ($image = $Up->Upload()) {
											//Delete previous data/files if necessary
											@unlink("assets/downloads/categories/".Io::Output($valrow['image']));
										} else if (!$Up->Selected()) {
											$image = Io::Output($valrow['image']);
										} else {
											$image = "";
										}
									}

									if (!sizeof($errors)) {
										$Db->Query("UPDATE #__downloads_categories SET title='".$Db->_e($title)."',
																					  name='".$Db->_e($name)."',
																					  section='".intval($section)."',
																					  parent='".intval($category)."',
																					  image='".$Db->_e($image)."',
																					  description='".$Db->_e($description)."' WHERE id=".intval($id));

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