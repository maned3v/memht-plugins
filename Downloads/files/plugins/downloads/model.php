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

class downloadsModel extends Views {
	public function _index() {
		global $Db,$Router,$User,$config_sys;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array("rating"=>true));
		//Start buffering content
		Utils::StartBuffering();

		//Filter
		$where = array();

		//...by section
		$sec = Io::GetVar("GET","sec","[^a-zA-Z0-9\-]");
		if (!empty($sec)) $where[] = "s.name='".$Db->_e($sec)."'";

		//...by category
		$cat = Io::GetVar("GET","cat","[^a-zA-Z0-9\-]");
		if (!empty($cat)) $where[] = "c.name='".$Db->_e($cat)."'";

		$where[] = "f.status='active'";
		$where[] = "NOW() BETWEEN f.start AND f.end";

		//Build query
		$where = (sizeof($where)>0) ? " WHERE ".implode(" AND ",$where) : "" ;

		$op = Io::GetVar("GET","op","[^a-zA-Z0-9\-]");
		//Options
		if (empty($op) && empty($cat)) {
			$orderby = "f.created";
			$order = "DESC";
		} else {
			$orderby = $Router->GetOption("orderby","f.created");
			$order = $Router->GetOption("order","DESC");
		}
		
		$limit = (empty($op) && empty($cat)) ? $Router->GetOption("limithome",5) : $Router->GetOption("limit",10);
		$plcom = $Router->GetOption("comments",1);
		$glcom = $plcom && $config_sys['comments'];

		//Tags
		$tag = Io::GetVar("GET","tag","[^a-zA-Z0-9\-]");
		if (!empty($tag)) {
			$tag_q1 = " JOIN #__tags AS t";
			$tag_q2 = " AND t.controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND f.id=t.item AND t.name='".$Db->_e($tag)."'";
			$row = $Db->GetRow("SELECT title FROM #__tags WHERE name='".$Db->_e($tag)."'");
			$tagtitle = Io::Output($row['title']);
		} else {
			$tag_q1 = $tag_q2 = "";
		}

		//Pagination
		$page = Io::GetVar("GET","page","int",false,1);
		if ($page<=0) $page = 1;
		$from = ($page * $limit) - $limit;

