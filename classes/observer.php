<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


defined('MOODLE_INTERNAL') or die();

include_once(__DIR__ . '/../lib.php');

/**
 * Event observer
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_forumdashboard_observer
{
    /**
     * Consequent actions when a forum post is created
     *
     * @param \mod_forum\event\post_created $event
     * @return void
     */
    public static function forum_post_created(\mod_forum\event\post_created $event)
    {
        global $DB;
        $config = get_config('block_forumdashboard');
        if (!$config || !isset($config->replynotifications) || !$config->replynotifications) {
            return;
        }

        $postid = $event->get_data()['objectid'];
        $post = $DB->get_record('forum_posts', ['id' => $postid]);
        if (!$post) {
            return;
        }

        $parentid = $post->parent;
        $notifieduserids = [];
        while ($parentid) {
            $parentpost = $DB->get_record('forum_posts', ['id' => $parentid]);
            if (!$parentpost) {
                break;
            }
            if ($post->userid == $parentpost->userid) {
                $parentid = $parentpost->parent;
                continue;
            }
            if (in_array($parentpost->userid, $notifieduserids)) {
                $parentid = $parentpost->parent;
                continue;
            }

            block_forumdashboard_notifyuser($post, $parentpost);
            array_push($notifieduserids, $parentpost->userid);

            $parentid = $parentpost->parent;
        }
    }
}
