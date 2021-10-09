<?php

namespace block_forumdashboard\metricitems;

use context_course;

abstract class engagement extends metricitem {
  protected static $CRON_CACHES = [];

  protected function getcronlevels($scope, $userid) {
    if (!isset(static::$CRON_CACHES[$scope])) {
      static::$CRON_CACHES[$scope] = [];
    }
    if (!isset(static::$CRON_CACHES[$scope][$userid])) {
      static::$CRON_CACHES[$scope][$userid] = static::getlevels($scope, $userid);
    }

    return static::$CRON_CACHES[$scope][$userid];
  }

  protected static function getlevels($scope, $userid) {
    global $DB;
    $posts = $scope ?
      $DB->get_records_sql('select posts.* from {forum_posts} posts join {forum_discussions} discussions on posts.discussion = discussions.id '
        . 'where discussions.course = ? and posts.userid = ? and posts.parent > 0 order by posts.id', [$scope, $userid]) :
      $DB->get_records_sql('select * from {forum_posts} where userid = ? and parent > 0 order by id', [$userid]);
    
    $depths = [];
    $levels = [0, 0, 0, 0];
    foreach ($posts as $post) {
      if (!isset($depths[$post->id])) {
        $parent = $post->parent;
        $depths[$post->id] = 1;
        while ($parent > 0) {
          if ($parentpost = $DB->get_record('forum_posts', ['id' => $parent])) {
            if ($parentpost->userid == $userid) {
              if (isset($depths[$parentpost->id])) {
                unset($depths[$parentpost->id]);
              }
              $depths[$parentpost->id] = 0;
              $depths[$post->id]++;
            }
            $parent = $parentpost->parent;
          } else {
            $depths[$post->id] = 0;
            continue;
          }
        }
        
        if ($depths[$post->id] < 4) {
          $levels[$depths[$post->id] - 1]++;
        } else {
          $levels[3]++;
        }
      }
    }

    return $levels;
  }

  protected static function getlevel($scope, $userid, $level) {
    return static::getlevels($scope, $userid)[$level];
  }

  protected static function getlevelaverage($scope, $level) {
    if ($scope) {
      $users = get_enrolled_users(context_course::instance($scope));
      $sum = 0;
      foreach ($users as $user) {
        $sum += static::getlevel($scope, $user->id, $level);
      }
      return $sum / count($users);
    } else {
      $users = get_users();
      $sum = 0;
      foreach ($users as $user) {
        $sum += static::getlevel($scope, $user->id, $level);
      }
      return $sum / count($users);
    }
  }
}
