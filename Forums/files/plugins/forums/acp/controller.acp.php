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
defined("_LOAD") or die("Access denied");

class forumsController extends forumsModel {
	public function index() {
		$this->Main();
	}
    //Categories
	public function categories() {
		$this->ListCategories();
	}
    public function newcategory() {
        $this->NewCategories();
    }
    public function editcategory() {
        $this->EditCategories();
    }
    public function deletecategory() {
        $this->DeleteCategories();
    }

    //Forums
    public function newforum() {
        $this->NewForums();
    }
    public function editforum() {
        $this->EditForums();
    }
    public function deleteforum() {
        $this->DeleteForums();
    }

    //Moderators
    public function moderators() {
        $this->BrowseModerators();
    }
    public function addmod() {
        $this->AddModerator();
    }
    public function removemod() {
        $this->RemoveModerator();
    }

    //Authorizations
    public function auth() {
		$this->SetAuthorizations();
	}
	public function setauth() {
		$this->SetAuthAsync();
	}

	//Options
	public function options() {
		$this->ForumsOpt();
	}
}

?>