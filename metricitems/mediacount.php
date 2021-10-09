<?php

namespace block_forumdashboard\metricitems;

include_once(__DIR__ . '/../lib.php');

class mediacount extends metricitem {

  public static $itemname = 'mediacount';
  public static $nameidentifier = 'item_mediacount';
  public static $valueidentifier = 'identifier_mediacount';
  public static $default_bgcolor = '#bfbfbf';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    global $DB;

    $record = $scope ?
      $DB->get_record_sql('select group_concat(posts.message) msg from {forum_posts} posts ' .
        'join {forum_discussions} discussions on posts.discussion = discussions.id '  .
        'where discussions.course = ? group by posts.userid having userid = ?', [$scope, $userid])
      : $DB->get_record_sql('select group_concat(message) msg from {forum_posts} group by userid having userid = ?', [$userid]);

    $mediacount = report_discussion_metrics_get_mulutimedia_num($record->msg);
    return $mediacount ? $mediacount->num : 0;
  }

  public function get_average($scope) {
    global $DB;

    $records = $scope ?
      $DB->get_records_sql('select group_concat(posts.message) msg from {forum_posts} posts ' .
        'join {forum_discussions} discussions on posts.discussion = discussions.id where discussions.course = ? group by posts.userid', [$scope])
      : $DB->get_records_sql('select group_concat(message) msg from {forum_posts} group by userid');

    $sum = 0;

    foreach ($records as $record) {
      $mediacount = report_discussion_metrics_get_mulutimedia_num($record->msg);
      $sum += $mediacount ? $mediacount->num : 0;
    }

    return $sum / count($records);
  }
}
