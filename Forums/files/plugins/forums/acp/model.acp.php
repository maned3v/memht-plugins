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
defined("_ADMINCP") or die("Access denied");

class forumsModel {
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
			$(document).ready(function() {
				//Create
				$('input#create').click(function() {
					window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=newforum';
				});

				//Delete permanently
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X_AND_CONTENT",MB::strtolower(_t("FORUM"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "xml",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=deleteforum",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=forums';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("FORUM"))); ?>');
					}
				});
			});
		</script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
        <?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
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

        echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
	        //Create forum
	        echo "<input type='button' name='create' value='"._t("CREATE_NEW_X",MB::strtolower(_t("FORUM")))."' style='margin:2px 0;' class='sys_form_button' id='create' />\n";
        echo "</div>\n";
        echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
	        //Delete permanently
	        echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
        echo "</div>\n";

        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";

        $modroles = Ram::Get("roles");
        $modroles['ALL']['name'] = _t("EVERYONE");

        if ($result = $Db->GetList("SELECT * FROM #__forums_categories ORDER BY position")) {
            foreach ($result as $row) {
                $id     = Io::Output($row['id'],'int');
                $title  = Io::Output($row['title']);
                $name   = Io::Output($row['name']);

                echo "<thead>\n";
                    echo "<tr>\n";
                        echo "<th colspan='6'>$title</th>\n";
                    echo "</tr>\n";
                    echo "<tr>\n";
	                    echo "<th width='1%'>&nbsp;</th>\n";
                        echo "<th>"._t("TITLE")."</th>\n";
                        echo "<th>"._t("NAME")."</th>\n";
	                    echo "<th>&nbsp;</th>\n";
	                    echo "<th style='text-align:center;'>"._t("POSITION")."</th>\n";
	                    echo "<th style='text-align:center;'>"._t("STATUS")."</th>\n";
                    echo "</tr>\n";
                echo "</thead>\n";

                if ($resultf = $Db->GetList("SELECT f.* FROM #__forums AS f WHERE f.category = '".intval($id)."' AND f.parent=0 ORDER BY f.position")) {
                    foreach ($resultf as $frow) {
                        $fid     = Io::Output($frow['id'],'int');
                        $ftitle  = Io::Output($frow['title']);
                        $fname   = Io::Output($frow['name']);
	                    $fposition = Io::Output($frow['position']);
	                    $fstatus = MB::ucfirst(Io::Output($frow['status']));

                        echo "<tbody>\n";
                            echo "<tr>\n";
	                            echo "<td><input type='checkbox' name='selected[]' value='$fid' class='cb' /></td>\n";
                                echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=editforum&amp;id=$fid' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FORUM")))."'><strong>$ftitle</strong></a></td>\n";
                                echo "<td>$fname</td>\n";
                                echo "<td style='text-align:center;'><a href='admin.php?cont="._PLUGIN."&amp;op=auth&amp;id=$fid' title='"._t("AUTHORIZATIONS")."'>"._t("AUTHORIZATIONS")."</a></td>\n";
		                        echo "<td style='text-align:center;'>$fposition</td>\n";
		                        echo "<td style='text-align:center;'>$fstatus</td>\n";
		                    echo "</tr>\n";
                        echo "</tbody>\n";

                       if ($resultsf = $Db->GetList("SELECT f.* FROM #__forums AS f WHERE f.category='".intval($id)."' AND f.parent='".intval($fid)."' ORDER BY f.position")) {
                            foreach ($resultsf as $sfrow) {
                                $sfid     = Io::Output($sfrow['id'],'int');
                                $sftitle  = Io::Output($sfrow['title']);
                                $sfname   = Io::Output($sfrow['name']);
	                            $sfposition = Io::Output($sfrow['position']);
	                            $sfstatus = MB::ucfirst(Io::Output($sfrow['status']));

                                echo "<tbody>\n";
                                    echo "<tr>\n";
	                                    echo "<td><input type='checkbox' name='selected[]' value='$sfid' class='cb' /></td>\n";
                                        echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='admin.php?cont="._PLUGIN."&amp;op=editforum&amp;id=$sfid' title='"._t("EDIT_THIS_X",MB::strtolower(_t("FORUM")))."'><strong>$sftitle</strong></a></td>\n";
                                        echo "<td>$sfname</td>\n";
                                        echo "<td style='text-align:center;'><a href='admin.php?cont="._PLUGIN."&amp;op=auth&amp;id=$sfid' title='"._t("AUTHORIZATIONS")."'>"._t("AUTHORIZATIONS")."</a></td>\n";
		                                echo "<td style='text-align:center;'>$sfposition</td>\n";
		                                echo "<td style='text-align:center;'>$sfstatus</td>\n";
                                    echo "</tr>\n";
                                echo "</tbody>\n";
                            }
                        }
                    }
                } else {
	                echo "<tbody>\n";
		                echo "<tr>\n";
		                    echo "<td colspan='6' style='text-align:center;'>"._t("NO_FORUMS_IN_CAT")."</td>\n";
		                echo "</tr>\n";
	                echo "</tbody>\n";
                }
            }
        }

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

	function ListCategories() {
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
					window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=newcategory';
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
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=deletecategory",
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
			?>
		</div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
			<tr>
				<td style="vertical-align:top;">
					<div class="widget ui-widget-content ui-corner-all">
						<div class="ui-widget-header"><?php echo _t("CATEGORIES"); ?></div>
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

							$modroles = Ram::Get("roles");
							$modroles['ALL']['name'] = _t("EVERYONE");

							echo "<thead>\n";
								echo "<tr>\n";
									echo "<th width='1%'>&nbsp;</th>\n";
									echo "<th>"._t("TITLE")."</th>\n";
									echo "<th>"._t("NAME")."</th>\n";
									echo "<th style='text-align:center;'>"._t("POSITION")."</th>\n";
									echo "<th style='text-align:center;'>"._t("STATUS")."</th>\n";
								echo "</tr>\n";
							echo "</thead>\n";

							if ($result = $Db->GetList("SELECT * FROM #__forums_categories ORDER BY position")) {
								foreach ($result as $row) {
									$id     = Io::Output($row['id'],'int');
									$title  = Io::Output($row['title']);
									$name   = Io::Output($row['name']);
									$position = Io::Output($row['position']);
									$status = MB::ucfirst($row['status']);

									echo "<tbody>\n";
										echo "<tr>\n";
										echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
											echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=editcategory&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("CATEGORY")))."'><strong>$title</strong></a></td>\n";
											echo "<td>$name</td>\n";
											echo "<td style='text-align:center;'>$position</td>\n";
											echo "<td style='text-align:center;'>$status</td>\n";
										echo "</tr>\n";
									echo "</tbody>\n";
								}
							}

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

	function NewCategories() {
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
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
			$form->action = "admin.php?cont="._PLUGIN."&amp;op=newcategory";

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

			//Description
			$form->AddElement(array("element"	=>"textarea",
			                        "label"		=>_t("DESCRIPTION"),
			                        "name"		=>"description",
			                        "height"	=>"200px",
			                        "class"		=>"simple"));

			//Status
			$form->AddElement(array("element"	=>"select",
			                        "label"		=>_t("STATUS"),
			                        "name"		=>"status",
			                        "values"	=>array(_t("ACTIVE")    => "active",
			                                            _t("INACTIVE")  => "inactive",
			                                            _t("LOCKED")    => "locked")));

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
				$status = Io::GetVar('POST','status','nohtml');

				$errors = array();
				if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
				if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

				if (!sizeof($errors)) {
					$row = $Db->GetRow("SELECT position FROM #__forums_categories ORDER BY position DESC LIMIT 1");
					$pos = Io::Output($row['position'],"int");
					$pos++;

					$Db->Query("INSERT INTO #__forums_categories (title,name,description,position,status)
												VALUES ('".$Db->_e($title)."','".$Db->_e($name)."','".$Db->_e($description)."',".intval($pos).",'".$Db->_e($status)."')");

					Utils::Redirect("admin.php?cont="._PLUGIN."&op=categories");
				} else {
					MemErr::Trigger("USERERROR",implode("<br />",$errors));
				}
			} else {
				MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
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

	function EditCategories() {
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
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
							if ($valrow = $Db->GetRow("SELECT * FROM #__forums_categories WHERE id=".intval($id))) {

								if (!isset($_POST['save'])) {
									$form = new Form();
									$form->action = "admin.php?cont="._PLUGIN."&amp;op=editcategory&amp;id=$id";

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

									//Description
									$form->AddElement(array("element"	=>"textarea",
									                        "label"		=>_t("DESCRIPTION"),
									                        "name"		=>"description",
									                        "value"		=>Io::Output($valrow['description']),
									                        "height"	=>"200px",
									                        "class"		=>"simple"));

									//Position
									$form->AddElement(array("element"	=>"text",
									                        "label"		=>_t("POSITION"),
									                        "width"		=>"100px",
									                        "value"		=>Io::Output($valrow['position']),
									                        "name"		=>"position",
									                        "id"		=>"position"));

									//Status
									$form->AddElement(array("element"	=>"select",
									                        "label"		=>_t("STATUS"),
									                        "name"		=>"status",
									                        "selected"	=>Io::Output($valrow['status']),
									                        "values"	=>array(_t("ACTIVE")    => "active",
									                                            _t("INACTIVE")  => "inactive",
									                                            _t("LOCKED")    => "locked")));

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
										$status = Io::GetVar('POST','status','nohtml');
										$position = Io::GetInt('POST','position');

										$errors = array();
										if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
										if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

										if (!sizeof($errors)) {
											$Db->Query("UPDATE #__forums_categories
                                                    SET title='".$Db->_e($title)."',name='".$Db->_e($name)."',description='".$Db->_e($description)."',position='".intval($position)."',status='".$Db->_e($status)."'
                                                    WHERE id=".intval($id));

											Utils::Redirect("admin.php?cont="._PLUGIN."&op=categories");
										} else {
											MemErr::Trigger("USERERROR",implode("<br />",$errors));
										}
									} else {
										MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
									}
								}
							} else {
								MemErr::Trigger("USERERROR",_t("X_NOT_FOUND",_t("CATEGORY")));
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

	function DeleteCategories() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$res = $Db->Query("DELETE FROM #__forums_categories WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();

		//Forums and posts
		$ids = array();
		if ($result = $Db->GetList("SELECT id FROM #__forums WHERE category IN (".$Db->_e($items).")")) {
			foreach ($result as $row) $ids[] = Io::Output($row['id']);

			$forumitems = implode(",",$ids);

			$Db->Query("DELETE FROM #__forums WHERE category IN (".$Db->_e($items).")");
			$Db->Query("DELETE FROM #__forums_posts WHERE forum IN (".$Db->_e($forumitems).")");
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<response>\n";
		$xml .= "<result>\n";
		$xml .= "<query>".$res."</query>\n";
		$xml .= "<rows>".$total."</rows>\n";
		$xml .= "</result>\n";
		$xml .= "</response>";
		return $xml;
	}

	function NewForums() {
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
				$("#parent").change(function () {
					var sel = $("#parent").val();
					if (sel==0) {
						$("#category").attr("disabled",false);
					} else {
						$("#category").attr("disabled",true);
					}
				});
			});
		</script>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
		<div style="text-align:right;">
			<?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
			?>
		</div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
		<tr>
		<td style="vertical-align:top;">
		<div class="widget ui-widget-content ui-corner-all">
		<div class="ui-widget-header"><?php echo _t("CREATE_NEW_X",MB::strtolower(_t("FORUM"))); ?></div>
		<div class="body">

		<?php

		if (!isset($_POST['create'])) {
			$form = new Form();
			$form->action = "admin.php?cont="._PLUGIN."&amp;op=newforum";

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

			//Forum
			$select = array();
			$select[_t("MAIN_X",MB::strtolower(_t("FORUM")))] = 0;
			$result = $Db->GetList("SELECT id,title FROM #__forums WHERE parent=0 ORDER BY title");
			foreach ($result as $row) {
				$select[Io::Output($row['title'])] = Io::Output($row['id'],"int");
			}
			$form->AddElement(array("element"	=>"select",
			                        "label"		=>_t("PARENT_X",MB::strtolower(_t("FORUM"))),
			                        "name"		=>"parent",
			                        "id"        =>"parent",
			                        "values"	=>$select));

			//Category
			$select = array();
			$result = $Db->GetList("SELECT id,title FROM #__forums_categories ORDER BY title");
			foreach ($result as $row) {
				$id = Io::Output($row['id'],"int");
				$select[Io::Output($row['title'])] = $id;

			}
			$form->AddElement(array("element"	=>"select",
			                        "label"		=>_t("CATEGORY"),
			                        "name"		=>"category",
									"id"        =>"category",
			                        "values"	=>$select));

			//Description
			$form->AddElement(array("element"	=>"textarea",
			                        "label"		=>_t("DESCRIPTION"),
			                        "name"		=>"description",
			                        "height"	=>"200px",
			                        "class"		=>"simple"));

			//Status
			$form->AddElement(array("element"	=>"select",
			                        "label"		=>_t("STATUS"),
			                        "name"		=>"status",
			                        "values"	=>array(_t("ACTIVE")    => "active",
			                                            _t("INACTIVE")  => "inactive",
			                                            _t("LOCKED")    => "locked")));

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
				$status = Io::GetVar('POST','status','nohtml');
				$parent = Io::GetInt('POST','parent');
				$category = Io::GetInt('POST','category');

				$errors = array();
				if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
				if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

				if (!sizeof($errors)) {
					if ($parent>0) {
						$row = $Db->GetRow("SELECT category FROM #__forums WHERE id='".intval($parent)."'");
						$category = Io::Output($row['category']);
					}

					$row = $Db->GetRow("SELECT position FROM #__forums WHERE category='".intval($category)."' ORDER BY position DESC LIMIT 1");
					$pos = Io::Output($row['position'],"int");
					$pos++;

					$Db->Query("INSERT INTO #__forums (title,name,description,parent,category,position,status)
								VALUES ('".$Db->_e($title)."','".$Db->_e($name)."','".$Db->_e($description)."',".intval($parent).",".intval($category).",".intval($pos).",'".$Db->_e($status)."')");

					$id = $Db->InsertId();

					Utils::Redirect("admin.php?cont="._PLUGIN."&op=auth&id=$id");
				} else {
					MemErr::Trigger("USERERROR",implode("<br />",$errors));
				}
			} else {
				MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
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

	function EditForums() {
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
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
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
							if ($valrow = $Db->GetRow("SELECT * FROM #__forums WHERE id=".intval($id))) {

								if (!isset($_POST['save'])) {
									$form = new Form();
									$form->action = "admin.php?cont="._PLUGIN."&amp;op=editforum&amp;id=$id";

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

									//Forum
									$disabled = array("disabled");
									$select = array();
									$select[_t("MAIN_X",MB::strtolower(_t("FORUM")))] = 0;
									$result = $Db->GetList("SELECT id,title FROM #__forums WHERE parent=0 ORDER BY title");
									foreach ($result as $row) {
										$select[Io::Output($row['title'])] = Io::Output($row['id'],"int");
									}
									$form->AddElement(array("element"	=>"select",
									                        "label"		=>_t("PARENT_X",MB::strtolower(_t("FORUM"))),
									                        "name"		=>"parent",
									                        "id"        =>"parent",
									                        "selected"	=>Io::Output($valrow['parent']),
									                        "values"	=>$select));

									//Category
									$disabled = array(0);
									$select = array();
									$result = $Db->GetList("SELECT id,title FROM #__forums_categories ORDER BY title");
									foreach ($result as $row) {
										$id = Io::Output($row['id'],"int");
										$select[Io::Output($row['title'])] = $id;

									}
									$form->AddElement(array("element"	=>"select",
									                        "label"		=>_t("CATEGORY"),
									                        "name"		=>"category",
									                        "id"        =>"category",
									                        "selected"	=>Io::Output($valrow['category']),
									                        "values"	=>$select));

									//Description
									$form->AddElement(array("element"	=>"textarea",
									                        "label"		=>_t("DESCRIPTION"),
									                        "name"		=>"description",
									                        "value"		=>Io::Output($valrow['description']),
									                        "height"	=>"200px",
									                        "class"		=>"simple"));

									//Position
									$form->AddElement(array("element"	=>"text",
									                        "label"		=>_t("POSITION"),
									                        "width"		=>"100px",
									                        "value"		=>Io::Output($valrow['position']),
									                        "name"		=>"position",
									                        "id"		=>"position"));

									//Status
									$form->AddElement(array("element"	=>"select",
									                        "label"		=>_t("STATUS"),
									                        "name"		=>"status",
									                        "selected"	=>Io::Output($valrow['status']),
									                        "values"	=>array(_t("ACTIVE")    => "active",
									                                            _t("INACTIVE")  => "inactive",
									                                            _t("LOCKED")    => "locked")));

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
										$status = Io::GetVar('POST','status','nohtml');
										$position = Io::GetInt('POST','position');
										$parent = Io::GetInt('POST','parent');
										$category = Io::GetInt('POST','category');

										$errors = array();
										if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
										if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));

										if (!sizeof($errors)) {
											$Db->Query("UPDATE #__forums
                                                        SET title='".$Db->_e($title)."',name='".$Db->_e($name)."',description='".$Db->_e($description)."',position='".intval($position)."',status='".$Db->_e($status)."',parent='".intval($parent)."',category='".intval($category)."'
                                                        WHERE id=".intval($id));

											Utils::Redirect("admin.php?cont="._PLUGIN."&op=forums");
										} else {
											MemErr::Trigger("USERERROR",implode("<br />",$errors));
										}
									} else {
										MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
									}
								}
							} else {
								MemErr::Trigger("USERERROR",_t("X_NOT_FOUND",_t("FORUM")));
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

	function DeleteForums() {
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$res = $Db->Query("DELETE FROM #__forums WHERE id IN (".$Db->_e($items).")");
		$total = $Db->AffectedRows();

		$Db->Query("DELETE FROM #__forums_posts WHERE forum IN (".$Db->_e($items).")");

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<response>\n";
		$xml .= "<result>\n";
		$xml .= "<query>".$res."</query>\n";
		$xml .= "<rows>".$total."</rows>\n";
		$xml .= "</result>\n";
		$xml .= "</response>";
		return $xml;
	}

    public function BrowseModerators() {
        global $Db,$config_sys,$preroles;

        //Load plugin language
        Language::LoadPluginFile(_PLUGIN_CONTROLLER);
        //Initialize and show site header
        Layout::Header();
        //Start buffering content
        Utils::StartBuffering();

        $id = Io::GetInt("GET","id","int");

        ?>

	    <script type="text/javascript" charset="utf-8">
		    $(document).ready(function() {
			    //Add
			    $('input#addmod').click(function() {
				    window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=addmod&id=<?php echo $id; ?>';
			    });
		    });
		    //Remove
		    function removemod(role) {
			    if (confirm('<?php echo _t("SURE_REMOVE_THE_X",MB::strtolower(_t("ROLE"))); ?>')) {
				    $.ajax({
					    type: "POST",
					    dataType: "xml",
					    url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=removemod&id=<?php echo $id; ?>&role="+role,
					    success: function(data){
						    location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=moderators&id=<?php echo $id; ?>';
					    }
				    });
			    }
		    }
	    </script>

        <div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
        <div style="text-align:right;">
            <?php
            echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
            echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
            echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
            ?>
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
            <tr>
                <td style="vertical-align:top;">
                    <div class="widget ui-widget-content ui-corner-all">
                        <div class="ui-widget-header"><?php echo _t("MODERATORS"); ?></div>
                        <div class="body">

                            <?php

                            echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
	                            //Add moderator
	                            echo "<input type='button' name='addmod' value='"._t("ADD_NEW_X",MB::strtolower(_t("MODERATOR")))."' style='margin:2px 0;' class='sys_form_button' id='addmod' />\n";
                            echo "</div>\n";

                            if ($row = $Db->GetRow("SELECT * FROM #__forums WHERE id='".intval($id)."'")) {
                                $title = Io::Output($row['title']);
                                $roles = Utils::Unserialize(Io::Output($row['roles_moderate']));

                                echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";

                                    echo "<thead>\n";
                                        echo "<tr>\n";
                                            echo "<th colspan='3'>$title</th>\n";
                                        echo "</tr>\n";
                                    echo "</thead>\n";

	                                $preroles = Ram::Get("roles");

									foreach ($roles as $role) {
										$rolename = (isset($preroles[$role]['name'])) ? $preroles[$role]['name'] : $role ;

	                                    echo "<tbody>\n";
		                                    echo "<tr>\n";
		                                        echo "<td>$rolename</td>\n";
		                                        echo "<td>$role</td>\n";
		                                        echo "<td><a onclick=\"removemod('{$role}');\" title='"._t("DELETE")."'>"._t("DELETE")."</a></td>\n";
		                                    echo "</tr>\n";
	                                    echo "</tbody>\n";
									}

                                echo "</table>\n";
                            } else {
                                MemErr::Trigger("USERERROR",_t("X_NOT_FOUND",_t("FORUM")));
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

	public function AddModerator() {
		global $Db,$config_sys,$preroles;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		$id = Io::GetInt("GET","id","int");

		?>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
		<div style="text-align:right;">
			<?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
			?>
		</div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
		<tr>
		<td style="vertical-align:top;">
			<div class="widget ui-widget-content ui-corner-all">
				<div class="ui-widget-header"><?php echo _t("ADD_NEW_X",MB::strtolower(_t("MODERATOR"))); ?></div>
					<div class="body">

						<?php

						if (!isset($_POST['add'])) {
							$form = new Form();
							$form->action = "admin.php?cont="._PLUGIN."&amp;op=addmod&id=$id";
							$form->enctype = "multipart/form-data";

							$form->Open();

							$select = array();
							$result = $Db->GetList("SELECT * FROM #__rba_roles ORDER BY title");
							foreach ($result as $row) {
								$label = Io::Output($row['label']);
								$title = Io::Output($row['title']);

								if (in_array($label,array("GUEST","REGISTERED"))) continue;

								$select[Io::Output($row['title'])] = $label;
							}

							//Role
							$form->AddElement(array("element"	=>"select",
							                        "label"		=>_t("ROLE"),
							                        "name"		=>"role",
							                        "values"	=>$select));

							//Add
							$form->AddElement(array("element"	=>"submit",
							                        "name"		=>"add",
							                        "inline"	=>true,
							                        "value"		=>_t("ADD")));

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
				$role = Io::GetVar('POST','role','nohtml');

				$errors = array();
				if (!sizeof($errors)) {
					if ($row = $Db->GetRow("SELECT roles_moderate FROM #__forums WHERE id='".intval($id)."'")) {
						$roles = Utils::Unserialize(Io::Output($row['roles_moderate']));

						if (is_array($roles) && !in_array($role,$roles)) {
							$roles = Utils::Serialize(array_merge($roles,array($role)));
							$result = $Db->Query("UPDATE #__forums SET roles_moderate='".$Db->_e($roles)."' WHERE id='".intval($id)."'");
						}
					}

					Utils::Redirect("admin.php?cont="._PLUGIN."&op=moderators&id=$id");
				} else {
					MemErr::Trigger("USERERROR",implode("<br />",$errors));
				}
			} else {
				MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
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

	public function RemoveModerator() {
		global $Db,$config_sys;

		$id = Io::GetInt("GET","id");
		$role = Io::GetVar("GET","role","nohtml");

		$result = 0;
		$total = 0;

		if ($row = $Db->GetRow("SELECT roles_moderate FROM #__forums WHERE id='".intval($id)."'")) {
			$roles = Utils::Unserialize(Io::Output($row['roles_moderate']));

			if (is_array($roles)) {
				$roles = Utils::Serialize(array_diff($roles,array($role)));

				$result = $Db->Query("UPDATE #__forums SET roles_moderate='".$Db->_e($roles)."' WHERE id='".intval($id)."'");
				$total = $Db->AffectedRows();
			}
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<response>\n";
		$xml .= "<result>\n";
		$xml .= "<query>".$result."</query>\n";
		$xml .= "<rows>".$total."</rows>\n";
		$xml .= "</result>\n";
		$xml .= "</response>";
		return $xml;
	}

	public function SetAuthorizations() {
		global $Db,$config_sys,$preroles;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		$id = Io::GetInt("GET","id","int");

		?>

		<style type="text/css">
			.dialog {
				width:200px;
				padding:20px 40px;
				background-color:#5ecd62;
				color: #FFF;
				font-size:18px;
				text-align:center;
				z-index:99999;
				position:fixed;
				top:50px;
				left:50px;
				border-radius: 5px;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				display:none;
			}
		</style>

		<script type="text/javascript" charset="utf-8">
			//SetAuth
			function toggleAuth(context,role) {
				if (context == "read" && role == "ALL") {
					$(".read").attr("checked",false);
				} else if (context == "read") {
					$(".all").attr("checked",false);
				}
				$(".dialog").fadeIn(100).fadeOut(5000);
				$.ajax({
					type: "POST",
					dataType: "xml",
					url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=setauth&id=<?php echo $id; ?>&context="+context,
					data: "role="+role,
					success: function(data){
						location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=auth&id=<?php echo $id; ?>';
					},
					error: function() {
						location = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=auth&id=<?php echo $id; ?>';
					}
				});
			}
		</script>

		<div class="dialog"><?php echo _t("LOADING"); ?></div>

		<div class="tpl_page_title"><a href="admin.php?cont=<?php echo _PLUGIN; ?>" title="<?php echo _PLUGIN_TITLE; ?>"><?php echo _PLUGIN_TITLE; ?></a></div>
		<div style="text-align:right;">
			<?php
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";
			echo "<a href='admin.php?cont="._PLUGIN."&amp;op=forums' title='"._t("FORUMS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."comments.png' alt='"._t("FORUMS")."' /></a>\n";
			echo "<a href='admin.php?cont=plugins&amp;op=options&amp;controller="._PLUGIN."' title='"._t("PLUGIN_OPTIONS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."configuration.png' alt='"._t("PLUGIN_OPTIONS")."' /></a>\n";
			?>
		</div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="">
			<tr>
				<td style="vertical-align:top;">
					<div class="widget ui-widget-content ui-corner-all">
						<div class="ui-widget-header"><?php echo _t("AUTHORIZATIONS"); ?></div>
						<div class="body">

							<?php

							if ($row = $Db->GetRow("SELECT * FROM #__forums WHERE id='".intval($id)."'")) {
								$title = Io::Output($row['title']);
								$read   = Utils::Unserialize(Io::Output($row['roles_read']));
								$write  = Utils::Unserialize(Io::Output($row['roles_write']));
								$mod    = Utils::Unserialize(Io::Output($row['roles_moderate']));

								echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";

								echo "<thead>\n";
									echo "<tr>\n";
										echo "<th colspan='4'>$title</th>\n";
									echo "</tr>\n";
									echo "<tr>\n";
										echo "<th>"._t("ROLE")."</th>\n";
										echo "<th style='text-align:center;'>"._t("CAN_READ")."</th>\n";
										echo "<th style='text-align:center;'>"._t("CAN_WRITE")."</th>\n";
										echo "<th style='text-align:center;'>"._t("CAN_MODERATE")."</th>\n";
									echo "</tr>\n";
								echo "</thead>\n";

								$cRead      = "";
								$cWrite     = "";
								$cModerate  = "";

								if (empty($read)) $cRead = "checked";

								echo "<tbody>\n";
									echo "<tr>\n";
										echo "<td><em>"._t("EVERYONE")." (ALL)</em></td>\n";
										//Read
										echo "<td style='text-align:center; width:20%;'><input type='checkbox' class='all' onclick=\"toggleAuth('read','ALL')\" $cRead /></td>\n";
										//Write
										echo "<td style='text-align:center; width:20%;'>&nbsp;</td>\n";
										//Moderate
										echo "<td style='text-align:center; width:20%;'>&nbsp;</td>\n";
									echo "</tr>\n";
								echo "</tbody>\n";

								$result = $Db->GetList("SELECT * FROM #__rba_roles ORDER BY title");
								foreach ($result as $row) {
									$title  = Io::Output($row['title']);
									$label  = Io::Output($row['label']);

									$cRead      = (in_array($label,$read)) ? "checked" : "" ;
									$cWrite     = (in_array($label,$write)) ? "checked" : "" ;
									$cModerate  = (in_array($label,$mod)) ? "checked" : "" ;

									if ($label == "GUEST") {
										$cWrite = "disabled style='display:none;'";
										$cModerate = "disabled style='display:none;'";
									}

									if ($label == "REGISTERED") {
										$cModerate = "disabled style='display:none;'";
									}

									echo "<tbody>\n";
										echo "<tr>\n";
											echo "<td".(($label=="ADMIN") ? " style='font-weight:bold;'" : "").">$title ($label)</td>\n";
											//Read
											echo "<td style='text-align:center; width:20%;'><input type='checkbox' class='read' onclick=\"toggleAuth('read','$label')\" $cRead /></td>\n";
											//Write
											echo "<td style='text-align:center; width:20%;'><input type='checkbox' onclick=\"toggleAuth('write','$label')\" $cWrite /></td>\n";
											//Moderate
											echo "<td style='text-align:center; width:20%;'><input type='checkbox' onclick=\"toggleAuth('moderate','$label')\" $cModerate /></td>\n";
										echo "</tr>\n";
									echo "</tbody>\n";
								}

								echo "</table>\n";
							} else {
								MemErr::Trigger("USERERROR",_t("X_NOT_FOUND",_t("FORUM")));
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

	public function SetAuthAsync() {
		global $Db,$config_sys;

		$id = Io::GetInt("GET","id");
		$context = Io::GetVar("GET","context","nohtml");
		$role = Io::GetVar("POST","role","nohtml");

		$result = 0;
		$total = 0;

		if (in_array($context,array("read","write","moderate"))) {
			if ($row = $Db->GetRow("SELECT * FROM #__forums WHERE id='".intval($id)."'")) {
				$read       = Utils::Unserialize(Io::Output($row['roles_read']));
				$write      = Utils::Unserialize(Io::Output($row['roles_write']));
				$moderate   = Utils::Unserialize(Io::Output($row['roles_moderate']));

				switch ($context) {
					case "read":
						$read = in_array($role,$read) ? array_diff($read,array($role)) : array_merge($read,array($role)) ;
						if ($role=="ALL") $read = array();
						break;
					case "write":
						$write = in_array($role,$write) ? array_diff($write,array($role)) : array_merge($write,array($role)) ;
						break;
					case "moderate":
						$moderate = in_array($role,$moderate) ? array_diff($moderate,array($role)) : array_merge($moderate,array($role)) ;
						break;
				}

				$read       = Utils::Serialize($read);
				$write      = Utils::Serialize($write);
				$moderate   = Utils::Serialize($moderate);

				$result = $Db->Query("UPDATE #__forums SET roles_read='".$Db->_e($read)."',roles_write='".$Db->_e($write)."',roles_moderate='".$Db->_e($moderate)."' WHERE id='".intval($id)."'");
				$total = $Db->AffectedRows();
			}
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml");

		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<response>\n";
		$xml .= "<result>\n";
		$xml .= "<query>".$result."</query>\n";
		$xml .= "<rows>".$total."</rows>\n";
		$xml .= "</result>\n";
		$xml .= "</response>";
		return $xml;
	}
}

?>