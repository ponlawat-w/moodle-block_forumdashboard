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

require_once(__DIR__ . '/../classes/engagement.php');

use context_course;

/**
 * Abstract class for different level of engagement metric
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class engagement extends metricitem
{
    /**
     * Calculator caches with key being discussion ID and value being calculator
     *
     * @var \block_forumbashboard_engagement\engagementcalculator
     */
    protected static $CALCULATOR_CACHES = [];
    
    /**
     * Result caches in array with first level key being course ID, second level key being user ID, then value to be result
     * 
     * @var array
     */
    protected static $RESULT_CACHES = [];

    /**
     * Try get result, null if not set
     *
     * @param int $scope
     * @param int $userid
     * @return \block_forumdashboard_engagement\engagementresult|null
     */
    private static function trygetresult($scope, $userid) {
        if (!isset(static::$RESULT_CACHES[$scope])) {
            return null;
        }
        return isset(static::$RESULT_CACHES[$scope][$userid]) ? static::$RESULT_CACHES[$scope][$userid] : null;
    }

    /**
     * Set result
     *
     * @param int $scope
     * @param int $userid
     * @param \block_forumdashboard_engagement\engagementresult $result
     */
    private static function setresult($scope, $userid, $result) {
        if (!isset(static::$RESULT_CACHES[$scope])) {
            static::$RESULT_CACHES[$scope] = [];
        }
        static::$RESULT_CACHES[$scope][$userid] = $result;
    }

    /**
     * Get calculator from discussion ID, might be cache
     *
     * @param int $discussionid
     * @return \block_forumdashboard_engagement\engagementcalculator
     */
    private static function getcalculator($discussionid) {
        if (!isset(static::$CALCULATOR_CACHES[$discussionid])) {
            $engagementmethod = get_config('block_forumdashboard', 'defaultengagementmethod');
            static::$CALCULATOR_CACHES[$discussionid] = \block_forumdashboard_engagement\engagement::getinstancefrommethod($engagementmethod, $discussionid);
        }
        return static::$CALCULATOR_CACHES[$discussionid];
    }

    /**
     * Get engagement results
     *
     * @param int $scope
     * @param int $userid
     * @return \block_forumdashboard_engagement\engagementresult
     */
    protected static function getresult($scope, $userid)
    {
        global $DB;
        $result = static::trygetresult($scope, $userid);
        if ($result) {
            return $result;
        }

        $discussions = $DB->get_records('forum_discussions', $scope ? ['course' => $scope] : []);
        $result = new \block_forumdashboard_engagement\engagementresult();
        foreach ($discussions as $discussion) {
            $calculator = static::getcalculator($discussion->id);
            $result->add($calculator->calculate(($userid)));
        }
        static::setresult($scope, $userid, $result);
        return $result;
    }

    /**
     * Get value from specified level
     *
     * @param int $scope
     * @param int $userid
     * @param int $level
     * @return int
     */
    protected static function getlevel($scope, $userid, $level)
    {
        if ($level > 4 || $level < 0) {
            return -1;
        }
        $results = static::getresult($scope, $userid);
        return $level < 4 ? $results->getlevel($level) : $results->getl4up();
    }

    /**
     * Get average value from specified level
     *
     * @param int $scope
     * @param int $level
     * @return int
     */
    protected static function getlevelaverage($scope, $level)
    {
        $users = $scope ? get_enrolled_users(context_course::instance($scope)) : get_users();
        $sum = 0;
        foreach ($users as $user) {
            $sum += static::getlevel($scope, $user->id, $level);
        }
        return $sum / count($users);
    }
}
