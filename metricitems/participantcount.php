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

/**
 * Count participants
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participantcount extends metricitem
{
    /**
     * @var string
     */
    public $itemname = 'participantcount';

    /**
     * @var string
     */
    public $nameidentifier = 'item_participantcount';

    /**
     * @var string
     */
    public $valueidentifier = 'identifier_participantcount';

    /**
     * @var string
     */
    public $default_bgcolor = '#3bad6e';

    /**
     * @var string
     */
    public $default_textcolor = '#000000';

    /**
     * @var string
     */
    public $globalsensitive = true;
    
    /**
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_value($scope, $userid)
    {
        global $DB;

        $discussionids = array_map(function ($discussion) {
            return $discussion->id;
        }, $DB->get_records('forum_discussions', $scope ? ['course' => $scope] : []));
        if (!count($discussionids)) {
            return 0;
        }
        list($discsin, $discsparam) = $DB->get_in_or_equal($discussionids);
        $discswhere = 'userid != ? and discussion ' . $discsin;

        $participantscountrecords = $DB->get_fieldset_select('forum_posts', 'DISTINCT userid', $discswhere, array_merge([$userid], $discsparam));

        return $participantscountrecords ? count($participantscountrecords) : 0;
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
}