		//Section
		$plugin_dir = array();
		$plugin_cat = array();
		if (!$Router->GetOption("catinpluginhomeonly",1) || (empty($op) && empty($cat)) ) {
			if ($result = $Db->GetList("SELECT * FROM #__downloads_sections ORDER BY title")) {
				foreach ($result as $row) {
					$sid	= Io::Output($row['id'],"int");
					$stitle	= Io::Output($row['title']);
					$sname	= Io::Output($row['name']);
					$simage	= Io::Output($row['image']);
					$sdesc	= Io::Output($row['description']);

					$plugin_dir[$sid]["head"]["head"] = array("title"	=> $stitle,
															  "name"	=> $sname,
															  "image"	=> $simage,
															  "desc"	=> $sdesc);

					//Category
					if ($result = $Db->GetList("SELECT * FROM #__downloads_categories WHERE section=".intval($sid)." AND parent=0 ORDER BY title")) {
						foreach ($result as $row) {
							$cid	= Io::Output($row['id'],"int");
							$ctitle	= Io::Output($row['title']);
							$cname	= Io::Output($row['name']);
							$cimage	= Io::Output($row['image']);
							$cdesc	= Io::Output($row['description']);

							$plugin_dir[$sid]["body"][$cid]["head"] = array("id"	=> $cid,
																			"title"	=> $ctitle,
																			"name"	=> $cname,
																			"image"	=> $cimage,
																			"desc"	=> $cdesc);

							//Sub-category
							if ($result = $Db->GetList("SELECT * FROM #__downloads_categories WHERE section=".intval($sid)." AND parent=".intval($cid)." ORDER BY title")) {
								foreach ($result as $row) {
									$scid	= Io::Output($row['id'],"int");
									$sctitle= Io::Output($row['title']);
									$scname	= Io::Output($row['name']);
									$scimage= Io::Output($row['image']);
									$scdesc	= Io::Output($row['description']);

									$plugin_dir[$sid]["body"][$cid]["body"][$scid]["head"] = array("id"	=> $scid,
																								   "title"	=> $sctitle,
																								   "name"	=> $scname,
																								   "image"	=> $scimage,
																								   "desc"	=> $scdesc);
								}
							} else {
								$plugin_dir[$sid]["body"][$cid]["body"] = array();
							}
						}
					} else {
						$plugin_dir[$sid]["body"] = array();
					}
				}
			} else {
				$plugin_dir[]["body"] = array();
			}
		} else if (!empty($cat)) {
			if ($row = $Db->GetRow("SELECT c.*,s.name AS sname,s.title AS stitle FROM #__downloads_categories AS c JOIN #__downloads_sections AS s ON c.section=s.id WHERE c.name='".$Db->_e($cat)."'")) {
				$cid	= Io::Output($row['id']);
				$ctitle	= Io::Output($row['title']);
				$cname	= Io::Output($row['name']);
				$cimage	= Io::Output($row['image']);
				$cdesc	= Io::Output($row['description']);
				$sname	= Io::Output($row['sname']);
				$stitle	= Io::Output($row['stitle']);

				$plugin_cat = array("title"	=> $ctitle,
									"name"	=> $cname,
									"image"	=> $cimage,
									"desc"	=> $cdesc,
									"sname"	=> $sname,
									"stitle"=> $stitle);
				
				//Sub-category
				if ($result = $Db->GetList("SELECT * FROM #__downloads_categories WHERE parent=".intval($cid)." ORDER BY title")) {
					foreach ($result as $row) {
						$scid	= Io::Output($row['id'],"int");
						$sctitle= Io::Output($row['title']);
						$scname	= Io::Output($row['name']);
						$scimage= Io::Output($row['image']);
						$scdesc	= Io::Output($row['description']);
				
						$plugin_cat["body"][$scid]["head"] = array("id"	=> $scid,
																   "title"	=> $sctitle,
																   "name"	=> $scname,
																   "image"	=> $scimage,
																   "desc"	=> $scdesc,
																   "sname"	=> $sname,
																   "stitle"	=> $stitle);
					}
				} else {
					$plugin_cat["body"] = array();
				}
			}
		}

