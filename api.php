<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Dashboard API
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_login();

$action = required_param('action', PARAM_TEXT);

if ($action === 'get') {
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
    $result->value = $instance->get_displayvalue($courseid, $USER->id);
    $result->valuetext = $instance->get_valuetext($result->value);
    $result->average = $instance->showaverage ? $instance->get_displayaverage($courseid) : null;
    $result->averagetext = $instance->showaverage ? $instance->get_averagetext($result->average) : null;

    echo json_encode($result);
    exit;
} else if ($action === 'getcached') {
    $courseid = required_param('courseid', PARAM_INT);

    $results = [];
    $items = array_filter(block_forumdashboard_getiteminstances(), function ($item) {
        return $item->caching;
    });

    foreach ($items as $item) {
        $recordresult = new stdClass();
        $recordresult->itemid = $item->itemid;
        $recordresult->value = $item->get_displayvalue($courseid, $USER->id);
        $recordresult->valuetext = $item->get_valuetext($recordresult->value);
        $recordresult->average = $item->showaverage ? $item->get_displayaverage($courseid) : null;
        $recordresult->averagetext = $item->showaverage ? $item->get_averagetext($recordresult->average) : null;
        array_push($results, $recordresult);
    }

    $config = get_config('block_forumdashboard');
    $lastcalculated = $config && isset($config->lastcalculated) ? userdate($config->lastcalculated, get_string('strftimedatetimeshort', 'langconfig')) : 'N/A';

    $apiresult = new stdClass();
    $apiresult->lastcalculatedtext = get_string('lastupdated', 'block_forumdashboard', $lastcalculated);
    $apiresult->items = $results;

    echo json_encode($apiresult);
    exit;
}
