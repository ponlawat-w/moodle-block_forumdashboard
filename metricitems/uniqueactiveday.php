<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * TODO describe file uniqueactiveday
 *
 * @package    block_forumdashboard
 * @copyright  2026 Ponlawat Weerapanpisit <ponlawat_w@outlook.co.th>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_forumdashboard\metricitems;

class uniqueactiveday extends metricitem {
    public $itemname = 'uniqueactiveday';
    public $nameidentifier = 'item_uniqueactiveday';
    public $valueidentifier = 'identifier_uniqueactiveday';
    public $default_bgcolor = '#ffe8e8';
    public $default_textcolor = '#000000';

    public function get_value($scope, $userid) {
        global $DB;
        /** @var \moodle_database $DB */
        $DB;
        
        $scope = $scope ? $scope : 0;

        $record = $DB->get_record_sql(
            <<<SQL
                SELECT COUNT(DISTINCT FLOOR(fp.created / 86400)) uniqueactivedays
                FROM {user} u
                LEFT OUTER JOIN {forum_posts} fp
                    ON fp.userid = u.id
                WHERE u.id = ?
                AND fp.discussion IN (
                    SELECT fd.id FROM {forum_discussions} fd
                    WHERE 0 = ? OR fd.course = ?
                )
            SQL,
            [$userid, $scope, $scope]
        );
        return $record->uniqueactivedays;
    }

    public function get_average($scope) {
        global $DB;
        /** @var \moodle_database $DB */
        $DB;
        
        $scope = $scope ? $scope : 0;

        $record = $DB->get_record_sql(
            <<<SQL
                SELECT SUM(uniqueactivedays) sumuniqueactivedays
                FROM (
                    SELECT COUNT(DISTINCT FLOOR(fp.created / 86400)) uniqueactivedays
                    FROM {user} u
                    LEFT OUTER JOIN {forum_posts} fp
                        ON fp.userid = u.id
                    WHERE fp.discussion IN (
                        SELECT fd.id FROM {forum_discussions} fd
                        WHERE 0 = ? OR fd.course = ?
                    )
                    GROUP BY u.id
                )
            SQL,
            [$scope, $scope]
        );
        $sum = $record->sumuniqueactivedays;

        /** @var \context $coursecontext */
        $coursecontext = $scope ? \core\context\course::instance($scope) : null;
        $userscount = $coursecontext ? count(get_enrolled_users($coursecontext)) : get_users(false);

        return $userscount ? ($sum / $userscount) : 0;
    }
}
