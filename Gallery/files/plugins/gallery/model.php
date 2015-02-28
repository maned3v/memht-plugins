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

class galleryModel extends Views {
	public function _index() {
		global $Db,$Router,$config_sys,$User;
		
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		//Section
		$plugin_sec = array();
		if ($result = $Db->GetList("SELECT * FROM #__gallery_sections ORDER BY title")) {
			foreach ($result as $row) {
				$id	= Io::Output($row['id'],"int");
				$title	= Io::Output($row['title']);
				$name	= Io::Output($row['name']);
				$file	= Io::Output($row['file']);
				$desc	= Io::Output($row['description']);
				
				$plugin_sec[] = array('title'	=> $title,
									  'name'	=> $name,
									  'file'	=> $file,
									  'desc'	=> $desc);
			}
		}
		
		//Output
		$this->plugin_opt = array();
		$this->plugin_index = array();
		$this->plugin_sec = $plugin_sec;
		$this->plugin_cat = array();
		$this->plugin_pagination = array();
		$this->Show("gallery".__FUNCTION__);
		
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
	
	public function _browsesec($sec) {
		global $Db,$Router,$config_sys,$User;
		
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		//Category
		$plugin_cat = array();
		if ($result = $Db->GetList("SELECT c.*,s.name AS sname,s.title AS stitle FROM #__gallery_categories AS c JOIN #__gallery_sections AS s ON c.section=s.id
									WHERE s.name='".$Db->_e($sec)."' ORDER BY c.title")) {
			foreach ($result as $row) {
				$id		= Io::Output($row['id'],"int");
				$title	= Io::Output($row['title']);
				$name	= Io::Output($row['name']);
				$sname	= Io::Output($row['sname']);
				$stitle	= Io::Output($row['stitle']);
				$file	= Io::Output($row['file']);
				$desc	= Io::Output($row['description']);
				
				$plugin_cat[] = array('title'	=> $title,
									  'name'	=> $name,
									  'sname'	=> $sname,
									  'stitle'	=> $stitle,
									  'file'	=> $file,
									  'desc'	=> $desc);
			}
			
			//Breadcrumb step
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname' title='".CleanTitleAtr($stitle)."'>$stitle</a></span>";
			//Site title step
			Utils::AddTitleStep($stitle);
		}
		
		//Output
		$this->plugin_opt = array();
		$this->plugin_index = array();
		$this->plugin_sec = array();
		$this->plugin_cat = $plugin_cat;
		$this->plugin_pagination = array();
		$this->Show("gallery_index");
		
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
	
	public function _browsecat($sec,$cat) {
		global $Db,$Router,$config_sys,$User;
		
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		//Filter
		$where = array();
		
		//...by section
		if (!empty($sec)) $where[] = "s.name='".$Db->_e($sec)."'";

		//...by category
		if (!empty($cat)) $where[] = "c.name='".$Db->_e($cat)."'";

		$where[] = "i.status='active'";
		$where[] = "NOW() BETWEEN i.start AND i.end";
		
		//Build query
		$where = (sizeof($where)>0) ? " WHERE ".implode(" AND ",$where) : "" ;
		
		//Options
		$order = $Router->GetOption("order","DESC");
		$limit = $Router->GetOption("limit",6);
		$plcom = $Router->GetOption("comments",1);
		$glcom = $plcom && $config_sys['comments'];
		
		//Tags
		$tag = Io::GetVar("GET","tag","[^a-zA-Z0-9\-]");
		if (!empty($tag)) {
			$tag_q1 = " JOIN #__tags AS t";
			$tag_q2 = " AND t.controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND i.id=t.item AND t.name='".$Db->_e($tag)."'";
			$row = $Db->GetRow("SELECT title FROM #__tags WHERE name='".$Db->_e($tag)."'");
			$tagtitle = Io::Output($row['title']);
		} else {
			$tag_q1 = $tag_q2 = "";
		}
		
		//Pagination
		$page = Io::GetVar("GET","page","int",false,1);
		if ($page<=0) $page = 1;
		$from = ($page * $limit) - $limit;
		
		$plugin_index = array();
		$images = 0;
		$urlinc = "";
		if ($result = $Db->GetList("SELECT i.*,c.title AS ctitle,s.title AS stitle,
									(SELECT COUNT(z.id) AS total FROM #__gallery AS z WHERE z.category=c.id) AS images
									FROM #__gallery AS i JOIN #__gallery_categories AS c JOIN #__gallery_sections AS s
									{$tag_q1} ON c.id=i.category AND c.section=s.id {$tag_q2}
									{$where}
									ORDER BY i.id $order
									LIMIT ".intval($from).",".intval($limit))) {
			foreach ($result as $row) {
				$roles = Utils::Unserialize(Io::Output($row['roles']));
				if ($User->CheckRole($roles) || $User->IsAdmin()) {
					$id			= Io::Output($row['id'],"int");
					$title		= Io::Output($row['title']);
					$name		= Io::Output($row['name']);
					$cname		= $cat;
					$ctitle		= Io::Output($row['ctitle']);
					$sname		= $sec;
					$stitle		= Io::Output($row['stitle']);
					$file		= Io::Output($row['file']);
					$thumb		= Io::Output($row['thumb']);
					$desc		= Io::Output($row['description']);
					$start		= Io::Output($row['start']);
					$end		= Io::Output($row['end']);
					$options	= Utils::Unserialize(Io::Output($row['options']));
					$usecomments= ($glcom && Io::Output($row['usecomments'],"int")==1) ? true : false ;
					$comments	= Io::Output($row['comments'],"int");
					$hits		= Io::Output($row['hits'],"int");
					$images		= max(Io::Output($row['images'],"int"),$images);
					
					$plugin_index[] = array('id'		=> $id,
											'title'		=> $title,
											'name'		=> $name,
											'sname'		=> $sname,
											'stitle'	=> $stitle,
											'cname'		=> $cname,
											'ctitle'	=> $ctitle,
											'file'		=> $file,
											'thumb'		=> $thumb,
											'desc'		=> $desc,
											'start'		=> $start,
											'end'		=> $end,
											'usecomments'=> $usecomments,
											'comments'	=> $comments,
											'hits'		=> $hits);
				}
			}
			
			if (!empty($sec)) {
				//Breadcrumb step
				$urlinc .= "&amp;sec=$sname";
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN.$urlinc."' title='".CleanTitleAtr($stitle)."'>$stitle</a></span>";
				//Site title step
				Utils::AddTitleStep($stitle);
			}
			if (!empty($cat)) {
				//Breadcrumb step
				$urlinc .= "&amp;cat=$cname";
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN.$urlinc."' title='".CleanTitleAtr($ctitle)."'>$ctitle</a></span>";
				//Site title step
				Utils::AddTitleStep($ctitle);
			}
		}
		
		//Pagination
		include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
		$Pag = new Pagination();
		$Pag->page = $page;
		$Pag->limit = $limit;
		$Pag->tot = $images;
		$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;op=browse{$urlinc}&amp;page={PAGE}";
		$plugin_pagination = $Pag->Show();
		
		//Options
		$plugin_options = array();
		$plugin_options['lightbox'] = $Router->GetOption("lightbox",1);
		
		//Output
		$this->plugin_opt = $plugin_options;
		$this->plugin_index = $plugin_index;
		$this->plugin_sec = array();
		$this->plugin_cat = array();
		$this->plugin_pagination = $plugin_pagination;
		$this->Show("gallery_index");
		
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
	
	function _view($name) {
		global $Db,$Router,$User,$config_sys;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);

		//Options
		$orrel = $Router->GetOption("related_order","RAND");
		switch ($orrel) {
			default:
			case "RAND":
				$orrel = "RAND()";
				break;
			case "ASC":
				$orrel = "a.id ASC";
				break;
			case "DESC":
				$orrel = "a.id DESC";
				break;
		}
		$lirel = $Router->GetOption("related_limit",5);
		$plcom = $Router->GetOption("comments",1);
		$glcom = ($plcom==1 && $config_sys['comments']==1) ? true : false ;
		
		if ($row = $Db->GetRow("SELECT i.*,c.name AS cname, c.title AS ctitle, u.name AS author_name, s.name AS sname, s.title AS stitle
								FROM #__gallery AS i JOIN #__gallery_categories AS c JOIN #__gallery_sections AS s JOIN #__user AS u
								ON i.category=c.id AND s.id=c.section AND i.author=u.uid
								WHERE i.name='".$Db->_e($name)."' AND i.status='active'")) {
			$id			= Io::Output($row['id'],"int");
			$title		= Io::Output($row['title']);
			$name		= Io::Output($row['name']);
			$cname		= Io::Output($row['cname']);
			$ctitle		= Io::Output($row['ctitle']);
			$sname		= Io::Output($row['sname']);
			$stitle		= Io::Output($row['stitle']);
			$file		= Io::Output($row['file']);
			$thumb		= Io::Output($row['thumb']);
			$desc		= Io::Output($row['description']);
			$aid		= Io::Output($row['author'],"int");
			$author		= Io::Output($row['author_name']);
			$created_o	= Io::Output($row['created']);
			$created	= Time::Output($created_o);
			$start		= Io::Output($row['start']);
			$end		= Io::Output($row['end']);
			$options	= Utils::Unserialize(Io::Output($row['options']));
			$roles		= Utils::Unserialize(Io::Output($row['roles']));
			$usecomments= ($glcom && Io::Output($row['usecomments'],"int")==1) ? true : false ;
			$comments	= Io::Output($row['comments'],"int");
			$hits		= Io::Output($row['hits'],"int");			
			
			if ($User->CheckRole($roles) || $User->IsAdmin()) {
				//Increment hits
				$Db->Query("UPDATE #__gallery SET hits=hits+1 WHERE id=".intval($id));
	
				//Split creation date
				$cdate = explode(" ",$created_o);
				$cdate = explode("-",$cdate[0]);
				$day = intval($cdate[2]);
				$month = intval($cdate[1]);
				$year = $cdate[0];
	
				//Tags
				$tags = $Db->GetList("SELECT name,title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item=".intval($id));
					
				$plugin_view = array(
					//Base
					"id"			=> $id,
					"sname"			=> $sname,
					"stitle"		=> $stitle,
					"cname"			=> $cname,
					"ctitle"		=> $ctitle,
					"sname"			=> $sname,
					"stitle"		=> $stitle,
					"title"			=> $title,
					"name"			=> $name,
					"aid"			=> $aid,
					"author"		=> $author,
					"file"			=> $file,
					"thumb"			=> $thumb,
					"desc"			=> $desc,
					"options"		=> $options,
					"created"		=> $created,
					"start"			=> $start,
					"end"			=> $end,
					"usecomments"	=> $usecomments,
					"comments"		=> $comments,
					"hits"			=> $hits,
					//Additional
					"more"			=> false,
					"year"			=> $year,
					"month"			=> $month,
					"smonth"		=> Utils::NumToMonth($month,true),
					"day"			=> $day,
					"tags"			=> $tags,
					"control"		=> ($User->IsAdmin()) ? " &lt;Edit&gt;" : false ,
					"info"			=> _t("WRITTEN_IN_X_BY_Y_ON_Z",
										  "<a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname' title='".CleanTitleAtr($ctitle)."'>$ctitle</a>",
										  "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
										  "$created"),
					"_author"		=> "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>"
				);
					
				//Related
				$plugin_related = array();
				if ($Router->GetOption("related",1)==1) {
					$tagarr = array();
					foreach ($tags as $tag) { $tagarr[] = $tag['name']; }
					for ($i=0;$i<sizeof($tagarr);$i++) $tagarr[$i] = "t.name='".$Db->_e($tagarr[$i])."'";
					if (sizeof($tagarr)>0) {
						if ($result = $Db->GetList("SELECT i.title,i.name,t.title AS ttag,t.name AS ntag,c.name AS cname,s.name AS sname
													FROM #__gallery AS i JOIN #__gallery_categories AS c JOIN #__gallery_sections AS s JOIN #__tags AS t
													ON c.section=s.id AND i.category=c.id AND i.id=t.item AND i.id!=".intval($id)." AND t.controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND (".implode(" OR ",$tagarr).")
													WHERE i.status='active'
													GROUP BY i.id
													ORDER BY $orrel
													LIMIT $lirel")) {
							foreach ($result as $row) {
								$plugin_related['data'][] = array(
									"title"	=> Io::Output($row['title']),
									"name"	=> Io::Output($row['name']),
									"url"	=> "index.php?"._NODE."="._PLUGIN."&amp;sec=".Io::Output($row['sname'])."&amp;cat=".Io::Output($row['cname'])."&amp;year=".Io::Output($row['year'])."&amp;month=".Io::Output($row['month'],"int")."&amp;title=".Io::Output($row['name']),
									"ttag"	=> Io::Output($row['ttag']),
									"ntag"	=> Io::Output($row['ntag'])
								);
							}
						}
					}
					$plugin_related['info']['status'] = "active";
					$plugin_related['info']['related'] = _t("RELATED_X",MB::strtolower(_t("IMAGES")));
				} else {
					$plugin_related['info']['status'] = "inactive";
				}
	
				//Comments
				include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."comments.class.php");
				$Com = new Comments();
				$Com->info = array("active"		=> $usecomments,
								   "plugin"		=> _PLUGIN,
								   "controller"	=> _PLUGIN_CONTROLLER,
								   "item"		=> $id,
								   "numcom"		=> $comments,
								   "url"		=> "index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname&amp;title=$name");
				$ComResult = $Com->GetCode();
					
				//Pagination
				include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
				$Pag = new Pagination();
				$Pag->page = Io::GetVar("GET","compage","int",false,1);
				$Pag->limit = Utils::GetComOption("comments","limit",10);
				$Pag->tot = $comments;
				$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname&amp;title=$name&amp;compage={PAGE}#comments";
				$plugin_pagination = $Pag->Show();
					
				//Meta data
				if (isset($plugin_view['options']['meta'])) {
					$controller = Ram::Get('controller');
					if (isset($plugin_view['options']['meta']['desc'])) $controller['meta_description'] = $plugin_view['options']['meta']['desc'];
					if (isset($plugin_view['options']['meta']['key'])) $controller['meta_keywords'] = $plugin_view['options']['meta']['key'];
					Ram::Set('controller',$controller);
				}
					
				//Initialize and show site header
				Layout::Header(array("comments"=>true));
				//Start buffering content
				Utils::StartBuffering();

				//Options
				$plugin_options = array();
				$plugin_options['lightbox'] = $Router->GetOption("lightbox",1);
				
				//Output
				$this->po = $plugin_options;

				$this->pv = $plugin_view;
				$this->pr = $plugin_related;
				$this->pc = $ComResult;
				$this->plugin_pagination = $plugin_pagination;
				$this->Show("gallery".__FUNCTION__);
					
				//Site title step
				Utils::AddTitleStep($title);
	
				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname' title='".CleanTitleAtr($stitle)."'>$stitle</a></span>";
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname' title='".CleanTitleAtr($ctitle)."'>$ctitle</a></span>";
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'>$title</span>";
			} else {
				//Initialize and show site header
				Layout::Header();
				//Start buffering content
				Utils::StartBuffering();
				
				Error::Trigger("USERERROR",_t("NOT_AUTH_TO_ACCESS_X",MB::strtolower(_t("IMAGE"))));
			}
		} else {
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();
			
			Error::Trigger("INFO",_t("X_NOT_FOUND_OR_INACTIVE",_t("IMAGE")));
		}

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
	
	public function _comment() {
		global $Db,$User,$config_sys,$Visitor;
		
		if ($Visitor['request_method']!="POST") Utils::Redirect(RewriteUrl($config_sys['site_url']));
		
		$name = Io::GetVar("POST","name");
		$email = Io::GetVar("POST","email");
		$url = Io::GetVar("POST","url");
		$message = Io::GetVar("POST","message");
		$item = Io::GetVar("POST","item","int");
		
		$gucom = Utils::GetComOption("comments","guest_can",0);
		$macom = Utils::GetComOption("comments","moderate_always",1);
		$mscom = Utils::GetComOption("comments","moderate_onspam",1);
		$swcom = Utils::GetComOption("comments","spam_words",array("http","ftp","www","://","sex","porn","viagra","pharmacy","fuck"));
		if (!is_array($swcom)) $swcom = array("http","ftp","www","://","sex","porn","viagra","pharmacy","fuck");
		
		$errors = array();
		if (!Utils::CheckToken()) { $errors[] = _t("INVALID_TOKEN"); }
		if (empty($message)) { $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("MESSAGE")); }
		if (!Utils::ValidEmail($email) && !$User->IsUser()) { $errors[] = _t("THE_FIELD_X_IS_NOT_INVALID",_t("EMAIL")); }
		if (empty($name) && !$User->IsUser()) { $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("NAME")); }
		if (!$User->IsUser() && !$gucom) { $errors[] = _t("LOGIN_TO_WRITE_COMMENT"); }

		if (!sizeof($errors)) {
			$author_name = ($User->IsUser()) ? "" : $name ;
			$author_email = ($User->IsUser()) ? "" : $email ;

			//Moderation
			$modresult = 0;
			if ($mscom) foreach ($swcom as $i) $modresult += (@MB::substr_count(MB::strtoupper($message),MB::strtoupper($i))>0) ? 1 : 0 ;
			$modresult += ($macom) ? 1 : 0 ;
			$status = ($modresult && !$User->IsAdmin()) ? "waiting" : "published" ;
			
			$row = $Db->GetRow("SELECT c.name AS category,s.name AS section,g.name AS image,g.created FROM #__gallery AS g JOIN #__gallery_categories AS c JOIN #__gallery_sections AS s ON g.category=c.id AND s.id=c.section WHERE g.id='".intval($item)."' AND g.status='active' LIMIT 1");
			$category = Io::Output($row['category']);
			$section = Io::Output($row['section']);
			$image = Io::Output($row['image']);
			$created = Io::Output($row['created']);

			$Db->Query("INSERT INTO #__comments (id,controller,item,author,author_name,author_email,author_site,author_ip,created,text,status)
						VALUES (NULL,'".$Db->_e(_PLUGIN_CONTROLLER)."','".intval($item)."','".intval($User->Uid())."','".$Db->_e($author_name)."','".$Db->_e($author_email)."',
						'".$Db->_e($url)."','".$Db->_e(Utils::Ip2num($User->Ip()))."',NOW(),'".$Db->_e($message)."','".$Db->_e($status)."')");
			$insid = $Db->InsertId();
			//The counter will be increased when the PM will be published
			if ($modresult==0) $Db->Query("UPDATE #__gallery SET comments=comments+1 WHERE id='".intval($item)."'");

			$suffixa = ($modresult) ? "&compage=1&mod=$insid" : "" ;
			$suffixb = ($modresult) ? "#comments" : "#comment".$insid ;
			Utils::Redirect(RewriteUrl($config_sys['site_url']._DS."index.php?"._NODE."="._PLUGIN."&sec=$section&cat=$category&title=$image{$suffixa}").$suffixb);
		} else {
			//Load plugin language
			Language::LoadPluginFile(_PLUGIN_CONTROLLER);
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();

			Error::Trigger("USERERROR",implode("<br />",$errors));

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
	
	function _delcomment() {
		global $Db,$User;

		if (!$User->IsAdmin()) return;
		
		$id = Io::GetVar("POST","id","int");
		$item = Io::GetVar("POST","item","int");
		
		if ($id==0 || $item==0) return;
		
		$result = $Db->Query("DELETE FROM #__comments WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND id=".intval($id)) ? 1 : 0 ;
		$total = $Db->AffectedRows();
		if ($total) $result = $Db->Query("UPDATE #__gallery SET comments=comments-1 WHERE id=".intval($item)) ? 1 : 0 ;

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
		echo $xml;
	}
}

?>