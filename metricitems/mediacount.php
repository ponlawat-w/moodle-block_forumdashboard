<?php

namespace block_forumdashboard\metricitems;

use context_course;

include_once(__DIR__ . '/../lib.php');

class mediacount extends textbase {

  public $itemname = 'mediacount';
  public $nameidentifier = 'item_mediacount';
  public $valueidentifier = 'identifier_mediacount';
  public $default_bgcolor = '#bfbfbf';
  public $default_textcolor = '#000000';

  public function calculatecronvalue($scope, $userid) {
    $mediacount = 0;
    $msgrecords = static::get_cronmessagerecords($scope, $userid);
    foreach ($msgrecords as $msgrecord) {
      $medianumresult = report_discussion_metrics_get_mulutimedia_num($msgrecord->message);
      $mediacount += ($medianumresult ? $medianumresult->num : 0);
    }

    return $mediacount;
  }

  public function get_value($scope, $userid) {
    $mediacount = 0;
    $msgrecords = static::get_messagerecords($scope, $userid);
    foreach ($msgrecords as $msgrecord) {
      $medianumresult = report_discussion_metrics_get_mulutimedia_num($msgrecord->message);
      $mediacount += ($medianumresult ? $medianumresult->num : 0);
    }

    return $mediacount;
  }

  public function get_average($scope) {
    $users = $scope ? get_enrolled_users(context_course::instance($scope)) : get_users();
    $sum = 0;
    foreach ($users as $user) {
      $sum += $this->get_value($scope, $user->id);
    }

    return $sum / count($users);
  }
}
