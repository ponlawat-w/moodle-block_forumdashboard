<?php

namespace block_forumdashboard\metricitems;

use context_course;

class participantcount extends metricitem {
  
  public static $itemname = 'participantcount';
  public static $nameidentifier = 'item_participantcount';
  public static $valueidentifier = 'identifier_participantcount';
  public static $default_bgcolor = '#3bad6e';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    global $DB;

    $discussionids = array_map(function ($discussion) { return $discussion->id; }, $DB->get_records('forum_discussions', $scope ? ['course' => $scope] : []));
    list($discsin, $discsparam) = $DB->get_in_or_equal($discussionids);
    $discswhere = 'userid != ? and discussion ' . $discsin;

    $participantscountrecords = $DB->get_fieldset_select('forum_posts', 'DISTINCT userid', $discswhere, array_merge([$userid], $discsparam));

    return $participantscountrecords ? count($participantscountrecords) : 0;
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
