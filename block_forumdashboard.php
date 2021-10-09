<?php

defined('MOODLE_INTERNAL') || die();

include_once(__DIR__ . '/metricitems/entry.php');
include_once(__DIR__ . '/lib.php');

class block_forumdashboard extends block_base {
  public $items = [];

  public function __construct() {
    parent::__construct();

    $config = get_config('block_forumdashboard');
    $iteminstances = $config && isset($config->metricitems) ? json_decode($config->metricitems) : [];

    foreach ($iteminstances as $iteminstance) {
      $class = '\\block_forumdashboard\\metricitems\\' . $iteminstance->item;
      array_push($this->items, new $class($iteminstance));
    }
  }

  public function init() {
    $this->title = get_string('blocktitle', 'block_forumdashboard');
    $this->version = 2021092403;
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

  private function get_courses() {
    $courses = [];
    $mycourses = enrol_get_my_courses();
    foreach ($mycourses as $mycourse) {
      array_push($courses, [
        'id' => $mycourse->id,
        'fullname' => $mycourse->fullname
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

  public function get_content() {
    global $OUTPUT, $PAGE;

    $PAGE->requires->jquery();
    $PAGE->requires->js(new moodle_url('/blocks/forumdashboard/script.js'));

    $this->content = new stdClass();
    $this->content->text = $OUTPUT->render_from_template('block_forumdashboard/block', [
      'content' => $this->get_items_content(),
      'courses' => $this->get_courses(),
      'expandable' => $this->expandable()
    ]);

    return $this->content;
  }

  public function get_aria_role() {
    return 'navigation';
  }
}
