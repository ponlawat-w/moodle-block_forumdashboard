<?php

namespace block_forumdashboard\metricitems;

use context_course;

class wordcount extends textbase {
  
  public $itemname = 'wordcount';
  public $nameidentifier = 'item_wordcount';
  public $valueidentifier = 'identifier_wordcount';
  public $default_bgcolor = '#a411c2';
  public $default_textcolor = '#ffffff';

  public function calculatecronvalue($scope, $userid) {
    $wordcount = 0;
    $msgrecords = static::get_cronmessagerecords($scope, $userid);
    foreach ($msgrecords as $msgrecord) {
      $wordcount += count_words($msgrecord->message);
    }

    return $wordcount;
  }

  public function get_value($scope, $userid) {
    $wordcount = 0;
    $msgrecords = static::get_messagerecords($scope, $userid);
    foreach ($msgrecords as $msgrecord) {
      $wordcount += count_words($msgrecord->message);
    }

    return $wordcount;
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
