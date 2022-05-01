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
 * A page when a user dismiss a notification
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once(__DIR__ . '/../../config.php');

require_login();

$id = required_param('id', PARAM_TEXT);
$returnurl = required_param('return', PARAM_TEXT);

$notifications = $DB->get_records(
    'notifications',
    array_merge(['component' => 'block_forumdashboard', 'useridto' => $USER->id], $id == 'all' ? [] : ['id' => $id])
);

foreach ($notifications as $notification) {
    \core_message\api::mark_notification_as_read($notification);
}
redirect($returnurl);
