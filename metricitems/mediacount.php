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

namespace block_forumdashboard\metricitems;

use context_course;
use context_module;

include_once(__DIR__ . '/../lib.php');

/**
 * Media count
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mediacount extends metricitem
{
    static $DISCUSSIONS_MODCONTEXTID_LOOKUP = [];

    /**
     * @var string
     */
    public $itemname = 'mediacount';

    /**
     * @var string
     */
    public $nameidentifier = 'item_mediacount';

    /**
     * @var string
     */
    public $valueidentifier = 'identifier_mediacount';

    /**
     * @var string
     */
    public $default_bgcolor = '#bfbfbf';

    /**
     * @var string
     */
    public $default_textcolor = '#000000';

    /**
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_value($scope, $userid)
    {
        $mediacount = 0;
        $posts = $this->getposts($scope, $userid);
        foreach ($posts as $post) {
            $medianumresult = report_discussion_metrics_get_mulutimedia_num($post->message);
            $mediacount += ($medianumresult ? $medianumresult->num : 0);
            $mediacount += $this->getmediaattachmentscount($post);
        }

        return $mediacount;
    }

    /**
     * @param int $scope
     * @return double
     */
    public function get_average($scope)
    {
        $users = $scope ? get_enrolled_users(context_course::instance($scope)) : get_users();
        $sum = 0;
        foreach ($users as $user) {
            $sum += $this->get_value($scope, $user->id);
        }

        return $sum / count($users);
    }

    private function getposts($scope, $userid)
    {
        global $DB;
        return $scope ?
            $DB->get_records_sql('select posts.* from {forum_posts} posts ' .
                'join {forum_discussions} discussions on posts.discussion = discussions.id '  .
                'where discussions.course = ? and posts.userid = ?', [$scope, $userid])
            : $DB->get_records_sql('select * from {forum_posts} where userid = ?', [$userid]);
    }

    private function getmediaattachmentscount($post)
    {
        global $DB;
        if (!isset(self::$DISCUSSIONS_MODCONTEXTID_LOOKUP[$post->discussion])) {
            $discussion = $DB->get_record('forum_discussions', ['id' => $post->discussion], '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance('forum', $discussion->forum, $discussion->course, false, MUST_EXIST);
            $modulecontext = context_module::instance($cm->id);
            self::$DISCUSSIONS_MODCONTEXTID_LOOKUP[$post->discussion] = $modulecontext->id;
        }

        return block_forumdashboard_countattachmentmultimedia(self::$DISCUSSIONS_MODCONTEXTID_LOOKUP[$post->discussion], $post->id);
    }
}
