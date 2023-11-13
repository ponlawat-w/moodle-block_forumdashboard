<?php

namespace block_forumdashboard\metricitems;

class reactionsgiven extends metricitem {
    public $itemname = 'reactionsgiven';
    public $nameidentifier = 'item_reactionsgiven';
    public $valueidentifier = 'identifier_reactionsgiven';
    public $default_bgcolor = '#ff7700';
    public $default_textcolor = '#ffffff';

    public function get_value($scope, $userid) {
        /**
         * @var \moodle_database $DB
         */
        global $DB;

        if (!block_forumdashbaord_reactforuminstalled()) {
            return 0;
        }

        $result = $DB->get_record_sql(
            'SELECT COUNT(*) reactionsgiven FROM {reactforum_reacted} WHERE userid = ? AND post IN ('
            . 'SELECT id FROM {forum_posts} WHERE discussion IN ('
            . 'SELECT id FROM {forum_discussions} WHERE 0 = ? OR course = ?'
            . '))',
            [$userid, $scope, $scope]
        );
        return $result ? $result->reactionsgiven : 0;
    }

    public function get_average($scope) {
        /**
         * @var \moodle_database $DB
         */
        global $DB;

        if (!block_forumdashbaord_reactforuminstalled()) {
            return 0;
        }

        $result = $DB->get_record_sql(
            'SELECT SUM(stats.reactionsgiven) sumreactionsgiven FROM ('
            . 'SELECT userid, COUNT(*) reactionsgiven FROM {reactforum_reacted} WHERE post IN ('
            . 'SELECT id FROM {forum_posts} WHERE discussion IN ('
            . 'SELECT id FROM {forum_discussions} WHERE 0 = ? OR course = ?'
            . ')) GROUP BY userid) stats',
            [$scope, $scope]
        );
        if (!$result) {
            return 0;
        }
        $userscount = count(self::get_allusers($scope));
        return $userscount ? $result->sumreactionsgiven / $userscount : 0;
    }
}
