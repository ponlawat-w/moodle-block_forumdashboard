<?php

namespace block_forumdashboard\metricitems;

use context_course;

class participantcount extends metricitem {
  
  public $itemname = 'participantcount';
  public $nameidentifier = 'item_participantcount';
  public $valueidentifier = 'identifier_participantcount';
  public $default_bgcolor = '#3bad6e';
  public $default_textcolor = '#000000';
  public $globalsensitive = true;

  public function get_value($scope, $userid) {
    global $DB;

    $discussionids = array_map(function ($discussion) { return $discussion->id; }, $DB->get_records('forum_discussions', $scope ? ['course' => $scope] : []));
    if (!count($discussionids)) {
      return 0;
    }
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
