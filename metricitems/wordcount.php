<?php

namespace block_forumdashboard\metricitems;

class wordcount extends metricitem {
  
  public static $itemname = 'wordcount';
  public static $nameidentifier = 'item_wordcount';
  public static $valueidentifier = 'identifier_wordcount';
  public static $default_bgcolor = '#a411c2';
  public static $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    global $DB;

    $record = $scope ?
      $DB->get_record_sql('select group_concat(posts.message) msg from {forum_posts} posts ' .
        'join {forum_discussions} discussions on posts.discussion = discussions.id '  .
        'where discussions.course = ? group by posts.userid having userid = ?', [$scope, $userid])
      : $DB->get_record_sql('select group_concat(message) msg from {forum_posts} group by userid having userid = ?', [$userid]);

    return count_words($record->msg);
  }

  public function get_average($scope) {
    global $DB;

    $records = $scope ?
      $DB->get_records_sql('select group_concat(posts.message) msg from {forum_posts} posts ' .
        'join {forum_discussions} discussions on posts.discussion = discussions.id where discussions.course = ? group by posts.userid', [$scope])
      : $DB->get_records_sql('select group_concat(message) msg from {forum_posts} group by userid');

    $sum = 0;

    foreach ($records as $record) {
      $sum += count_words($record->msg);
    }

    return $sum / count($records);
  }
}
