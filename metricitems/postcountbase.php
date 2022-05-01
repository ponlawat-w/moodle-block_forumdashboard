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
 * x
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class postcountbase extends metricitem
{
    /**
     * Get joined tables and conditions in SQL
     *
     * @return string[]
     */
    protected static abstract function getadditionaljoins();

    /**
     * Get joined tables and conditions parameters
     *
     * @return array
     */
    protected static abstract function getadditionaljoinparameters();

    /**
     * Get WHERE conditions in SQL
     *
     * @return string[]
     */
    protected static abstract function getadditionalconditions();

    /**
     * Get WHERE conditions parameters
     *
     * @return array
     */
    protected static abstract function getadditionalparameters();

    /**
     * Get initial join SQL statement
     *
     * @param int $scope
     * @param string[] $conditions
     * @param array $parameters
     * @return array
     */
    private function get_joins($scope, &$conditions, &$parameters)
    {
        if ($scope) {
            array_push($conditions, 'discussions.course = ?');
            array_push($parameters, $scope);
            return ['JOIN {forum_discussions} discussions ON posts.discussion = discussions.id'];
        }
        return [];
    }

    /**
     * Calculate value
     *
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_value($scope, $userid)
    {
        global $DB;

        $conditions = [];
        $joinparameters = [];
        $parameters = [];

        $joins = array_merge($this->get_joins($scope, $conditions, $joinparameters), static::getadditionaljoins());
        $joinparameters = array_merge($joinparameters, static::getadditionaljoinparameters());

        $conditions = array_merge($conditions, ['posts.userid = ?'], static::getadditionalconditions());
        $parameters = array_merge($parameters, [$userid], static::getadditionalparameters());

        $record = $DB->get_record_sql(
            'SELECT COUNT(*) resultcount FROM {forum_posts} posts ' . implode(' ', $joins) . ' WHERE ' . implode(' AND ', $conditions),
            array_merge($joinparameters, $parameters)
        );
        return $record->resultcount;
    }

    /**
     * Calculate average value
     *
     * @param int $scope
     * @return double
     */
    public function get_average($scope)
    {
        global $DB;

        $conditions = [];
        $joinparameters = [];
        $parameters = [];

        $joins = array_merge($this->get_joins($scope, $conditions, $joinparameters), static::getadditionaljoins());
        $joinparameters = array_merge($joinparameters, static::getadditionaljoinparameters());

        $conditions = array_merge($conditions, static::getadditionalconditions());
        $parameters = array_merge($parameters, static::getadditionalparameters());

        $whereclause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $record = $DB->get_record_sql('SELECT AVG(p.resultcount) avgresult FROM (' .
            'SELECT COUNT(*) resultcount FROM {forum_posts} posts ' . implode(' ', $joins) . ' ' . $whereclause .
            ') p', array_merge($joinparameters, $parameters));
        return $record->avgresult;
    }
}
