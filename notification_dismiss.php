<?php

include_once(__DIR__ . '/../../config.php');

require_login();

$id = required_param('id', PARAM_TEXT);
$returnurl = required_param('return', PARAM_TEXT);

$notifications = $DB->get_records('notifications',
  array_merge(['component' => 'block_forumdashboard', 'useridto' => $USER->id], $id == 'all' ? [] : ['id' => $id]));

foreach ($notifications as $notification) {
  \core_message\api::mark_notification_as_read($notification);
}
redirect($returnurl);
