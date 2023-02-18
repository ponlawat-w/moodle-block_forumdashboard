<?php

defined('MOODLE_INTERNAL') || die();

include_once(__DIR__ . '/lib.php');

class block_forumdashboard extends block_base {
  public $items = [];

  public function __construct() {
    parent::__construct();

    $this->items = block_forumdashboard_getiteminstances();
  }

  public function init() {
    $this->title = get_string('blocktitle', 'block_forumdashboard');
    $this->config = get_config('block_forumdashboard');
  }

  public function has_config() {
    return true;
  }

  public function instance_can_be_hidden() {
    return false;
  }

  public function instance_allow_multiple() {
    return false;
  }

  public function applicable_formats() {
    return [
      'all' => false,
      'my' => true,
      'site' => false
    ];
  }

  private function get_items_content() {
    $html = '';
    foreach ($this->items as $item) {
      $html .= $item->renderinitial();
    }
    return $html;
  }

  private function get_courses($recentid) {
    $courses = [];
    $mycourses = enrol_get_my_courses(null, 'fullname');
    foreach ($mycourses as $mycourse) {
      array_push($courses, [
        'id' => $mycourse->id,
        'fullname' => $mycourse->fullname,
        'selected' => $recentid == $mycourse->id
      ]);
    }

    return $courses;
  }

  private function expandable() {
    foreach ($this->items as $item) {
      if (!$item->initial) {
        return true;
      }
    }

    return false;
  }

  private function get_notifications($returnurl) {
    global $PAGE;

    $notifications = block_forumdashboard_getmynotifications();
    $results = [];
    foreach ($notifications as $notification) {
      $results[] = [
        'subject' => $notification->subject,
        'url' => new moodle_url('/message/output/popup/mark_notification_read.php', ['notificationid' => $notification->id]),
        'course' => $notification->customdata,
        'dismissurl' => new moodle_url('/blocks/forumdashboard/notification_dismiss.php', ['id' => $notification->id, 'return' => $returnurl])
      ];
    }

    return $results;
  }

  public function get_content() {
    global $OUTPUT, $PAGE, $USER;

    $instanceid = $this->context->instanceid;
    $returnurl = new moodle_url($PAGE->url, $_GET, 'forumdashboard-' . $instanceid);
    $dismissallurl = new moodle_url('/blocks/forumdashboard/notification_dismiss.php', ['id' => 'all', 'return' => $returnurl]);

    $PAGE->requires->jquery();
    $PAGE->requires->js(new moodle_url('/blocks/forumdashboard/script.js'));

    $notifications = $this->config && isset($this->config->replynotifications) && $this->config->replynotifications ? $this->get_notifications($returnurl) : [];

    $recentcourses = array_values(course_get_recent_courses($USER->id, 1));
    $recentcourseid = count($recentcourses) ? $recentcourses[0]->id : 0;

    $this->content = new stdClass();
    $this->content->text = $OUTPUT->render_from_template('block_forumdashboard/block', [
      'instanceid' => $instanceid,
      'content' => $this->get_items_content(),
      'courses' => $this->get_courses($recentcourseid),
      'expandable' => $this->expandable(),
      'notifications' => $notifications,
      'hasnotifications' => count($notifications) > 0,
      'dismissallurl' => $dismissallurl
    ]);

    return $this->content;
  }

  public function get_aria_role() {
    return 'navigation';
  }
}
