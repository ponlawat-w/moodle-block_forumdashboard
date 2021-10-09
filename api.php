<?php

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/metricitems/entry.php');
require_once(__DIR__ . '/lib.php');

require_login();

$itemid = required_param('itemid', PARAM_TEXT);
$courseid = required_param('courseid', PARAM_INT);

$item = block_forumdashboard_getiteminstance($itemid);

if (!$item) {
  http_response_code(404);
  throw new moodle_exception('Item ' . $itemid . ' not found');
  exit;
}

$class = '\\block_forumdashboard\\metricitems\\' . $item->item;
$instance = new $class($item);

$result = new stdClass();
$result->itemid = $instance->itemid;
$result->value = $instance->get_value($courseid, $USER->id);
$result->valuetext = $instance->get_valuetext($result->value);
$result->average = $instance->get_average($courseid);
$result->averagetext = $instance->showaverage ? $instance->get_averagetext($result->average) : null;

echo json_encode($result);

exit;
