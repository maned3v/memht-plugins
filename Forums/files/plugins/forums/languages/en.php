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

global $config_sys,$memht_lang;

$memht_lang[$config_sys['language']]['REPLY'] = 'Reply';
$memht_lang[$config_sys['language']]['QUICKREPLY'] = 'Quick reply';
$memht_lang[$config_sys['language']]['QUOTE'] = 'Quote';
$memht_lang[$config_sys['language']]['NEW_THREAD'] = 'New thread';
$memht_lang[$config_sys['language']]['FORUM_DOESNT_EXIST'] = 'Sorry, the selected forum doesn\'t exist';
$memht_lang[$config_sys['language']]['THREAD_DOESNT_EXIST'] = 'Sorry, the selected thread doesn\'t exist';
$memht_lang[$config_sys['language']]['POST_DOESNT_EXIST'] = 'Sorry, the selected post doesn\'t exist';
$memht_lang[$config_sys['language']]['THREAD_CREATED'] = 'Thread created successfully';
$memht_lang[$config_sys['language']]['POST_ADDED'] = 'Post added successfully';
$memht_lang[$config_sys['language']]['POST_EDITED'] = 'Post modified successfully';
$memht_lang[$config_sys['language']]['POST_DELETED'] = 'Post deleted successfully';
$memht_lang[$config_sys['language']]['REDIRECTING'] = 'Redirecting...';
$memht_lang[$config_sys['language']]['MODIFIED_BY_X_ON_Y'] = 'Modified by %s on %s';
$memht_lang[$config_sys['language']]['CANT_DELETE_STARTPOST'] = 'Sorry, you cannot delete the thread starter post';
$memht_lang[$config_sys['language']]['SUBFORUMS'] = 'Subforums:';
$memht_lang[$config_sys['language']]['THREADS'] = 'Threads';
$memht_lang[$config_sys['language']]['POSTS'] = 'Posts';
$memht_lang[$config_sys['language']]['LAST_POST'] = 'Last post';
$memht_lang[$config_sys['language']]['FORUM'] = 'Forum';
$memht_lang[$config_sys['language']]['FORUMS'] = 'Forums';
$memht_lang[$config_sys['language']]['SUBFORUM'] = 'Subforum';
$memht_lang[$config_sys['language']]['SUBFORUMS'] = 'Subforums';
$memht_lang[$config_sys['language']]['REPLIES'] = 'Replies';
$memht_lang[$config_sys['language']]['VIEWS'] = 'Views';
$memht_lang[$config_sys['language']]['THREAD'] = 'Thread';
$memht_lang[$config_sys['language']]['STICKY'] = 'Sticky';
$memht_lang[$config_sys['language']]['NO_THREADS_CREATE_FIRST'] = 'There are no threads in this forum, be the first and create one!';
$memht_lang[$config_sys['language']]['NO_FORUMS_IN_CAT'] = 'There are no forums in this category';
$memht_lang[$config_sys['language']]['NOT_AUTH_TO_PERF_OP'] = 'Sorry, you are not authorized to perform this operation';
$memht_lang[$config_sys['language']]['MODERATORS'] = 'Moderators';
$memht_lang[$config_sys['language']]['AUTHORIZATIONS'] = 'Authorizations';
$memht_lang[$config_sys['language']]['SURE_REMOVE_THE_X'] = 'Are you sure you want to remove the %s?';
$memht_lang[$config_sys['language']]['CAN_READ'] = 'Can read';
$memht_lang[$config_sys['language']]['CAN_WRITE'] = 'Can write';
$memht_lang[$config_sys['language']]['CAN_MODERATE'] = 'Can moderate';
$memht_lang[$config_sys['language']]['MODERATE'] = 'Moderate';
$memht_lang[$config_sys['language']]['ACTION'] = 'Action';
$memht_lang[$config_sys['language']]['TOGGLE_X'] = 'Toggle %s';
$memht_lang[$config_sys['language']]['MOVE'] = 'Move';
$memht_lang[$config_sys['language']]['APPLY'] = 'Apply';

?>