<?php

namespace block_forumdashboard\metricitems;

class reactionsreceived extends metricitem {
    public $itemname = 'reactionsreceived';
    public $nameidentifier = 'item_reactionsreceived';
    public $valueidentifier = 'identifier_reactionsreceived';
    public $default_bgcolor = '#a86f32';
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
            'SELECT COUNT(*) reactionsreceived FROM {reactforum_reacted} WHERE post IN ('
            . 'SELECT id FROM {forum_posts} WHERE userid = ? AND discussion IN ('
            . 'SELECT id FROM {forum_discussions} WHERE 0 = ? OR course = ?'
            . '))',
            [$userid, $scope, $scope]
        );
        return $result ? $result->reactionsreceived : 0;
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
            'SELECT SUM(stats.reactionsreceived) sumreactionsreceived FROM ('
            . 'SELECT p.userid, COUNT(*) reactionsreceived FROM {reactforum_reacted} r'
            . ' JOIN {forum_posts} p ON r.post = p.id'
            . ' JOIN {forum_discussions} c ON p.discussion = c.id'
            . ' WHERE 0 = ? OR course = ?'
            . ' GROUP BY p.userid) stats',
            [$scope, $scope]
        );
        if (!$result) {
            return 0;
        }
        $userscount = count(self::get_allusers($scope));
        return $userscount ? $result->sumreactionsreceived / $userscount : 0;
    }
}
