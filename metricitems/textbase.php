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

/**
 * Metrics whose calculation are based on textual data
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class textbase extends metricitem
{
    /**
     * Cron caches for text based, with first level key being course ID, second level key being user ID and then the value being text
     *
     * @var array
     */
    private static $CRON_TEXTS = [];

    /**
     * Get all message records of a user in a course
     *
     * @param int $scope
     * @param int $userid
     * @return object[]
     */
    protected static function get_messagerecords($scope, $userid)
    {
        global $DB;

        $records = $scope ?
            $DB->get_records_sql('select posts.id, posts.message from {forum_posts} posts ' .
                'join {forum_discussions} discussions on posts.discussion = discussions.id '  .
                'where discussions.course = ? and posts.userid = ?', [$scope, $userid])
            : $DB->get_records_sql('select id, message from {forum_posts} where userid = ?', [$userid]);

        return $records;
    }

    /**
     * Get cron cached message
     *
     * @param int $scope
     * @param int $userid
     * @return object[]
     */
    protected static function get_cronmessagerecords($scope, $userid)
    {
        if (!isset(static::$CRON_TEXTS[$scope])) {
            static::$CRON_TEXTS[$scope] = [];
        }
        if (!isset(static::$CRON_TEXTS[$scope][$userid])) {
            static::$CRON_TEXTS[$scope][$userid] = static::get_messagerecords($scope, $userid);
        }
        return static::$CRON_TEXTS[$scope][$userid];
    }
}