		$plugin_index = array();
		$files = 0;
		if ($result = $Db->GetList("SELECT f.*,c.name AS cname, c.title AS ctitle, u.name AS author_name, s.name AS sname, s.title AS stitle,
									(SELECT ROUND(SUM(rate)/COUNT(id)) AS rating FROM #__ratings WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item=f.id) AS rating
									FROM #__downloads AS f FORCE INDEX(cs) JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s JOIN #__user AS u
									{$tag_q1} ON f.category=c.id AND s.id=c.section AND f.author=u.uid {$tag_q2}
									{$where}
									ORDER BY $orderby $order
									LIMIT ".intval($from).",".intval($limit))) {
									
			foreach ($result as $row) {
				$id			= Io::Output($row['id'],"int");
				$category	= Io::Output($row['category'],"int");
				$sname		= Io::Output($row['sname']);
				$stitle		= Io::Output($row['stitle']);
				$cname		= Io::Output($row['cname']);
				$ctitle		= Io::Output($row['ctitle']);
				$title		= Io::Output($row['title']);
				$name		= Io::Output($row['name']);
				$aid		= Io::Output($row['author'],"int");
				$author		= Io::Output($row['author_name']);
				$created_o	= Io::Output($row['created']);
				$created	= Time::Output($created_o);
				$external	= Io::Output($row['external'],"int");
				$image		= Io::Output($row['image'],"nohtml");
				$desc		= Io::Output($row['description']);
				$demo		= Io::Output($row['demo'],"nohtml");
				$license	= Io::Output($row['license'],"nohtml");
				$version	= Io::Output($row['version'],"nohtml");
				$size		= Utils::Bytes2Str(Io::Output($row['size'],"int"));
				$start		= Io::Output($row['start']);
				$end		= Io::Output($row['end']);
				$options	= Utils::Unserialize(Io::Output($row['options']));
				$usecomments= ($glcom && Io::Output($row['usecomments'],"int")==1) ? true : false ;
				$comments	= Io::Output($row['comments'],"int");
				$hits		= Io::Output($row['hits'],"int");
				$rating		= Io::Output($row['rating'],"int");
				if (empty($rating)) $rating = 0;
				
				//Extended fields
				$ext_author		= Io::Output($row['ext_author']);
				$ext_contact	= Io::Output($row['ext_contact']);
				$ext_memhtver	= Utils::Unserialize(Io::Output($row['ext_memhtver']));
				

				//Split creation date
				$cdate = explode(" ",$created_o);
				$cdate = explode("-",$cdate[0]);
				$cday = intval($cdate[2]);
				$cmonth = intval($cdate[1]);
				$cyear = $cdate[0];

				$plugin_index[] = array(
					//Base
					"id"			=> $id,
					"sname"			=> $sname,
					"stitle"		=> $stitle,
					"cname"			=> $cname,
					"ctitle"		=> $ctitle,
					"title"			=> $title,
					"name"			=> $name,
					"aid"			=> $aid,
					"author"		=> $author,
					"options"		=> $options,
					"description"	=> $desc,
					"created"		=> $created,
					"external"		=> $external,
					"image"			=> $image,
					"demo"			=> $demo,
					"license"		=> $license,
					"version"		=> $version,
					"size"			=> Utils::Bytes2str($size),
					"start"			=> $start,
					"end"			=> $end,
					"usecomments"	=> $usecomments,
					"comments"		=> $comments,
					"hits"			=> $hits,
					//Additional
					"year"			=> $cyear,
					"month"			=> $cmonth,
					"smonth"		=> Utils::NumToMonth($cmonth,true),
					"day"			=> $cday,
					"tags"			=> $Db->GetList("SELECT name,title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item=".intval($id)),
					"rating"		=> $rating,
					"control"		=> ($User->IsAdmin()) ? " &lt;Edit&gt;" : false ,
					"info"			=> _t("ADDED_IN_X_BY_Y_ON_Z",
										  "<a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname' title='".CleanTitleAtr($ctitle)."'>$ctitle</a>",
										  "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
										  "$created"),
					"_author"		=> "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
					//Extended fields
					"ext_author"	=> $ext_author,
					"ext_contact"	=> $ext_contact,
					"ext_memhtver"	=> $ext_memhtver
				);
			}	
		}
		
		if (!empty($plugin_index) && (!empty($op) || !empty($cat))) {
			$urlinc = "";
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
				
				$row = $Db->GetRow("SELECT COUNT(*) AS total FROM #__downloads WHERE category=".intval($category));
				$files = Io::Output($row['total'],"int");
			}

			if ($op!="related") {
				//Pagination
				include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
				$Pag = new Pagination();
				$Pag->page = $page;
				$Pag->limit = $limit;
				$Pag->tot = $files;
				if (_ISHOME==0) {
					$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;op=browse{$urlinc}&amp;page={PAGE}";
					$plugin_pagination = $Pag->Show();
				} else {
					$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;op=browse";
					$plugin_pagination = $Pag->Label();
				}
			} else {
				$plugin_pagination = "";
			}
		} else {
			$plugin_pagination = "";
		}

		//Output
		$this->plugin_index = $plugin_index;
		$this->plugin_dir = $plugin_dir;
		$this->plugin_cat = $plugin_cat;
		$this->plugin_pagination = $plugin_pagination;
		$this->Show("downloads".__FUNCTION__);

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

	public function _view($name) {
		global $Db,$Router,$config_sys,$User;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);

		if ($row = $Db->GetRow("SELECT f.*,c.name AS cname, c.title AS ctitle, u.name AS author_name, s.name AS sname, s.title AS stitle,
								(SELECT ROUND(SUM(rate)/COUNT(id)) AS rating FROM #__ratings WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item=f.id) AS rating
								FROM #__downloads AS f JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s JOIN #__user AS u
								ON f.category=c.id AND s.id=c.section AND f.author=u.uid
								WHERE f.name='".$Db->_e($name)."' AND f.status='active'")) {

			//Options
			$orrel = $Router->GetOption("related_order","RAND");
			switch ($orrel) {
				default:
				case "RAND":
					$orrel = "RAND()";
					break;
				case "ASC":
					$orrel = "f.id ASC";
					break;
				case "DESC":
					$orrel = "f.id DESC";
					break;
			}
			$lirel = $Router->GetOption("related_limit",5);
			$plcom = $Router->GetOption("comments",1);
			$glcom = ($plcom==1 && $config_sys['comments']==1) ? true : false ;

			$id			= Io::Output($row['id'],"int");
			$category	= Io::Output($row['category'],"int");
			$sname		= Io::Output($row['sname']);
			$stitle		= Io::Output($row['stitle']);
			$cname		= Io::Output($row['cname']);
			$ctitle		= Io::Output($row['ctitle']);
			$title		= Io::Output($row['title']);
			$name		= Io::Output($row['name']);
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
			$external	= Io::Output($row['external'],"int");
			$image		= Io::Output($row['image']);
			$desc		= Io::Output($row['description']);
			$size		= Io::Output($row['size'],"int");
			$version	= Io::Output($row['version']);
			$license	= Io::Output($row['license']);
			$demo		= Io::Output($row['demo']);
			$rating		= Io::Output($row['rating'],"int");
			if (empty($rating)) $rating = 0;
			
			//Extended fields
			$ext_author		= Io::Output($row['ext_author']);
			$ext_contact	= Io::Output($row['ext_contact']);
			$ext_memhtver	= Utils::Unserialize(Io::Output($row['ext_memhtver']));

			if ($User->CheckRole($roles) || $User->IsAdmin()) {
				//Increment hits
				$Db->Query("UPDATE #__downloads SET hits=hits+1 WHERE id=".intval($id));

				//Split creation date
				$cdate = explode(" ",$created_o);
				$cdate = explode("-",$cdate[0]);
				$day = intval($cdate[2]);
				$month = intval($cdate[1]);
				$year = $cdate[0];

				//Tags
				$tags = $Db->GetList("SELECT name,title FROM #__tags WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item=".intval($id));

				//Rating
				include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."rating.class.php");
				$Rate = new Rating();
				$Rate->plugin = _PLUGIN;
				$Rate->controller = _PLUGIN_CONTROLLER;
				$Rate->id = $id;
				$Rate->rank = $rating;
				$rating = $Rate->Show();

				$plugin_view = array(
					//Base
					"id"			=> $id,
					"sname"			=> $sname,
					"stitle"		=> $stitle,
					"cname"			=> $cname,
					"ctitle"		=> $ctitle,
					"title"			=> $title,
					"name"			=> $name,
					"aid"			=> $aid,
					"author"		=> $author,
					"options"		=> $options,
					"created"		=> $created,
					"start"			=> $start,
					"end"			=> $end,
					"usecomments"	=> $usecomments,
					"comments"		=> $comments,
					"hits"			=> $hits,
					//File details
					"external"		=> $external,
					"image"			=> $image,
					"description"	=> $desc,
					"size"			=> Utils::Bytes2str($size),
					"version"		=> $version,
					"license"		=> $license,
					"demo"			=> $demo,
					//Additional
					"year"			=> $year,
					"month"			=> $month,
					"smonth"		=> Utils::NumToMonth($month,true),
					"day"			=> $day,
					"tags"			=> $tags,
					"rating"		=> $rating,
					"control"		=> ($User->IsAdmin()) ? " &lt;Edit&gt;" : false ,
					"info"			=> _t("WRITTEN_IN_X_BY_Y_ON_Z",
										  "<a href='index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname' title='".CleanTitleAtr($ctitle)."'>$ctitle</a>",
										  "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
										  "$created"),
					"_author"		=> "<a href='index.php?"._NODE."=user&amp;op=info&amp;uid=$aid' title='".CleanTitleAtr($author)."'>$author</a>",
					//Extended fields
					"ext_author"	=> $ext_author,
					"ext_contact"	=> $ext_contact,
					"ext_memhtver"	=> $ext_memhtver
				);

				//Related
				$plugin_related = array();
				if ($Router->GetOption("related",1)==1) {
					$tagarr = array();
					foreach ($tags as $tag) { $tagarr[] = $tag['name']; }
					for ($i=0;$i<sizeof($tagarr);$i++) $tagarr[$i] = "t.name='".$Db->_e($tagarr[$i])."'";
					if (sizeof($tagarr)>0) {
						if ($result = $Db->GetList("SELECT f.title,f.name,t.title AS ttag,t.name AS ntag,c.name AS cname,s.name AS sname,YEAR(f.created) AS year,MONTH(f.created) AS month
													FROM #__downloads AS f JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s JOIN #__tags AS t
													ON c.section=s.id AND f.category=c.id AND f.id=t.item AND f.id!=".intval($id)." AND t.controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND (".implode(" OR ",$tagarr).")
													WHERE f.status='active'
													GROUP BY f.id
													ORDER BY $orrel
													LIMIT $lirel")) {
							foreach ($result as $row) {
								$plugin_related['data'][] = array(
									"title"	=> Io::Output($row['title']),
									"name"	=> Io::Output($row['name']),
									"url"	=> "index.php?"._NODE."="._PLUGIN."&amp;sec=".Io::Output($row['sname'])."&amp;cat=".Io::Output($row['cname'])."&amp;title=".Io::Output($row['name']),
									"ttag"	=> Io::Output($row['ttag']),
									"ntag"	=> Io::Output($row['ntag'])
								);
							}
						}
					}
					$plugin_related['info']['status'] = "active";
					$plugin_related['info']['related'] = _t("RELATED_X",MB::strtolower(_t("FILES")));
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
				Layout::Header(array("rating"=>true,"comments"=>true));
				//Start buffering content
				Utils::StartBuffering();

				//Output
				$this->pv = $plugin_view;
				$this->pr = $plugin_related;
				$this->pc = $ComResult;
				$this->plugin_pagination = $plugin_pagination;
				$this->Show("downloads".__FUNCTION__);

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
				
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_ACCESS_X",MB::strtolower(_t("FILE"))));
			}
		} else {
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();

			MemErr::Trigger("INFO",_t("X_NOT_FOUND_OR_INACTIVE",_t("FILE")));
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

	public function DownloadFile() {
		global $Db,$config_sys,$User;

		$download = false;
		$message = _t("FILE_NOT_FOUND");
		
		$id = Io::GetVar("GET","id","[^a-zA-Z0-9\-]");
		if ($row = $Db->GetRow("SELECT * FROM #__downloads WHERE id='".intval($id)."'")) {
			$external = Io::Output($row['external'],"int");
			$file = Io::Output($row['file']);
			$ext = Io::Output($row['ext']);
			$file = (!$external) ? "assets"._DS."downloads"._DS."files"._DS.$config_sys['files_path']._DS.$file.".zip" : $file ;
			$name = Io::Output($row['title']);
			$size = Io::Output($row['size'],"int");
			$roles = Utils::Unserialize(Io::Output($row['roles']));

			//TODO: if (file_exists($file)) $download = true;
			$download = true;
			if (!$User->CheckRole($roles) && !$User->IsAdmin()) {
				$download = false;
				$message = _t("NOT_AUTH_TO_ACCESS_X",MB::strtolower(_t("FILE")));
			}
		}
		
		if ($download===true) {
			$Db->Query("UPDATE #__downloads SET downloads=downloads+1 WHERE id='".intval($id)."'");

			if (!$external) {
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false);
				header("Content-Type: application/force-download");
				header("Content-Disposition: attachment; filename=\"$name.$ext\";" );
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: $size");
				readfile($file) or die(_t("FILE_NOT_FOUND"));
				exit();
				die();
			} else {
				Utils::Redirect($file);
			}
		} else {
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();

			MemErr::Trigger("USERERROR",$message);

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

	public function _rss() {
		global $Db,$Router,$config_sys;

		$limit = $Router->GetOption("rss_limit",10);
		$sec = Io::GetVar("GET","sec","[^a-zA-Z0-9\-]");
		$cat = Io::GetVar("GET","cat","[^a-zA-Z0-9\-]");

		$where = array();
		if (!empty($sec))	$where[] = "s.name='".$Db->_e($sec)."'";
		if (!empty($cat))	$where[] = "c.name='".$Db->_e($cat)."'";
		$where = (sizeof($where)) ? "AND ".implode(" AND ",$where) : "" ;

		$ctitle = "";
		if ($result = $Db->GetList("SELECT f.title,f.name,f.created,u.name AS aname,u.email AS aemail,c.name AS cname,c.title AS ctitle,s.name AS sname,s.title AS stitle,f.created,f.description FROM #__downloads AS f JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s JOIN #__user AS u ON c.id=f.category AND f.author=u.uid AND s.id=c.section WHERE f.status='active'{$where} ORDER BY f.created DESC LIMIT ".intval($limit))) {
			if (!empty($cat)) $ctitle .= Io::Output($result[0]['ctitle'])." | ";
			if (!empty($sec)) $ctitle .= Io::Output($result[0]['stitle'])." | ";
		}

		include_once(_PATH_LIBRARIES._DS."MemHT"._DS."rss.class.php");
		$Rss = new Rss();
		$Rss->Channel(array("title"			=> $ctitle._PLUGIN_TITLE." | ".$config_sys['site_name'],
							"link"			=> $config_sys['site_url'],
							"description"	=> $config_sys['meta_description'],
							"language"		=> str_replace("_","-",_t("LANGID")),
							"copyright"		=> $config_sys['copyright'],
							"generator"		=> "MemHT Portal - Free PHP CMS and Blog",
							"lastbuilddate"	=> (_GMT_DATETIME." ".str_replace(":","",preg_replace("#([+|-])([0-9]+)#is","$1:0:$2",$config_sys['dbserver_timezone'])))));

		foreach ($result as $row) {
			$title = Io::Output($row['title']);
			$name = Io::Output($row['name']);
			$aemail = Io::Output($row['aemail']);
			$aname = Io::Output($row['aname']);
			$sname = Io::Output($row['sname']);
			$stitle = Io::Output($row['stitle']);
			$cname = Io::Output($row['cname']);
			$ctitle = Io::Output($row['ctitle']);
			$description = Io::Output($row['description']);
			$created = Io::Output($row['created']);

			//Split creation date
			$cdate = explode(" ",$created);
			$cdate = explode("-",$cdate[0]);
			$month = intval($cdate[1]);
			$year = $cdate[0];

			$Rss->Item(array("title"		=> $title,
							 "link"			=> RewriteUrl($config_sys['site_url']."/index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname&amp;title=$name"),
							 "permalink"	=> RewriteUrl($config_sys['site_url']."/index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname&amp;title=$name"),
							 "comments"		=> RewriteUrl($config_sys['site_url']."/index.php?"._NODE."="._PLUGIN."&amp;sec=$sname&amp;cat=$cname&amp;title=$name")."#comments",
							 "description"	=> $description,
							 "author"		=> "$aemail ($aname)",
							 "category"		=> $ctitle,
							 "pubdate"		=> (Time::Output($created,"D, j M Y H:i:s"," ",true)." ".str_replace(":","",preg_replace("#([+|-])([0-9]+)#is","$1:0:$2",$config_sys['dbserver_timezone'])))));
		}
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

			$row = $Db->GetRow("SELECT c.name AS category,s.name AS section,f.name AS file,f.created FROM #__downloads AS f JOIN #__downloads_categories AS c JOIN #__downloads_sections AS s ON f.category=c.id AND s.id=c.section WHERE f.id='".intval($item)."' AND f.status='active' LIMIT 1");
			$category = Io::Output($row['category']);
			$section = Io::Output($row['section']);
			$file = Io::Output($row['file']);
			$created = Io::Output($row['created']);

			//Split creation date
			$cdate = explode(" ",$created);
			$cdate = explode("-",$cdate[0]);
			$month = intval($cdate[1]);
			$year = $cdate[0];

			$Db->Query("INSERT INTO #__comments (id,controller,item,author,author_name,author_email,author_site,author_ip,created,text,status)
						VALUES (NULL,'".$Db->_e(_PLUGIN_CONTROLLER)."','".intval($item)."','".intval($User->Uid())."','".$Db->_e($author_name)."','".$Db->_e($author_email)."',
						'".$Db->_e($url)."','".$Db->_e(Utils::Ip2num($User->Ip()))."',NOW(),'".$Db->_e($message)."','".$Db->_e($status)."')");
			$insid = $Db->InsertId();
			//The counter will be increased when the PM will be published
			if ($modresult==0) $Db->Query("UPDATE #__downloads SET comments=comments+1 WHERE id='".intval($item)."'");

			$suffixa = ($modresult) ? "&compage=1&mod=$insid" : "" ;
			$suffixb = ($modresult) ? "#comments" : "#comment".$insid ;
			Utils::Redirect(RewriteUrl($config_sys['site_url']._DS."index.php?"._NODE."="._PLUGIN."&sec=$section&cat=$category&title=$file{$suffixa}").$suffixb);
		} else {
			//Load plugin language
			Language::LoadPluginFile(_PLUGIN_CONTROLLER);
			//Initialize and show site header
			Layout::Header();
			//Start buffering content
			Utils::StartBuffering();

			MemErr::Trigger("USERERROR",implode("<br />",$errors));

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
		if ($total) $result = $Db->Query("UPDATE #__downloads SET comments=comments-1 WHERE id=".intval($item)) ? 1 : 0 ;

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

	public function _rate() {
		global $Db,$User,$Visitor;

		if ($Visitor['request_method']!="POST") Utils::Redirect(RewriteUrl($config_sys['site_url']));

		$guests = Utils::GetComOption("rating","guests",0);
		if ($User->IsUser() || $guests) {
			$id = Io::GetVar("POST","id","int");
			$vote = Io::GetVar("POST","vote","int");

			if ($Db->GetRow("SELECT id FROM #__ratings WHERE controller='".$Db->_e(_PLUGIN_CONTROLLER)."' AND item='".intval($id)."' AND ((uid>0 AND uid='".intval($User->Uid())."') OR (uid=0 AND ip='".$Db->_e(Utils::Ip2num($Visitor['ip']))."')) LIMIT 1")) {
				echo _t("YOU_ALREADY_VOTED");
			} else {
				$Db->Query("INSERT INTO #__ratings (id,controller,item,rate,uid,ip)
							VALUES (null,'".$Db->_e(_PLUGIN_CONTROLLER)."','".intval($id)."','".$Db->_e($vote)."','".$Db->_e($User->Uid())."','".$Db->_e(Utils::Ip2num($Visitor['ip']))."')");
				echo _t("THANKS_FOR_VOTING");
			}
		} else {
			echo "<a href='"._SITEURL._DS."index.php?"._NODE."=user' title='"._t("LOGIN")."'>"._t("LOGIN")."</a>";
			echo " - <a href='"._SITEURL._DS."index.php?"._NODE."=user&amp;op=register' title='"._t("REGISTER")."'>"._t("REGISTER")."</a>";
		}
	}
}

?>