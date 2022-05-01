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
 * Abstract class for different level of engagement metric
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class engagement extends metricitem
{
    /**
     * Cron caches in array with first level key being course ID, second level key being user ID, third level key being engagement level
     * 
     * @var array
     */
    protected static $CRON_CACHES = [];

    /**
     * Get levels from cron caches
     *
     * @param int $scope
     * @param int $userid
     * @return int[]
     */
    protected function getcronlevels($scope, $userid)
    {
        if (!isset(static::$CRON_CACHES[$scope])) {
            static::$CRON_CACHES[$scope] = [];
        }
        if (!isset(static::$CRON_CACHES[$scope][$userid])) {
            static::$CRON_CACHES[$scope][$userid] = static::getlevels($scope, $userid);
        }

        return static::$CRON_CACHES[$scope][$userid];
    }

    /**
     * Get engagement levels
     *
     * @param int $scope
     * @param int $userid
     * @return int[]
     */
    protected static function getlevels($scope, $userid)
    {
        global $DB;
        $posts = $scope ?
            $DB->get_records_sql('select posts.* from {forum_posts} posts join {forum_discussions} discussions on posts.discussion = discussions.id '
                . 'where discussions.course = ? and posts.userid = ? and posts.parent > 0 order by posts.id', [$scope, $userid]) :
            $DB->get_records_sql('select * from {forum_posts} where userid = ? and parent > 0 order by id', [$userid]);

        $depths = [];
        $levels = [0, 0, 0, 0];
        foreach ($posts as $post) {
            if (!isset($depths[$post->id])) {
                $parent = $post->parent;
                $depths[$post->id] = 1;
                while ($parent > 0) {
                    if ($parentpost = $DB->get_record('forum_posts', ['id' => $parent])) {
                        if ($parentpost->userid == $userid) {
                            if (isset($depths[$parentpost->id])) {
                                unset($depths[$parentpost->id]);
                            }
                            $depths[$parentpost->id] = 0;
                            $depths[$post->id]++;
                        }
                        $parent = $parentpost->parent;
                    } else {
                        $depths[$post->id] = 0;
                        continue;
                    }
                }

                if ($depths[$post->id] < 4) {
                    $levels[$depths[$post->id] - 1]++;
                } else {
                    $levels[3]++;
                }
            }
        }

        return $levels;
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
        return static::getlevels($scope, $userid)[$level];
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
        if ($scope) {
            $users = get_enrolled_users(context_course::instance($scope));
            $sum = 0;
            foreach ($users as $user) {
                $sum += static::getlevel($scope, $user->id, $level);
            }
            return $sum / count($users);
        } else {
            $users = get_users();
            $sum = 0;
            foreach ($users as $user) {
                $sum += static::getlevel($scope, $user->id, $level);
            }
            return $sum / count($users);
        }
    }
}
