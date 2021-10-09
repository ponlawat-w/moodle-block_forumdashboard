<?php

namespace block_forumdashboard\task;

include_once(__DIR__ . '/../../lib.php');

class calculate_task extends \core\task\scheduled_task {
  public function get_name() {
    return get_string('calculate_task', 'block_forumdashboard');
  }

  public function execute() {
    $scheduletime = block_forumdashboard_getnextcronschedule();

    if (!$scheduletime) {
      mtrace('...... No schedule');
      return true;
    }

    if (time() >= $scheduletime) {
      block_forumdashboard_executecron(true);
    } else {
      mtrace('...... Skipped');
    }

    return true;
  }
}
