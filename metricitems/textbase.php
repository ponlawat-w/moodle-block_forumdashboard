<?php

namespace block_forumdashboard\metricitems;

abstract class textbase extends metricitem {
  private static $CRON_TEXTS = [];

  protected static function get_messagerecords($scope, $userid) {
    global $DB;

    $records = $scope ?
      $DB->get_records_sql('select posts.id, posts.message from {forum_posts} posts ' .
        'join {forum_discussions} discussions on posts.discussion = discussions.id '  .
        'where discussions.course = ? and posts.userid = ?', [$scope, $userid])
      : $DB->get_records_sql('select id, message from {forum_posts} where userid = ?', [$userid]);
    
    return $records;
  }

  protected static function get_cronmessagerecords($scope, $userid) {
    if (!isset(static::$CRON_TEXTS[$scope])) {
      static::$CRON_TEXTS[$scope] = [];
    }
    if (!isset(static::$CRON_TEXTS[$scope][$userid])) {
      static::$CRON_TEXTS[$scope][$userid] = static::get_messagerecords($scope, $userid);
    }
    return static::$CRON_TEXTS[$scope][$userid];
  }
}
