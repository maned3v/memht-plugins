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
defined("_LOAD") or die("Access denied");

class linksModel extends Views {
   
    public function _index() {
		global $Db,$User,$Router;
        //Initialize and show site header
		Layout::Header();
		//Start buffering content
		Utils::StartBuffering();
		
        $cat   = Io::GetVar("GET","cat","[^a-zA-Z0-9\-]");
        $num = array();
        $plugin_cats  = array();
        $plugin_links = array();           
        $this->plugin_pagination = "";
        
        if(isset($cat) && !empty($cat)) {
            $category = $Db->GetRow("SELECT id,title FROM #__links_categories WHERE name='".$Db->_e($cat)."'");
            
            $limit = $Router->GetOption("limit",5);
			$page = Io::GetVar("GET","page","int",false,1);
			if ($page<=0) $page = 1;
			$from = ($page * $limit) - $limit;
            
            if ($result_list = $Db->GetList("SELECT id,roles FROM #__links WHERE category='".Io::Output($category['id'],"int")."' AND status='active' ORDER BY id")) {
                foreach ($result_list as $row_list) {
                    $gid	= Io::Output($row_list['id'],"int");
                    $groles = Utils::Unserialize(Io::Output($row_list['roles']));
                    if ($User->CheckRole($groles) || $User->IsAdmin()) { $num[] = $gid; }
                }                
            }
            $items = implode(",",$num);
            $items = ($items) ? $items : "0"; 
            if ($result = $Db->GetList("SELECT * FROM #__links WHERE id IN(".$Db->_e($items).") ORDER BY id LIMIT ".intval($from).",".intval($limit)."")) {
                foreach ($result as $row) {
    				$id	          = Io::Output($row['id'],"int");
    				$title	      = Io::Output($row['title']);
    				$name	      = Io::Output($row['name']);
                    $roles	      = Utils::Unserialize(Io::Output($row['roles']));
                    $url	      = Io::Output($row['url']);
                    $description  = Io::Output($row['description']);
                    $image	      = Io::Output($row['image']);

    				if ($User->CheckRole($roles) || $User->IsAdmin()) {
                        $plugin_links[] = array('id' 	     => $id,
                                               'title'	     => $title,
        									   'name'	     => $name,
                                               'category'    => $cat,
                                               'url'         => $url,
                                               'description' => $description,
                                               'image'       => $image);
                    }
    			} 
				//Pagination
				include_once(_PATH_LIBRARIES._DS."MemHT"._DS."content"._DS."pagination.class.php");
				$Pag = new Pagination();
				$Pag->page = $page;
				$Pag->limit = $limit;
				$Pag->query = "SELECT COUNT(id) AS tot FROM #__links WHERE id IN(".$Db->_e($items).")";
				$Pag->url = "index.php?"._NODE."="._PLUGIN."&amp;cat=".$Db->_e($cat)."&amp;page={PAGE}";
				$plugin_pagination = $Pag->Show();
                $this->plugin_pagination = $plugin_pagination; 
                Utils::AddTitleStep(Io::Output($category['title']));
                $Router->breadcrumbs[] = "<span class='sys_breadcrumb'><a href='index.php?"._NODE."="._PLUGIN."&amp;cat=".$Db->_e($cat)."' title='".Io::Output($category['title'])."'>".Io::Output($category['title'])."</a></span>";                              
            }
        } else {

            if ($result = $Db->GetList("SELECT * FROM #__links_categories ORDER BY id")) {
                foreach ($result as $row) {
    				$cid	= Io::Output($row['id'],"int");
    				$ctitle	= Io::Output($row['title']);
    				$cname	= Io::Output($row['name']);
             
                    $plugin_cats[] = array('cid' 	=> $cid,
                                           'ctitle'	=> $ctitle,
    									   'cname'	=> $cname);
    			}
                
                $this->plugin_pagination = "";
		    }         
        }
       
		//Output
		$this->plugin_cats = $plugin_cats;
        $this->plugin_links = $plugin_links;
		$this->Show("links".__FUNCTION__);
		
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
    
    public function _go() {
        global $Db,$User;
        $cat     = Io::GetVar("GET","cat","[^a-zA-Z0-9\-]"); 
        $title   = Io::GetVar("GET","title","[^a-zA-Z0-9\-]");
        
        $category = $Db->GetRow("SELECT id FROM #__links_categories WHERE name='".$Db->_e($cat)."'");
        
        if($row = $Db->GetRow("SELECT * FROM #__links WHERE category = '".Io::Output($category['id'],"int")."' AND name = '".$Db->_e($title)."'")) {
            $roles	= Utils::Unserialize(Io::Output($row['roles']));
            $url	= Io::Output($row['url']);
            if ($User->CheckRole($roles) || $User->IsAdmin()) {
                $Db->Query("UPDATE #__links SET hits=hits+1 WHERE category = '".Io::Output($category['id'],"int")."' AND name = '".$Db->_e($title)."'");
                Utils::Redirect($url);
            } else {
				//Initialize and show site header
				Layout::Header();
				//Start buffering content
				Utils::StartBuffering();                
                Error::Trigger("USERERROR",_t("NOT_AUTH_TO_ACCESS_X",MB::strtolower(_t("LINK"))));
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
        } else {
            Utils::Redirect("index.php");
        }
           
    }    

}