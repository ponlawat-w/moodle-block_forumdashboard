<?php

defined('MOODLE_INTERNAL') or die();

include_once(__DIR__ . '/../lib.php');

class block_forumdashboard_observer {
  public static function forum_post_created(\mod_forum\event\post_created $event) {
    global $DB;
    $config = get_config('block_forumdashboard');
    if (!$config || !isset($config->replynotifications) || !$config->replynotifications) {
      return;
    }

    $postid = $event->get_data()['objectid'];
    $post = $DB->get_record('forum_posts', ['id' => $postid]);
    if (!$post) {
      return;
    }

    $parentid = $post->parent;
    $notifieduserids = [];
    while ($parentid) {
      $parentpost = $DB->get_record('forum_posts', ['id' => $parentid]);
      if (!$parentpost) {
        break;
      }
      if ($post->userid == $parentpost->userid) {
        $parentid = $parentpost->parent;
        continue;
      }
      if (in_array($parentpost->userid, $notifieduserids)) {
        $parentid = $parentpost->parent;
        continue;
      }

      block_forumdashboard_notifyuser($post, $parentpost);
      array_push($notifieduserids, $parentpost->userid);

      $parentid = $parentpost->parent;
    }
  }
}
