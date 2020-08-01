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
defined("_ADMINCP") or die("Access denied");

class linksModel {
	function Main() {
		global $Db;
        //Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
        $this->Menu();
        ?>
        <script type="text/javascript" charset="utf-8">			
			$(document).ready(function() {				
    			//Create
    			$('input#create').click(function() {
    	            window.location.href = 'admin.php?cont=<?php echo _PLUGIN; ?>&op=createlink';
    		    });				
                //Delete permanently
				$('input#delete').click(function() {
					var obj = $('.cb:checkbox:checked');
					if (obj.length>0) {
						if (confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X",MB::strtolower(_t("LINKS"))); ?>')) {
							var items = new Array();
							for (var i=0;i<obj.length;i++) items[i] = obj[i].value;
							$.ajax({
								type: "POST",
								dataType: "xml",
								url: "admin.php?cont=<?php echo _PLUGIN; ?>&op=dellink",
								data: "items="+items,
								success: function(data){
									location = 'admin.php?cont=<?php echo _PLUGIN; ?>';
								}
							});
						}
					} else {
						alert('<?php echo _t("MUST_SELECT_AT_LEAST_ONE_X",MB::strtolower(_t("LINK"))); ?>');
					}
				});
                                                               
			});
		</script>
        <?php
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>\n";
		echo "<tr>\n";
		    echo "<td style='vertical-align:top;'>\n";            
                echo "<div class='widget ui-widget-content ui-corner-all'>\n";
                    echo "<div class='ui-widget-header'>"._t("MANAGE_LINKS")."</div>\n";
                	echo "<div class='body'>\n";                    
				        echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
					        echo "<input type='button' name='create' value='"._t("ADD_NEW_X",MB::strtolower(_t("LINK")))."' style='margin:2px 0;' class='sys_form_button' id='create' />\n";
				        echo "</div>\n";
					    echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
						    //Delete permanently
							echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";	
						echo "</div>\n";
                                                 
                        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
                            echo "<thead>\n";
                			echo "<tr>\n";
                				echo "<th width='1%' style='text-align:right;'><input type='checkbox' id='selectall' /></th>\n";
                                echo "<th width='30%'>"._t("TITLE")."</th>\n";
                                echo "<th width='30%'>"._t("URL")."</th>\n";
                                echo "<th width='23%'>"._t("CATEGORY")."</th>\n";
                                echo "<th width='8%'>"._t("HITS")."</th>\n";
                                echo "<th width='8%'>"._t("STATUS")."</th>\n";
                			echo "</tr>\n";
                			echo "</thead>\n";
                			echo "<tbody>\n";                                
			
							//Pagination
                            $limit = Io::GetVar("GET","limit","int",false,10);
							$page = Io::GetVar("GET","page","int",false,1);
							if ($page<=0) $page = 1;
                            $from = ($page * $limit) - $limit;
                            
                            if ($result = $Db->GetList("SELECT l.*,c.title AS ctitle FROM #__links AS l JOIN #__links_categories AS c ON l.category=c.id ORDER BY l.id DESC LIMIT ".intval($from).",".intval($limit)."")) {                                    
                                
                                $preroles = Ram::Get("roles");
							    $preroles['ALL']['name'] = _t("EVERYONE");
                                
                                foreach ($result as $row) {
                        			$id			= Io::Output($row['id'],"int");
                                    $title  	= Io::Output($row['title']);
                                    $url  	    = Io::Output($row['url']);
									$hits	    = Io::Output($row['hits'],"int");                                   
                                    $roles		= Utils::Unserialize(Io::Output($row['roles']));
                                    $status		= MB::ucfirst(Io::Output($row['status']));
                                    $category	= Io::Output($row['ctitle']);
                                    
                                    echo "<tr>\n";
                        				echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
                                        echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=editlink&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("LINK")))."'>$title</a></td>\n";                                                                                               
                                        echo "<td>$url</td>\n";
                                        echo "<td>$category</td>\n";
                                        echo "<td>$hits</td>\n";
                                        echo "<td>$status</td>\n";
                        			echo "</tr>\n";                                
                                }                              
                            } else {
							    echo "<tr>\n";
								    echo "<td colspan='6' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
                                echo "</tr>\n";
							}     
                            echo "</tbody>\n";                    
                        echo "</table>\n";
								include_once(_PATH_ACP_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
								$Pag = new Pagination();
								$Pag->page = $page;
								$Pag->limit = $limit;
								$Pag->query = "SELECT COUNT(id) AS tot FROM #__links";							
								$Pag->url = "admin.php?cont="._PLUGIN."&amp;page={PAGE}";							
								echo $Pag->Show();                        
                    echo "</div>\n";
                echo "</div>\n";            
            echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer(); 
	}

	function CreateLinks() {
		global $Db,$config_sys,$Router;
        //Initialize and show site header
		Layout::Header(array("editor"=>true));
		//Start buffering content
		Utils::StartBuffering();
        
        $this->Menu();
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
        <?php		
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>";
		echo "<tr>";      
            echo "<td style='vertical-align:top;'>";
            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("CREATE_NEW_X",MB::strtolower(_t("LINK")))."</div>";
                	echo "<div class='body'>";
                    
                        if (!isset($_POST['create'])) {
                            
                            $form = new Form();
                            $form->action = "admin.php?cont="._PLUGIN."&amp;op=createlink";
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


							//URL
							$form->AddElement(array("element"	=>"text",
													"label"		=>_t("URL"),
													"width"		=>"300px",
													"name"		=>"url",
                                                    "info"		=>"http://...",
													"id"		=>"url"));
							//Category
							$select = array();
							$result = $Db->GetList("SELECT id,title FROM #__links_categories ORDER BY title");
							foreach ($result as $row) $select[Io::Output($row['title'])] = intval($row['id']);
							$form->AddElement(array("element"	=>"select",
													"label"		=>_t("CATEGORY"),
													"name"		=>"category",
													"values"	=>$select));                                                    
                                                      
							//Image
							$form->AddElement(array("element"	=>"file",
													"label"		=>_t("IMAGE"),
													"name"		=>"image",
													"size"		=>30,
													"info"		=>_t("IMAGE_TYPE_INFO_X_Y","200Kb","225x225px")));

							//Description
							$form->AddElement(array("element"	=>"textarea",
													"label"		=>_t("DESCRIPTION"),
													"name"		=>"description",
													"height"	=>"200px",
													"class"		=>"simple"));                                                    
                                                    
                                                                      
                    echo "</div>";
                echo "</div>";
            
            echo "</td>";
			echo "<td class='sidebar'>";
            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("OPTIONS")."</div>";
                	echo "<div class='body'>";
                        //Status
                        $form->AddElement(array("element"	=>"select",
                                                "label"		=>_t("STATUS"),
                                                "name"		=>"status",
                                                "values"	=>array(_t("ACTIVE")    => "active",
                                                                    _t("INACTIVE") 	=> "inactive")));                    
                    echo "</div>";
                echo "</div>";

                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("AUTHORIZATION_MANAGER")."</div>";
                	echo "<div class='body'>";
                        //Required roles
    					$result = $Db->GetList("SELECT title,label FROM #__rba_roles ORDER BY rid");
    					$rba = array();
    					$rba[_t("EVERYONE")] = "ALL";
    					foreach ($result as $row) $rba[Io::Output($row['title'])] = Io::Output($row['label']);
    					$form->AddElement(array("element"	=>"select",
    											"label"		=>_t("WHO_ACCESS_THE_X",MB::strtolower("PAGE")),
    											"name"		=>"roles[]",
    											"multiple"	=>true,
    											"values"	=>$rba,
    											"selected"	=>"ALL",
    											"info"		=>_t("MULTIPLE_CHOICES_ALLOWED")));  
						//Create
						$form->AddElement(array("element"	=>"submit",
												"name"		=>"create",
												"inline"	=>true,
												"value"		=>_t("CREATE")));
                                                
                       } else {

                            //Check token
							if (Utils::CheckToken()) {
								//Get POST data
								$title = Io::GetVar('POST','title','nohtml');
								$name = Io::GetVar('POST','name','[^a-zA-Z0-9\-]');
								$url = Io::GetVar('POST','url');
                                $category = Io::GetVar('POST','category','int');
                                $description = Io::GetVar('POST','description','fullhtml',false);
								$roles = Io::GetVar('POST','roles','nohtml',true,array());
                                $status = Io::GetVar('POST','status','nohtml');

								$errors = array();
								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
								if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));
								if (empty($url)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("URL"));
                                if (!Utils::ValidUrl($url)) $errors[] = _t("THE_FIELD_X_IS_NOT_INVALID",_t("URL"));
                                if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));

								if (!sizeof($errors)) {                                  
									//Upload
									include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
									$Up = new UploadImg();
									$Up->path = "assets/links/images/";
									$Up->field = "image";						
									$Up->max_size = $Router->GetOption("image_size",1024000);
									$Up->max_w = $Router->GetOption("image_width",225);
									$Up->max_h = $Router->GetOption("image_height",225);
									$Up->resize_w = $Router->GetOption("image_resize_width",120);
									$Up->resize_h = $Router->GetOption("image_resize_height",120);
									if (!$thumb = $Up->Upload()) $errors[] = implode(",",$Up->GetErrors());                                    
								}

								if (!sizeof($errors)) {

									if (in_array("ALL",$roles)) $roles = array();
									$roles = Utils::Serialize($roles);

									$Db->Query("INSERT INTO #__links (`category`,`title`,`name`,`url`,`description`,`image`,`status`,`roles`)
                                                VALUES ('".intval($category)."','".$Db->_e($title)."','".$Db->_e($name)."','".$Db->_e($url)."',
														'".$Db->_e($description)."','".$Db->_e($thumb)."',
														'".$Db->_e($status)."','".$Db->_e($roles)."')");

									Utils::Redirect("admin.php?cont="._PLUGIN);
								} else {
									MemErr::Trigger("USERERROR",implode("<br />",$errors));
								}
							} else {
								MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
							}
                       
                       }                                           
                    echo "</div>";
                echo "</div>";
            
            echo "</td>";
        echo "</tr>";
        echo "</table>";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer(); 	   
    }
	
    public function EditLinks() { 
		global $Db,$config_sys,$Router;
        //Initialize and show site header
		Layout::Header(array("editor"=>true));
		//Start buffering content
		Utils::StartBuffering();
        
        $this->Menu();
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
        <?php		
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>";
		echo "<tr>";      
            echo "<td style='vertical-align:top;'>";
            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("EDIT_X",MB::strtolower(_t("LINK")))."</div>";
                	echo "<div class='body'>";
					    
                        $id = Io::GetVar('GET','id','int');
						if ($row = $Db->GetRow("SELECT * FROM #__links WHERE id=".intval($id))) {                    
                        
                            if (!isset($_POST['edit'])) {
                                
                                $form = new Form();
                                $form->action = "admin.php?cont="._PLUGIN."&amp;op=editlink&amp;id=$id";
    							$form->enctype = "multipart/form-data";
                                $form->Open();
    							
                                //Title
    							$form->AddElement(array("element"	=>"text",
    													"label"		=>_t("TITLE"),
    													"width"		=>"300px",
    													"name"		=>"title",
                                                        "value"		=>Io::Output($row['title']),
    													"id"		=>"title"));
    														
    							//Name
    							$form->AddElement(array("element"	=>"text",
    													"label"		=>_t("LINK_NAME"),
    													"name"		=>"name",
    													"width"		=>"300px",
    													"id"		=>"urlvalidname",
                                                        "value"		=>Io::Output($row['name']),
    													"suffix"	=>"<input type='button' id='autoname' value='"._t("AUTO")."' class='sys_form_button' />",
    													"info"		=>_t("NUM_LOWCASE_LATIN_CHARS_DASH_ONLY")));                                                            
    
    
    							//URL
    							$form->AddElement(array("element"	=>"text",
    													"label"		=>_t("URL"),
    													"width"		=>"300px",
    													"name"		=>"url",
                                                        "value"		=>Io::Output($row['url']),
                                                        "info"		=>"http://...",
    													"id"		=>"url"));
    							//Category
    							$select = array();
    							$result = $Db->GetList("SELECT id,title FROM #__links_categories ORDER BY title");
    							foreach ($result as $row2) $select[Io::Output($row2['title'])] = intval($row2['id']);
    							$form->AddElement(array("element"	=>"select",
    													"label"		=>_t("CATEGORY"),
                                                        "selected"  =>Io::Output($row['category'],'int'),
    													"name"		=>"category",
    													"values"	=>$select));                                                    
                                                          
    							//Image
    							$form->AddElement(array("element"	=>"file",
    													"label"		=>_t("IMAGE"),
    													"name"		=>"image",
                                                        "value"		=>Io::Output($row['image']),
    													"size"		=>30,
    													"info"		=>_t("IMAGE_TYPE_INFO_X_Y","200Kb","225x225px")));
    
    							//Description
    							$form->AddElement(array("element"	=>"textarea",
    													"label"		=>_t("DESCRIPTION"),
    													"name"		=>"description",
                                                        "value"		=>Io::Output($row['description']),
    													"height"	=>"200px",
    													"class"		=>"simple"));                                                    
                                                        
                                                                          
                        echo "</div>";
                    echo "</div>";
                
                echo "</td>";
    			echo "<td class='sidebar'>";
                
                    echo "<div class='widget ui-widget-content ui-corner-all'>";
                        echo "<div class='ui-widget-header'>"._t("OPTIONS")."</div>";
                    	echo "<div class='body'>";
                            //Status
                            $form->AddElement(array("element"	=>"select",
                                                    "label"		=>_t("STATUS"),
                                                    "name"		=>"status",
                                                    "selected"  =>Io::Output($row['status']),
                                                    "values"	=>array(_t("ACTIVE")    => "active",
                                                                        _t("INACTIVE") 	=> "inactive")));                    
                        echo "</div>";
                    echo "</div>";
    
                    echo "<div class='widget ui-widget-content ui-corner-all'>";
                        echo "<div class='ui-widget-header'>"._t("AUTHORIZATION_MANAGER")."</div>";
                    	echo "<div class='body'>";
                            //Required roles
        					$result = $Db->GetList("SELECT title,label FROM #__rba_roles ORDER BY rid");
        					$rba = array();
        					$rba[_t("EVERYONE")] = "ALL";
        					$roles = Utils::Unserialize(Io::Output($row['roles']));
        					if (!sizeof($roles)|| empty($roles)) $roles = array('ALL');
                            foreach ($result as $roww) $rba[Io::Output($roww['title'])] = Io::Output($roww['label']);
        					$form->AddElement(array("element"	=>"select",
        											"label"		=>_t("WHO_ACCESS_THE_X",MB::strtolower("PAGE")),
        											"name"		=>"roles[]",
        											"multiple"	=>true,
        											"values"	=>$rba,
        											"selected"	=>$roles,
        											"info"		=>_t("MULTIPLE_CHOICES_ALLOWED")));  
    						//Create
    						$form->AddElement(array("element"	=>"submit",
    												"name"		=>"edit",
    												"inline"	=>true,
    												"value"		=>_t("SAVE")));
                                                    
                           } else {
    
                                //Check token
    							if (Utils::CheckToken()) {
    								//Get POST data
    								$title = Io::GetVar('POST','title','nohtml');
    								$name = Io::GetVar('POST','name','[^a-zA-Z0-9\-]');
    								$url = Io::GetVar('POST','url');
                                    $category = Io::GetVar('POST','category','int');
                                    $description = Io::GetVar('POST','description','fullhtml',false);
    								$roles = Io::GetVar('POST','roles','nohtml',true,array());
                                    $status = Io::GetVar('POST','status','nohtml');
    
    								$errors = array();
    								if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
    								if (empty($name)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("LINK_NAME"));
    								if (empty($url)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("URL"));
                                    if (!Utils::ValidUrl($url)) $errors[] = _t("THE_FIELD_X_IS_NOT_INVALID",_t("URL"));
                                    if (empty($category)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("CATEGORY"));
    
    								if (!sizeof($errors)) {                                  

										//Upload
										include_once(_PATH_LIBRARIES._DS."MemHT"._DS."upload.img.class.php");
										$Up = new UploadImg();
										$Up->path = "assets/links/images/";
										$Up->field = "image";
										$Up->max_size = $Router->GetOption("image_size",1024000);
										$Up->max_w = $Router->GetOption("image_width",225);
										$Up->max_h = $Router->GetOption("image_height",225);
										$Up->resize_w = $Router->GetOption("image_resize_width",120);
										$Up->resize_h = $Router->GetOption("image_resize_height",120);
										if ($image = $Up->Upload()) {
											//Delete previous data/files if necessary
											@unlink("assets/links/images/".Io::Output($row['image']));
										} else if (!$Up->Selected()) {
											$image = Io::Output($row['image']);
										} else {
											$errors[] = implode(",",$Up->GetErrors());
										}                                    
                                    
                                    }
    
    								if (!sizeof($errors)) {
    
    									if (in_array("ALL",$roles)) $roles = array();
    									$roles = Utils::Serialize($roles);
      
										$Db->Query("UPDATE #__links
                                                    SET category='".intval($category)."',title='".$Db->_e($title)."',name='".$Db->_e($name)."',url='".$Db->_e($url)."',description='".$Db->_e($description)."',image='".$Db->_e($image)."',status='".$Db->_e($status)."',roles='".$Db->_e($roles)."'
                                                    WHERE id=".intval($id));    
    
    									Utils::Redirect("admin.php?cont="._PLUGIN);
    								} else {
    									MemErr::Trigger("USERERROR",implode("<br />",$errors));
    								}
    							} else {
    								MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
    							}
                           
                           }
					} else {
						MemErr::Trigger("USERERROR",_t("X_NOT_FOUND",_t("LINK")));
					}                                                                  
                    echo "</div>";
                echo "</div>";
            
            echo "</td>";
        echo "</tr>";
        echo "</table>";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();         
    }
    
	public function DeleteLinks() { 
		global $Db,$config_sys;

		$items = Io::GetVar("POST","items",false,true);

		$result = $Db->GetList("SELECT image FROM #__links WHERE id IN (".$Db->_e($items).")");
		foreach ($result as $row) @unlink("assets/links/images/".Io::Output($row['image']));

		$res = $Db->Query("DELETE FROM #__links WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();

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
    
    function LinksCategories() {
		global $Db;
        //Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		$this->Menu();
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
        <?php
        echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>";
		echo "<tr>";
		    echo "<td style='vertical-align:top;'>";            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("MANAGE_CATEGORIES")."</div>";
                	echo "<div class='body'>";
                    
                    
                		echo "<div style='float:left; margin:6px 0 2px 0;'>\n";
                			//Create category
                			echo "<input type='button' name='create' value='"._t("CREATE_NEW_X",MB::strtolower(_t("CATEGORY")))."' style='margin:2px 0;' class='sys_form_button' id='create' />\n";
                		echo "</div>\n";
                		echo "<div style='text-align:right; padding:6px 0 2px 0; clear:right;'>\n";
                			//Delete permanently
                			echo "<input type='button' name='delete' value='"._t("DELETE_PERMANENTLY")."' style='margin:2px 0;' class='sys_form_button' id='delete' />\n";
                		echo "</div>\n";                    
                    
                        echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' summary='0' class='tgrid'>\n";
                			echo "<thead>\n";
                				echo "<tr>\n";
                					echo "<th width='1%' style='text-align:right;'><input type='checkbox' id='selectall' /></th>\n";
                					echo "<th width='45%'>"._t("TITLE")."</th>\n";
                					echo "<th width='45%'>"._t("NAME")."</th>\n";
                					echo "<th width='9%' style='text-align:right;'>"._t("LINKS")."</th>\n";
                				echo "</tr>\n";
                			echo "</thead>\n";
                			echo "<tbody>\n";
                								
                			if ($result = $Db->GetList("SELECT c.id,c.title,c.name,(SELECT COUNT(p.id) AS tot FROM #__links AS p WHERE p.category=c.id) AS links FROM #__links_categories AS c ORDER BY c.title")) {
                				foreach ($result as $row) {
                					$id		= Io::Output($row['id'],"int");
                					$title	= Io::Output($row['title']);
                					$name	= Io::Output($row['name']);
                					$links	= Io::Output($row['links'],"int");
                												
                					echo "<tr>\n";
                						echo "<td><input type='checkbox' name='selected[]' value='$id' class='cb' /></td>\n";
                						echo "<td><a href='admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$id' title='"._t("EDIT_THIS_X",MB::strtolower(_t("CATEGORY")))."'>$title</a></td>\n";
                						echo "<td>$name</td>\n";
                						echo "<td style='text-align:right;'>$links</td>\n";
                                    echo "</tr>\n";
                				}
                			} else {
                				echo "<tr>\n";
                					echo "<td colspan='4' style='text-align:center;'>"._t("LIST_EMPTY")."</td>\n";
                            	echo "</tr>\n";
                			}
                            echo "</tbody>";
                        echo "</table>";
                                
                    echo "</div>";
                echo "</div>";            
            echo "</td>";
        echo "</tr>";
        echo "</table>";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();         
    }
    
    function CreateLinksCategory() {
		global $Db,$config_sys;
        //Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
        $this->Menu();
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
        <?php
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>";
		echo "<tr>";
		    echo "<td style='vertical-align:top;'>";
            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("CREATE_NEW_X",MB::strtolower(_t("CATEGORY")))."</div>";
                	echo "<div class='body'>";

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
									$Db->Query("INSERT INTO #__links_categories (title,name)
												VALUES ('".$Db->_e($title)."','".$Db->_e($name)."')");
												
									Utils::Redirect("admin.php?cont="._PLUGIN."&op=categories");
								} else {
									MemErr::Trigger("USERERROR",implode("<br />",$errors));
								}
							} else {
								MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
							}
                        }
                    echo "</div>";
                echo "</div>";
            
            echo "</td>";
        echo "</tr>";
        echo "</table>";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();         
    }

    function EditLinksCategory() {
		global $Db,$config_sys;
        //Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
        $this->Menu();
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
        <?php
		echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' summary=''>";
		echo "<tr>";
		    echo "<td style='vertical-align:top;'>";
            
                echo "<div class='widget ui-widget-content ui-corner-all'>";
                    echo "<div class='ui-widget-header'>"._t("EDIT_X",MB::strtolower(_t("CATEGORY")))."</div>";
                	echo "<div class='body'>";

						$id = Io::GetVar('GET','id','int');
						if ($row = $Db->GetRow("SELECT * FROM #__links_categories WHERE id=".intval($id))) {
						
							if (!isset($_POST['save'])) {
									$form = new Form();
									$form->action = "admin.php?cont="._PLUGIN."&amp;op=editcat&amp;id=$id";
									
									$form->Open();
		
									//Title
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("TITLE"),
															"width"		=>"300px",
															"name"		=>"title",
															"id"		=>"title",
															"value"		=>Io::Output($row['title'])));
															
									//Name
									$form->AddElement(array("element"	=>"text",
															"label"		=>_t("LINK_NAME"),
															"name"		=>"name",
															"width"		=>"300px",
															"id"		=>"urlvalidname",
															"suffix"	=>"<input type='button' id='autoname' value='"._t("AUTO")."' class='sys_form_button' />",
															"info"		=>_t("NUM_LOWCASE_LATIN_CHARS_DASH_ONLY"),
															"value"		=>Io::Output($row['name'])));
	
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
											$Db->Query("UPDATE #__links_categories SET title='".$Db->_e($title)."',name='".$Db->_e($name)."' WHERE id=".intval($id));
											
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
                    echo "</div>";
                echo "</div>";
            
            echo "</td>";
        echo "</tr>";
        echo "</table>";  
		
		//Assign captured content to the template engine and clean buffer
		Template::AssignVar("sys_main",array("title"=>_PLUGIN_TITLE,"url"=>"admin.php?cont="._PLUGIN,"content"=>Utils::GetBufferContent("clean")));
		//Draw site template
		Template::Draw();
		//Initialize and show site footer
		Layout::Footer();          
    }
    
    function DeleteLinksCategory() {
		global $Db;

		$items = Io::GetVar("POST","items",false,true);

		$res = $Db->Query("DELETE FROM #__links_categories WHERE id IN (".$Db->_e($items).")") ? 1 : 0 ;
		$total = $Db->AffectedRows();
		
		$ids = array();
		if ($result = $Db->GetList("SELECT id,image FROM #__links WHERE category IN (".$Db->_e($items).")")) {
			foreach ($result as $row) {
			    @unlink("assets/links/images/".Io::Output($row['image']));
                $ids[] = Io::Output($row['id']); 
			} 
		}
		$items = implode(",",$ids);
		
		$Db->Query("DELETE FROM #__links WHERE id IN (".$Db->_e($items).")");
        

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
    
    function Menu() {
        global $config_sys;
                
        echo "<div class='tpl_page_title'><a href='admin.php?cont="._PLUGIN."' title='"._PLUGIN_TITLE."'>"._PLUGIN_TITLE."</a></div>";
        echo "<div style='text-align:right;'>";
			echo "<a href='admin.php?cont="._PLUGIN."' title='"._t("LINKS")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."page.png' alt='"._t("LINKS")."' /></a>\n";
            echo "<a href='admin.php?cont="._PLUGIN."&amp;op=createlink' title='"._t("CREATE_NEW_X",MB::strtolower(_t("LINK")))."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."create.png' alt='"._t("CREATE_NEW_X",MB::strtolower(_t("LINK")))."' /></a>\n";			
            echo "<a href='admin.php?cont="._PLUGIN."&amp;op=categories' title='"._t("CATEGORIES")."'><img src='admin"._DS."templates"._DS.$config_sys['admincp_template']._DS."buttons"._DS."category.png' alt='"._t("CATEGORIES")."' /></a>\n";			
        echo "</div>";        
    }
}

?>