<?php

//========================================================================
// MemHT Portal
// 
// Copyright (C) 2008-2011 by Miltenovikj Manojlo <dev@miltenovik.com>
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

class forumsModel extends Views {
	public function Main() {
		global $Db,$Router,$User;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		$plugin_forums = array();
		$limit = $Router->GetOption("posts_limit",10);
		
		if ($result = $Db->GetList("SELECT * FROM #__forums_categories ORDER BY position")) {
			foreach ($result as $row) {
				$cid			= Io::Output($row['id'],"int");
				$title			= Io::Output($row['title']);
				$name			= Io::Output($row['name']);
				$description	= Io::Output($row['description']);
				
				//Category
				$plugin_forums[$cid] = array("id"			=> $cid,
											 "title"		=> $title,
											 "name"			=> $name,
											 "description"	=> $description);
				
				$subs = array();
				
				if ($result_f = $Db->GetList("SELECT f.*,
											  (SELECT COUNT(*) FROM #__forums_posts WHERE forum=f.id AND parent=0) AS threads,
											  (SELECT COUNT(*) FROM #__forums_posts WHERE forum=f.id AND parent>0) AS posts
											  FROM #__forums AS f WHERE f.category='".intval($cid)."' ORDER BY f.parent,f.position")) {
					foreach ($result_f as $row_f) {
						$roles_read		= Utils::Unserialize(Io::Output($row_f['roles_read']));
						$roles_moderate	= Utils::Unserialize(Io::Output($row_f['roles_moderate']));
						$parent			= Io::Output($row_f['parent'],"int");
						
						if ($User->CheckRole($roles_read) || $User->CheckRole($roles_moderate) || $parent>0) {
							$id				= Io::Output($row_f['id'],"int");
							$title			= Io::Output($row_f['title']);
							$name			= Io::Output($row_f['name']);
							$description	= Io::Output($row_f['description']);
							$threads		= Io::Output($row_f['threads'],"int");
							$posts			= Io::Output($row_f['posts'],"int")+$threads;
							
							if ($parent>0) {
								//Subforum
								$subs[$parent]['subforums'][$id] = array("id"				=> $id,
																		 "parent"			=> $parent,
																		 "title"			=> $title,
																		 "name"				=> $name,
																		 "description"		=> $description,
																		 "threads"			=> $threads,
																		 "posts"			=> $posts);
							} else {
								//Forum
								$plugin_forums[$cid]['forums'][$id] = array("id"			=> $id,
																			"parent"		=> 0,
																			"title"			=> $title,
																			"name"			=> $name,
																			"description"	=> $description,
																			"threads"		=> $threads,
																			"posts"			=> $posts);
																										
								if ($row_l = $Db->GetRow("SELECT p.id,p.parent,p.title,p.author,p.created,u.name,
														  (SELECT COUNT(*) FROM #__forums_posts WHERE parent=p.parent AND id<=p.id) AS posts
														  FROM #__forums_posts AS p JOIN #__user AS u ON p.author=u.uid WHERE p.forum='".intval($id)."' ORDER BY p.id DESC LIMIT 1")) {
									$row_l['id']			= Io::Output($row_l['id'],"int");
									$row_l['parent']		= Io::Output($row_l['parent'],"int");
									$row_l['thread_id']		= ($row_l['parent']>0) ? $row_l['parent'] : $row_l['id'] ;
									$row_l['pagination']	= (intval($row_l['posts'])>$limit) ? "&amp;page=".ceil(intval($row_l['posts'])/$limit) : "" ;
									$row_l['title']			= Utils::CutStr(Io::Output($row_l['title']),20);
									$row_l['author']		= Io::Output($row_l['author'],"int");
									$row_l['created']		= Time::Output(Io::Output($row_l['created']));
									$row_l['author_name']	= Io::Output($row_l['name']);
									
									//Forum
									$plugin_forums[$cid]['forums'][$id] = array_merge($plugin_forums[$cid]['forums'][$id],array("lastpost" => $row_l));
								}
							}
						}
					}
					//Insert subforums
					if (isset($plugin_forums[$cid]['forums'])) {
						foreach ($plugin_forums[$cid]['forums'] as $fid => $fcont) {
							if (isset($subs[$fid])) $plugin_forums[$cid]['forums'][$fid]['subforums'] = $subs[$fid]['subforums'];
						}
					}
				}		
			}
		}
		
		//Output
		$this->plugin_data = array();
		$this->plugin_forums = $plugin_forums;
		$this->plugin_subforums = array();
		$this->plugin_threads = array();
		$this->plugin_pagination = array();
		$this->Show("forums_index");
		
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
	
	public function Threads() {
		global $Db,$User,$Router;
		
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		$plugin_data = array();
		$plugin_subforums = array();
		$plugin_threads = array();
		
		//Options
		$orderby = $Router->GetOption("threads_orderby","t.id");
		$order = $Router->GetOption("threads_order","DESC");
		$limit = $Router->GetOption("threads_limit",15);
		$postslimit = $Router->GetOption("posts_limit",10);
		
		//Filter
		$where = array();
		
		$f = Io::GetVar("GET","f","[^a-zA-Z0-9\-]");
		$where[] = "f.name='".$Db->_e($f)."'";
		$where[] = "t.parent=0";
		
		//Build query
		$where = (sizeof($where)>0) ? " WHERE ".implode(" AND ",$where) : "" ;
		
		//Pagination
		$page = Io::GetVar("GET","page","int",false,1);
		if ($page<=0) $page = 1;
		$from = ($page * $limit) - $limit;
		
		//Forum's data
		$row = $Db->GetRow("SELECT * FROM #__forums WHERE name='".$Db->_e($f)."'");
		$forum_title	= Io::Output($row['title']);
		$parent			= Io::Output($row['parent'],"int");
		$roles_read		= Utils::Unserialize(Io::Output($row['roles_read']));
		$roles_write	= Utils::Unserialize(Io::Output($row['roles_write']));
		$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
		if ($parent) {
			$rowp = $Db->GetRow("SELECT * FROM #__forums WHERE id='".intval($parent)."'");
			$parent_title	= Io::Output($rowp['title']);
			$parent_name	= Io::Output($rowp['name']);
			$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));
			
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
		}
		
		//Site title step
		Utils::AddTitleStep($forum_title);
		
		//Breadcrumbs path
		$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$f' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
		
		if ($User->CheckRole($roles_read) || $User->CheckRole($roles_moderate)) {
			//Subforums
			if ($result_f = $Db->GetList("SELECT f.name,f.id,sf.*,
										  (SELECT COUNT(*) FROM #__forums_posts WHERE forum=sf.id AND parent=0) AS threads,
										  (SELECT COUNT(*) FROM #__forums_posts WHERE forum=sf.id AND parent>0) AS posts
										  FROM #__forums AS f JOIN #__forums AS sf ON f.id=sf.parent WHERE f.name='".$Db->_e($f)."' ORDER BY position")) {
				foreach ($result_f as $row_f) {					
					$roles_read		= Utils::Unserialize(Io::Output($row_f['roles_read']));
			
					if ($User->CheckRole($roles_read)) {
						$id				= Io::Output($row_f['id'],"int");
						$title			= Io::Output($row_f['title']);
						$name			= Io::Output($row_f['name']);
						$description	= Io::Output($row_f['description']);
						$threads		= Io::Output($row_f['threads'],"int");
						$posts			= Io::Output($row_f['posts'],"int")+$threads;

						//Subforum
						$plugin_subforums[$id] = array("title"			=> $title,
													   "name"			=> $name,
													   "description"	=> $description,
													   "threads"		=> $threads,
													   "posts"			=> $posts);
						
						if ($row_l = $Db->GetRow("SELECT p.id,p.parent,p.title,p.author,p.created,u.name,
												  (SELECT COUNT(*) FROM #__forums_posts WHERE parent=p.parent AND id<=p.id) AS posts
												  FROM #__forums_posts AS p FORCE INDEX(cfs) JOIN #__user AS u ON p.author=u.uid WHERE p.forum='".intval($id)."' ORDER BY p.id DESC LIMIT 1")) {
							$row_l['id']			= Io::Output($row_l['id'],"int");
							$row_l['parent']		= Io::Output($row_l['parent'],"int");
							$row_l['thread_id']		= ($row_l['parent']>0) ? $row_l['parent'] : $row_l['id'] ;
							$row_l['pagination']	= (intval($row_l['posts'])>$limit) ? "&amp;page=".ceil(intval($row_l['posts'])/$limit) : "" ;
							$row_l['title']			= Utils::CutStr(Io::Output($row_l['title']),20);
							$row_l['author']		= Io::Output($row_l['author'],"int");
							$row_l['created']		= Time::Output(Io::Output($row_l['created']));
							$row_l['author_name']	= Io::Output($row_l['name']);
						
							//Forum
							$plugin_subforums[$id] = array_merge($plugin_subforums[$id],array("lastpost" => $row_l));
						}
					}
				}
			}
	
			//Threads
			if ($result = $Db->GetList("SELECT f.id,f.title AS forum_title,u.name AS author_name,
										(SELECT COUNT(*) AS tot FROM #__forums_posts WHERE parent=t.id) AS replies,
										t.*
										FROM #__forums_posts AS t FORCE INDEX(cfs) JOIN #__forums AS f JOIN #__user AS u
										ON t.forum=f.id AND t.author=u.uid
										{$where}
										ORDER BY t.flag DESC,".$Db->_e($orderby)." ".$Db->_e($order)."
										LIMIT ".intval($from).",".intval($limit))) {
				//Get userdata
				$lastpostread = 0;
				if ($udrow = $Db->GetRow("SELECT lastpostread FROM #__forums_userdata WHERE uid='".intval($User->Uid())."'")) {
					$lastpostread = Io::Output($udrow['lastpostread'],"int");
				}
				foreach ($result as $row) {
					$id				= Io::Output($row['id'],"int");
					$title			= Io::Output($row['title']);
					$forum			= $f;
					$flag			= Io::Output($row['flag'],"int");
					$author_id		= Io::Output($row['author'],"int");
					$author_name	= Io::Output($row['author_name']);
					$created		= Time::Output(Io::Output($row['created']));
					$hits			= Io::Output($row['hits'],"int");
					$replies		= Io::Output($row['replies'],"int");
			
					$plugin_threads[$id] = array("title"		=> $title,
												 "forum"		=> $forum,
												 "forum_title"	=> $forum_title,
												 "flag"			=> $flag,
												 "author_id"	=> $author_id,
												 "author_name"	=> $author_name,
												 "created"		=> $created,
												 "views"		=> $hits,
												 "replies"		=> $replies,
												 "new"          => (($id > $lastpostread) ? 1 : 0));
					
					if ($row_l = $Db->GetRow("SELECT p.id,p.parent,p.title,p.author,p.created,u.name
											  FROM #__forums_posts AS p JOIN #__user AS u ON p.author=u.uid WHERE (p.id='".intval($id)."' OR p.parent='".intval($id)."') ORDER BY p.id DESC LIMIT 1")) {
						$row_l['id']			= Io::Output($row_l['id'],"int");
						$row_l['parent']		= Io::Output($row_l['parent'],"int");
						$row_l['thread_id']		= ($row_l['parent']>0) ? $row_l['parent'] : $row_l['id'] ;
						$row_l['pagination']	= ($replies>$postslimit) ? "&amp;page=".ceil($replies/$postslimit) : "" ;
						$row_l['title']			= Utils::CutStr(Io::Output($row_l['title']),20);
						$row_l['author']		= Io::Output($row_l['author'],"int");
						$row_l['created']		= Time::Output(Io::Output($row_l['created']));
						$row_l['author_name']	= Io::Output($row_l['name']);
						
						$plugin_threads[$id] = array_merge($plugin_threads[$id],array("lastpost" => $row_l));
					}
				}
			}
			
			//TODO: Check permissions and configuration to build data
			//Info
			$plugin_data['op'] = "threads";
			$plugin_data['forum'] = $f;
			$plugin_data['forum_title'] = $forum_title;
			//Buttons
			$plugin_data['buttons']['write'] = ($User->CheckRole($roles_write) || $User->CheckRole($roles_moderate)) ? 1 : 0 ;
			
			//Pagination
			include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
			$Pag = new Pagination();
			$Pag->page = $page;
			$Pag->limit = $limit;
			$Pag->query = "SELECT COUNT(*) AS tot FROM #__forums_posts AS t JOIN #__forums AS f ON t.forum=f.id{$where}";
			$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;op=browse&amp;f=".$f."&amp;page={PAGE}";
			$plugin_pagination = $Pag->Show();
			
			//Output
			$this->plugin_data = $plugin_data;
			$this->plugin_forums = array();
			$this->plugin_subforums = $plugin_subforums;
			$this->plugin_threads = $plugin_threads;
			$this->plugin_pagination = $plugin_pagination;
			$this->Show("forums_index");
		} else {
			MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
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
	
	public function ReadThread() {
		global $Db,$User,$Router,$Ext;
	
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
		//Options
		$orderby	= $Router->GetOption("posts_orderby","p.id");
		$order		= $Router->GetOption("posts_order","ASC");
		$limit		= $Router->GetOption("posts_limit",10);
		$quickreply	= $Router->GetOption("quickreply",1);
	
		//Pagination
		$page = Io::GetVar("GET","page","int",false,1);
		if ($page<=0) $page = 1;
		$from = ($page * $limit) - $limit;
		
		$plugin_data = array();
		$plugin_posts = array();
		
		$t = Io::GetVar("GET","t","int");
		
		//Get forum's data
		if ($result_t = $Db->GetList("SELECT u.user,u.name AS author_name,u.regdate,p.*,f.name AS forum_name,f.title AS forum_title,f.parent AS forum_parent,f.roles_read,f.roles_write,f.roles_moderate FROM #__forums_posts AS p JOIN #__user AS u JOIN #__forums AS f
									ON p.author=u.uid AND p.forum=f.id
									WHERE p.id='".intval($t)."' AND p.parent=0")) {
		
			$row_t = current($result_t);
			$thread_title	= Io::Output($row_t['title']);
			$forum_name		= Io::Output($row_t['forum_name']);
			$forum_title	= Io::Output($row_t['forum_title']);
			$forum_parent	= Io::Output($row_t['forum_parent'],"int");
			$roles_read		= Utils::Unserialize(Io::Output($row_t['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($row_t['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($row_t['roles_moderate']));
			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT title,name,roles_read,roles_write,roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$parent_title	= Io::Output($rowp['title']);
				$parent_name	= Io::Output($rowp['name']);
				$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
				$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));
				
				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
			}
			
			//Site title step
			Utils::AddTitleStep($thread_title);
			
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name&amp;t=$t' title='".CleanTitleAtr($thread_title)."'>$thread_title</a></span>";

			if ($User->CheckRole($roles_read) || $User->CheckRole($roles_moderate)) {
				$result = $Db->GetList("SELECT u.user,u.name AS author_name,u.regdate,p.*,f.name AS forum_name FROM #__forums_posts AS p JOIN #__user AS u JOIN #__forums AS f
										ON p.author=u.uid AND p.forum=f.id
										WHERE p.parent='".intval($t)."'
										ORDER BY ".$Db->_e($orderby)." ".$Db->_e($order)."
										LIMIT ".intval($from).",".intval($limit));
					
				if ($page == 1) $result = array_merge($result_t,$result);
				
				if ($result) {	
					//Get author's forum related data
					$uids = array();
					$udata = array();
					foreach ($result as $row) $uids[intval($row['author'])] = true;
					if (sizeof($uids)) {
						$res = $Db->GetList("SELECT d.*,u.name,u.user,u.email,u.regdate,u.options,u.roles,u.lastseen
											 FROM #__forums_userdata AS d JOIN #__user AS u ON d.uid=u.uid WHERE d.uid IN (".implode(",",array_keys($uids)).")");
						foreach ($res as $r) {
							$uid = Io::Output($r['uid'],"int");
							$r['options'] = Utils::Unserialize($r['options']);
							$r['options']['rolename'] = $User->GetRoleName($uid);
							$r['options']['stylename'] = $User->Name($uid);
							$r['avatar'] = $User->DisplayAvatar($uid,false,true);
							$r['roles'] = Utils::Unserialize($r['roles']);
							$udata[$uid] = $r;
							
							$Ext->RunMext("Forum_ReadPost_Auth",array(&$udata,$uid,$r));
						}
					}
											
					//Inc Hits
					$Db->Query("UPDATE #__forums_posts SET hits=hits+1 WHERE id='".intval($t)."'");

					foreach ($result as $row) {
						$id				= Io::Output($row['id'],"int");
						$lastchild		= Io::Output($row['lastchild'],"int");
						$title			= Io::Output($row['title']);
						$forum_id		= Io::Output($row['forum'],"int");
						$forum_name		= Io::Output($row['forum_name']);
						$flag			= Io::Output($row['flag'],"int");
						$author_id		= Io::Output($row['author'],"int");
						$author_name	= Io::Output($row['author_name']);
						$modauthor_id	= Io::Output($row['modauthor'],"int");
						$modauthor_name	= ($modauthor_id != $author_id) ? $User->Name($modauthor_id,true) : $author_name;
						$created		= Time::Output(Io::Output($row['created']));
						$modified		= Time::Output(Io::Output($row['modified']));
						$hits			= Io::Output($row['hits'],"int");
						$text			= BBCode::ToHtml(Io::Output($row['text']));
					
						$plugin_posts[$id] = array("id"			=> $id,
												   "title"		=> $title,
												   "lastchild"	=> $lastchild,
												   "forum_id"	=> $forum_id,
												   "flag"		=> $flag,
												   "author_id"	=> $author_id,
												   "author_name"=> $author_name,
												   "modauthor_id"	=> $modauthor_id,
												   "modauthor_name"	=> $modauthor_name,
												   "created"	=> $created,
												   "modified"	=> $modified,
												   "hits"		=> $hits,
												   "text"		=> $text,
												   "buttons"	=> array("edit"		=> (($User->CheckRole($roles_write) && $User->Uid()==$author_id) || $User->CheckRole($roles_moderate) ? 1 : 0 ),
												   						 "delete"	=> (($User->CheckRole($roles_write) && $User->Uid()==$author_id) || $User->CheckRole($roles_moderate) ? 1 : 0 )),
												   "userdata"	=>(isset($udata[$author_id]) ? $udata[$author_id] : array()));
						
						$Ext->RunMext("Forum_ReadPost",array(&$plugin_posts,$id,$row));
						
					}

					//Set last post id in user data
					$Db->Query("INSERT INTO #__forums_userdata (uid,lastpostread)
								VALUES ('".intval($User->Uid())."','".intval($id)."')
								ON DUPLICATE KEY UPDATE lastpostread='".intval($id)."'");
					
					//TODO: Check permissions and configuration to build data
					//Info
					$plugin_data['forum_id'] = $forum_id;
					$plugin_data['thread_id'] = $t;
					//Buttons
					$plugin_data['buttons']['reply'] = ($User->CheckRole($roles_write)) ? 1 : 0 ;
					$plugin_data['quickreply'] = $quickreply;
					
					$plugin_form = array();
					if ($quickreply && $User->CheckRole($roles_write)) {						
						$plugin_form['form']['op']				= "reply";
						$plugin_form['form']['action']			= "index.php?"._NODE."="._PLUGIN."&amp;op=reply&amp;t=$t&amp;p=0&amp;sop=save";
						$plugin_form['form']['method']			= "POST";
						$plugin_form['form']['enctype']			= "multipart/form-data";//"application/x-www-form-urlencoded";
						$plugin_form['form']['title']			= "Re: ".$thread_title;
						$plugin_form['form']['text']			= ""; //Default text
						
						//Token
						$tok = Utils::GenerateToken();
						$tok = explode(":",$tok);
						$plugin_form['form']['ctok'] = $tok[0];
						$plugin_form['form']['ftok'] = $tok[1];
						
						$Ext->RunMext("Forum_QuickReply",array(&$plugin_form));
					}
					
					$Ext->RunMext("Forum_ReadThread",array(&$plugin_data,&$plugin_posts));
					
					//Pagination
					include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
					$Pag = new Pagination();
					$Pag->page = $page;
					$Pag->limit = $limit;
					$Pag->query = "SELECT COUNT(p.id) AS tot FROM #__forums_posts AS p WHERE p.parent='".intval($t)."'";
					$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name&amp;t=$t&amp;page={PAGE}";
					$plugin_pagination = $Pag->Show();
					
					//Output
					$this->plugin_data = $plugin_data;
					$this->plugin_posts = $plugin_posts;
					$this->plugin_form = $plugin_form;
					$this->plugin_pagination = $plugin_pagination;
					$this->Show("forums_thread");

					if ($User->CheckRole($roles_moderate)) {
						?>
						<span class="sys_forum_mod">
							<?php
							echo "<a href='index.php?"._NODE."="._PLUGIN."&amp;op=moderate&amp;t=$t'>"._t("MODERATE")."</a>\n";
							?>
						</span>
						<?php
					}
				}
			} else {
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			MemErr::Trigger("USERERROR",_t("THREAD_DOESNT_EXIST"));
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
	
	public function NewThread() {
		global $Db,$User,$Router,$Ext;
		
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array('editor'=>true));
		//Start buffering content
		Utils::StartBuffering();
		
		$f = Io::GetVar("GET","f","[^a-zA-Z0-9\-]");
		$plugin_form = array();
		if ($row = $Db->GetRow("SELECT * FROM #__forums WHERE name='".$Db->_e($f)."' AND status='active'")) {
			$sop = Io::GetVar("GET","sop","[^a-zA-Z0-9\-]");
			
			$forum_name		= Io::Output($row['name']);
			$forum_title	= Io::Output($row['title']);
			$forum_parent	= Io::Output($row['parent'],"int");
			$roles_read		= Utils::Unserialize(Io::Output($row['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($row['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT title,name,roles_read,roles_write,roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$parent_title	= Io::Output($rowp['title']);
				$parent_name	= Io::Output($rowp['name']);
				$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
				$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));
				
				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
			}
				
			//Site title step
			Utils::AddTitleStep($forum_title);
				
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
			
			if ($User->CheckRole($roles_write) || $User->CheckRole($roles_moderate)) {
				switch ($sop) {
					default:
						$plugin_form['forum']['id']				= Io::Output($row['id'],"int");
						$plugin_form['forum']['parent_id']		= Io::Output($row['parent'],"int");
						$plugin_form['forum']['category_id']	= Io::Output($row['category'],"int");
						$plugin_form['forum']['title']			= Io::Output($row['title']);
						$plugin_form['forum']['name']			= $f;
						$plugin_form['forum']['description']	= Io::Output($row['description']);
						
						$plugin_form['form']['op']				= "write";
						$plugin_form['form']['action']			= "index.php?"._NODE."="._PLUGIN."&amp;op=write&amp;f=$f&amp;sop=save";
						$plugin_form['form']['method']			= "POST";
						$plugin_form['form']['enctype']			= "multipart/form-data";//"application/x-www-form-urlencoded";
						$plugin_form['form']['title']			= ""; //Default title
						$plugin_form['form']['text']			= ""; //Default text
						
						//Token
						$tok = Utils::GenerateToken();
						$tok = explode(":",$tok);
						$plugin_form['form']['ctok'] = $tok[0];
						$plugin_form['form']['ftok'] = $tok[1];
						
						$Ext->RunMext("Forum_NewThread",array(&$plugin_form));
							
						//Output
						$this->plugin_data = array();
						$this->plugin_form = $plugin_form;
						$this->Show("forums_form");
						break;
					case "save":
						//Check token
						if (Utils::CheckToken()) {
							//Get POST data
							$title = Io::GetVar('POST','title');
							$text = Io::GetVar('POST','text');
							
							$errors = array();
							if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
							if (empty($text)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TEXT"));
							
							if (!sizeof($errors)) {
								//Insert
								$Db->Query("INSERT INTO #__forums_posts (forum,title,author,text,created,status) VALUES
											('".Io::Output($row['id'],"int")."','".$Db->_e($title)."','".intval($User->Uid())."','".$Db->_e($text)."',NOW(),'active')");
								
								$id = intval($Db->InsertId());
								
								//Inc user posts
								$Db->Query("INSERT INTO #__forums_userdata (uid,posts,lastpost)
											VALUES ('".intval($User->Uid())."',1,'".intval($id)."')
											ON DUPLICATE KEY UPDATE posts=posts+1,lastpost='".intval($id)."'");
								
								$Ext->RunMext("Forum_NewThread_Save",array($id,$row,$title,$text));
								
								MemErr::Trigger("INFO",_t("THREAD_CREATED"),_t("REDIRECTING"));
								Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=$f&t=$id"));
							} else {
								MemErr::Trigger("USERERROR",implode("<br />",$errors));
							}
						} else {
							MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
						}
						break;
				}
			} else {
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			MemErr::Trigger("USERERROR",_t("FORUM_DOESNT_EXIST"));
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
	
	public function NewPost() {
		global $Db,$User,$Router,$Ext;
	
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array('editor'=>true));
		//Start buffering content
		Utils::StartBuffering();
	
		//TODO: Check privs
		
		$t = Io::GetVar("GET","t","int");
		$p = Io::GetVar("GET","p","int");
		$plugin_form = array();
		if ($row = $Db->GetRow("SELECT p.*,u.name AS author_name,f.name AS forum_name,f.title AS forum_title,f.parent AS forum_parent,f.roles_read,f.roles_write,f.roles_moderate FROM #__forums_posts AS p JOIN #__user AS u JOIN #__forums AS f ON p.author=u.uid AND p.forum=f.id WHERE p.id='".intval($t)."'")) {
			$sop = Io::GetVar("GET","sop","[^a-zA-Z0-9\-]");
			
			$thread_title	= Io::Output($row['title']);
			$forum_name		= Io::Output($row['forum_name']);
			$forum_title	= Io::Output($row['forum_title']);
			$forum_parent	= Io::Output($row['forum_parent'],"int");
			$roles_read		= Utils::Unserialize(Io::Output($row['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($row['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
			$status         = Io::Output($row['status']);
			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT title,name,roles_read,roles_write,roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$parent_title	= Io::Output($rowp['title']);
				$parent_name	= Io::Output($rowp['name']);
				$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
				$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));

				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
			}
				
			//Site title step
			Utils::AddTitleStep($thread_title);
				
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name&amp;t=$t' title='".CleanTitleAtr($thread_title)."'>$thread_title</a></span>";
			
			if (($User->CheckRole($roles_write) || $User->CheckRole($roles_moderate)) && ($status=="active" || $User->IsAdmin())) {
				switch ($sop) {
					default:
						$plugin_form['thread']['id']			= $t;
						$plugin_form['thread']['parent_id']		= Io::Output($row['parent'],"int");
						$plugin_form['thread']['forum_id']		= Io::Output($row['forum'],"int");
						$plugin_form['thread']['title']			= Io::Output($row['title']);
						$plugin_form['thread']['author_id']		= Io::Output($row['author'],"int");
						$plugin_form['thread']['author_name']	= Io::Output($row['author_name']);
						$plugin_form['thread']['text']			= Io::Output($row['text']);
						
						$plugin_form['form']['op']				= "reply";
						$plugin_form['form']['action']			= "index.php?"._NODE."="._PLUGIN."&amp;op=reply&amp;t=$t&amp;p=$p&amp;sop=save";
						$plugin_form['form']['method']			= "POST";
						$plugin_form['form']['enctype']			= "multipart/form-data";//"application/x-www-form-urlencoded";
						$plugin_form['form']['title']			= "Re: ".$plugin_form['thread']['title'];
						$plugin_form['form']['text']			= ""; //Default text
						
						//Quote
						if ($p > $t) {
							if ($rowp = $Db->GetRow("SELECT p.*,u.name AS author_name FROM #__forums_posts AS p JOIN #__user AS u ON p.author=u.uid WHERE p.id='".intval($p)."' AND p.parent='".intval($t)."' AND p.status='active'")){
								$plugin_form['form']['title'] = Io::Output($rowp['title']);
								$plugin_form['form']['text'] = "[quote][i]".Io::Output($rowp['author_name']).":[/i]\n\n".Io::Output($rowp['text'])."[/quote]";
							}
						} else if ($p == $t) {
							$plugin_form['form']['text'] = "[quote][i]".$plugin_form['thread']['author_name'].":[/i]\n\n".$plugin_form['thread']['text']."[/quote]";
						}
						
						//Token
						$tok = Utils::GenerateToken();
						$tok = explode(":",$tok);
						$plugin_form['form']['ctok'] = $tok[0];
						$plugin_form['form']['ftok'] = $tok[1];
						
						$Ext->RunMext("Forum_Reply",array(&$plugin_form));
							
						//Output
						$this->plugin_data = array();
						$this->plugin_form = $plugin_form;
						$this->Show("forums_form");
						break;
					case "save":
						//Check token
						if (Utils::CheckToken()) {
							//Get POST data
							$title = Io::GetVar('POST','title');
							$text = Io::GetVar('POST','text');
							
							$errors = array();
							if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
							if (empty($text)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TEXT"));
							
							if (!sizeof($errors)) {
								//Insert
								$Db->Query("INSERT INTO #__forums_posts (parent,forum,title,author,text,created,status) VALUES
											('".Io::Output($row['id'],"int")."','".Io::Output($row['forum'],"int")."','".$Db->_e($title)."','".intval($User->Uid())."','".$Db->_e($text)."',NOW(),'active')");
								
								$id = intval($Db->InsertId());
								
								//Update lastchild in the thread starting post
								$Db->Query("UPDATE #__forums_posts SET lastchild='".intval($id)."' WHERE id='".intval($t)."'");
								
								//Inc user posts
								$Db->Query("INSERT INTO #__forums_userdata (uid,posts,lastpost)
											VALUES ('".intval($User->Uid())."',1,'".intval($id)."')
											ON DUPLICATE KEY UPDATE posts=posts+1,lastpost='".intval($id)."'");
								
								$Ext->RunMext("Forum_Reply_Save",array($id,$row,$title,$text));
								
								MemErr::Trigger("INFO",_t("POST_ADDED"),_t("REDIRECTING"));
								
								//Pagination
								$limit = $Router->GetOption("posts_limit",10);
								$num = $Db->GetNum("SELECT p.id FROM #__forums_posts AS p WHERE p.parent='".intval($t)."' AND p.id<='".intval($id)."'");
								$pag = ($num>$limit) ? "&page=".ceil($num/$limit) : "" ;
								Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=".Io::Output($row['forum_name'])."&t=$t".$pag."#post$id"));
							} else {
								MemErr::Trigger("USERERROR",implode("<br />",$errors));
							}
						} else {
							MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
						}
						break;
				}
			} else {
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			MemErr::Trigger("USERERROR",_t("THREAD_DOESNT_EXIST"));
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
	
	public function Modify() {
		global $Db,$User,$Router,$Ext;
	
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header(array('editor'=>true));
		//Start buffering content
		Utils::StartBuffering();
	
		//TODO: Check privs
		$p = Io::GetVar("GET","p","int");
		$plugin_form = array();
		if ($row = $Db->GetRow("SELECT p.*,u.name AS author_name,f.name AS forum_name,f.title AS forum_title,f.parent AS forum_parent,f.roles_read,f.roles_write,f.roles_moderate FROM #__forums_posts AS p JOIN #__user AS u JOIN #__forums AS f ON p.author=u.uid AND p.forum=f.id WHERE p.id='".intval($p)."'")) {
			$sop = Io::GetVar("GET","sop","[^a-zA-Z0-9\-]");
			$author 		= Io::Output($row['author'],"int");
			$thread_title	= Io::Output($row['title']);
			$forum_name		= Io::Output($row['forum_name']);
			$forum_title	= Io::Output($row['forum_title']);
			$forum_parent	= Io::Output($row['forum_parent'],"int");
			$roles_read		= Utils::Unserialize(Io::Output($row['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($row['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
			$status         = Io::Output($row['status']);
			$parent         = Io::Output($row['parent'],"int");

			$t = $p;
			if (!$t) $t = $p;

			if ($parent>0) {
				$prow = $Db->GetRow("SELECT status FROM #__forums_posts WHERE id='".intval($parent)."'");
				$status         = Io::Output($prow['status']);
			}

			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT title,name,roles_read,roles_write,roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$parent_title	= Io::Output($rowp['title']);
				$parent_name	= Io::Output($rowp['name']);
				$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
				$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));

				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
			}
			
			//Site title step
			Utils::AddTitleStep($thread_title);
			
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name&amp;t=$t' title='".CleanTitleAtr($thread_title)."'>$thread_title</a></span>";
			if ((($User->CheckRole($roles_write) && $User->Uid()==$author) || $User->CheckRole($roles_moderate)) && ($status=="active" || $User->IsAdmin())) {
				switch ($sop) {
					default:
						$plugin_form['thread']['id']			= $p;
						$plugin_form['thread']['parent_id']		= Io::Output($row['parent'],"int");
						$plugin_form['thread']['forum_id']		= Io::Output($row['forum'],"int");
						$plugin_form['thread']['title']			= Io::Output($row['title']);
						$plugin_form['thread']['author_id']		= Io::Output($row['author'],"int");
						$plugin_form['thread']['author_name']	= Io::Output($row['author_name']);
						$plugin_form['thread']['text']			= Io::Output($row['text']);
						
						$plugin_form['form']['op']				= "reply";
						$plugin_form['form']['action']			= "index.php?"._NODE."="._PLUGIN."&amp;op=edit&amp;p=$p&amp;sop=save";
						$plugin_form['form']['method']			= "POST";
						$plugin_form['form']['enctype']			= "multipart/form-data";//"application/x-www-form-urlencoded";
						$plugin_form['form']['title']			= $plugin_form['thread']['title'];
						$plugin_form['form']['text']			= $plugin_form['thread']['text'];
						
						//Token
						$tok = Utils::GenerateToken();
						$tok = explode(":",$tok);
						$plugin_form['form']['ctok'] = $tok[0];
						$plugin_form['form']['ftok'] = $tok[1];
						
						$Ext->RunMext("Forum_Modify",array($p,$row));
							
						//Output
						$this->plugin_data = array();
						$this->plugin_form = $plugin_form;
						$this->Show("forums_form");
						break;
					case "save":
						//Check token
						if (Utils::CheckToken()) {
							//Get POST data
							$title = Io::GetVar('POST','title');
							$text = Io::GetVar('POST','text');
							
							$errors = array();
							if (empty($title)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TITLE"));
							if (empty($text)) $errors[] = _t("THE_FIELD_X_IS_REQUIRED",_t("TEXT"));
							
							if (!sizeof($errors)) {
								//Update
								$Db->Query("UPDATE #__forums_posts SET title='".$Db->_e($title)."',text='".$Db->_e($text)."',modified=NOW(),modauthor='".intval($User->Uid())."' WHERE id='".intval($p)."'");
								
								$Ext->RunMext("Forum_Modify_Save",array($p,$row,$title,$text));
								
								MemErr::Trigger("INFO",_t("POST_EDITED"),_t("REDIRECTING"));
								$t = Io::Output($row['parent'],"int");
								if (!$t) $t = $p;
								
								//Pagination
								$limit = $Router->GetOption("posts_limit",10);
								$num = $Db->GetNum("SELECT p.id FROM #__forums_posts AS p WHERE p.parent='".intval($t)."' AND p.id<='".intval($p)."'");
								$pag = ($num>$limit) ? "&page=".ceil($num/$limit) : "" ;
								Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=".Io::Output($row['forum_name'])."&t=$t".$pag."#post$p"));
							} else {
								MemErr::Trigger("USERERROR",implode("<br />",$errors));
							}
						} else {
							MemErr::Trigger("USERERROR",_t("INVALID_TOKEN"));
						}
						break;
				}
			} else {
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			MemErr::Trigger("USERERROR",_t("POST_DOESNT_EXIST"));
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
	
	public function DeletePost() {
		global $Db,$User,$Router,$Ext;
	
		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
	
		//TODO: Check privs - Delete
				
		$p = Io::GetVar("GET","p","int");
		if ($row = $Db->GetRow("SELECT p.*,f.name AS forum_name,f.title AS forum_title,f.parent AS forum_parent,f.roles_read,f.roles_write,f.roles_moderate FROM #__forums_posts AS p JOIN #__forums AS f ON p.forum=f.id WHERE p.id='".intval($p)."'")) {
			$author = Io::Output($row['author'],"int");
			$parent = Io::Output($row['parent'],"int");
			
			$thread_title	= Io::Output($row['title']);
			$forum_name		= Io::Output($row['forum_name']);
			$forum_title	= Io::Output($row['forum_title']);
			$forum_parent	= Io::Output($row['forum_parent'],"int");
			$roles_read		= Utils::Unserialize(Io::Output($row['roles_read']));
			$roles_write	= Utils::Unserialize(Io::Output($row['roles_write']));
			$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
			$status         = Io::Output($row['status']);
			$parent         = Io::Output($row['parent'],"int");

			$t = $p;
			if (!$t) $t = $p;

			if ($parent>0) {
				$prow = $Db->GetRow("SELECT status FROM #__forums_posts WHERE id='".intval($parent)."'");
				$status         = Io::Output($prow['status']);
			}

			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT title,name,roles_read,roles_write,roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$parent_title	= Io::Output($rowp['title']);
				$parent_name	= Io::Output($rowp['name']);
				$roles_read		= Utils::Unserialize(Io::Output($rowp['roles_read']));
				$roles_write	= Utils::Unserialize(Io::Output($rowp['roles_write']));
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));
					
				//Breadcrumbs path
				$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$parent_name' title='".CleanTitleAtr($parent_title)."'>$parent_title</a></span>";
			}
				
			//Site title step
			Utils::AddTitleStep($thread_title);
				
			//Breadcrumbs path
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name' title='".CleanTitleAtr($forum_title)."'>$forum_title</a></span>";
			$Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;f=$forum_name&amp;t=$t' title='".CleanTitleAtr($thread_title)."'>$thread_title</a></span>";
			
			if ((($User->CheckRole($roles_write) && $User->Uid()==$author) || $User->CheckRole($roles_moderate)) && ($status=="active" || $User->IsAdmin())) {
				if ($parent>0) {
					$sop = Io::GetVar("GET","sop","[^a-zA-Z0-9\-]");
					switch ($sop) {
						default:
							$plugin_op = array();
							$plugin_op['message'] = _t("SURE_PERMANENTLY_DELETE_THE_X",MB::strtolower(_t("POST")));
							$plugin_op['options'][] = "<a href='index.php?"._NODE."="._PLUGIN."&amp;op=delete&amp;p=$p&amp;sop=delete'>"._t("DELETE")."</a>";
							$plugin_op['options'][] = "<a href='index.php?"._NODE."="._PLUGIN."&amp;f=".Io::Output($row['forum_name'])."&amp;t=$parent'>"._t("DONT_DELETE")."</a>";
						
							$Ext->RunMext("Forum_Delete",array($p,$row));
							
							//Output
							$this->plugin_op = $plugin_op;
							$this->Show("forums_op");
							break;
						case "delete":
							$Db->Query("DELETE FROM #__forums_posts WHERE id='".intval($p)."' AND parent>0");
							
							//Rebuild lastchild
							$row = $Db->GetRow("SELECT id FROM #__forums_posts WHERE parent='".intval($parent)."' ORDER BY id DESC LIMIT 1");
							$Db->Query("UPDATE #__forums_posts SET lastchild='".Io::Output($row['id'],"int")."' WHERE id='".intval($parent)."'");
							
							//User's data
							$Db->Query("UPDATE #__forums_userdata SET posts=posts-1,lastpost=(SELECT id FROM #__forums_posts WHERE author='".intval($author)."' ORDER BY id DESC LIMIT 1) WHERE uid='".intval($author)."'");
							
							$Ext->RunMext("Forum_Delete_Save",array($p,$row));
							
							//Redirect
							MemErr::Trigger("INFO",_t("POST_DELETED"),_t("REDIRECTING"));
							Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=$forum_name&t=$parent"));
							
							break;
					}
				} else {
					MemErr::Trigger("USERERROR",_t("CANT_DELETE_STARTPOST"));
				}
			} else {
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			MemErr::Trigger("USERERROR",_t("POST_DOESNT_EXIST"));
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
	
	public function Moderate() {
		global $Db,$User,$Router;

		//Load plugin language
		Language::LoadPluginFile(_PLUGIN_CONTROLLER);
		//Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();

		?>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#action").change(function() {
					if ($("#action").val()=="move") {
						$("#forum").css("display","block");
					} else {
						$("#forum").css("display","none");
					}

					if ($("#action").val()=="delete") {
						if (!confirm('<?php echo _t("SURE_PERMANENTLY_DELETE_THE_X_AND_CONTENT",MB::strtolower(_t("THREAD"))); ?>')) {
							$("#action").val('sticky');
						}
					}
				});
			});
		</script>
		<?php

		$id = Io::GetInt("GET","t");
		if ($row = $Db->GetRow("SELECT p.parent,p.flag,p.status,f.roles_moderate,f.parent AS forum_parent,f.id AS fid,f.name as fname FROM #__forums_posts AS p JOIN #__forums AS f ON p.forum=f.id WHERE p.id='".intval($id)."'")) {
			$fid            = Io::Output($row['fid'],"int");
			$fname          = Io::Output($row['fname']);
			$forum_parent	= Io::Output($row['forum_parent'],"int");
			$roles_moderate	= Utils::Unserialize(Io::Output($row['roles_moderate']));
			$flag           = Io::Output($row['flag'],"int");
			$status         = Io::Output($row['status']);
			$t = Io::Output($row['parent'],"int");

			if ($forum_parent) {
				$rowp = $Db->GetRow("SELECT roles_moderate FROM #__forums WHERE id='".intval($forum_parent)."'");
				$roles_moderate	= Utils::Unserialize(Io::Output($rowp['roles_moderate']));
			}

			if ($User->CheckRole($roles_moderate)) {
				$sop = Io::GetAlnum("POST","sop");
				switch ($sop) {
					default:
						$form = new Form();
						$form->action = "index.php?"._NODE."="._PLUGIN."&amp;op=moderate&t=$id";
						$form->enctype = "multipart/form-data";

						$form->Open();

						//Action
						$form->AddElement(array("element"	=>"select",
						                        "label"		=>_t("ACTION"),
						                        "name"		=>"sop",
												"id"        =>"action",
						                        "values"	=>array(_t("TOGGLE_X",_t("STICKY")) => "sticky",
						                                            _t("TOGGLE_X",_t("LOCKED")) => "locked",
						                                            _t("DELETE")                => "delete",
											                        _t("MOVE")                  => "move")));

						//Forum
						$select = array();
						$result = $Db->GetList("SELECT id,title FROM #__forums WHERE parent=0 AND status='active' ORDER BY title");
						foreach ($result as $row) {
							$select[Io::Output($row['title'])] = Io::Output($row['id'],"int");
							$sresult = $Db->GetList("SELECT id,title FROM #__forums WHERE parent=".intval(Io::Output($row['id'],"int"))." AND status='active' ORDER BY title");
							foreach ($sresult as $srow) {
								$select["&nbsp;&nbsp;&nbsp;".Io::Output($srow['title'])] = Io::Output($srow['id'],"int");
							}
						}
						$form->AddElement(array("element"	=>"select",
						                        "name"		=>"forum",
						                        "id"        =>"forum",
												"style"     =>"display:none;",
						                        "values"	=>$select));

						//Apply
						$form->AddElement(array("element"	=>"submit",
						                        "name"		=>"apply",
						                        "inline"	=>true,
						                        "value"		=>_t("APPLY")));

						$form->Close();
						break;
					case "sticky":
						$flag = ($flag==0) ? 1 : 0 ;
						$Db->Query("UPDATE #__forums_posts SET flag=".intval($flag)." WHERE id='".intval($id)."'");

						Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=$fname&t=$id"));
						break;
					case "delete":
						$Db->Query("DELETE FROM #__forums_posts WHERE id='".intval($id)."' OR parent='".intval($id)."'");

						Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN));
						break;
					case "move":
						$forum = Io::GetInt("POST","forum");
						if ($forum>0) {
							$Db->Query("UPDATE #__forums_posts SET forum=".intval($forum)." WHERE id='".intval($id)."' OR parent='".intval($id)."'");
						}

						Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=$fname&t=$id"));
						break;
					case "locked":
						$locked = ($status=="locked") ? "active" : "locked" ;
						$Db->Query("UPDATE #__forums_posts SET status='".$Db->_e($locked)."' WHERE id='".intval($id)."'");

						Utils::Redirect(RewriteUrl("index.php?"._NODE."="._PLUGIN."&f=$fname&t=$id"));
						break;
				}
			} else {
				//No auth
				MemErr::Trigger("USERERROR",_t("NOT_AUTH_TO_PERF_OP"));
			}
		} else {
			//Thread or forum not found
			MemErr::Trigger("USERERROR",_t("POST_DOESNT_EXIST"));
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
}

?>