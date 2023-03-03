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
 * Administrator's settings
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/engagement.php');

if ($ADMIN->fulltree) {
    $config = get_config('block_forumdashboard');
    $lastcalculated = $config && isset($config->lastcalculated) ? userdate($config->lastcalculated, get_string('strftimedatetime', 'langconfig')) : 'N/A';
    $nextscheduletime = block_forumdashboard_getnextcronschedule();
    $nextscheduletimetext = $nextscheduletime ? userdate($nextscheduletime, get_string('strftimedatetime', 'langconfig')) : 'N/A';

    $settings->add(
        new admin_setting_configempty(
            'block_forumdashboard/metricitems_empty',
            get_string('configmetricitems', 'block_forumdashboard'),
            html_writer::link(
                new moodle_url('/blocks/forumdashboard/admin.php'),
                get_string('clicktoconfigmetricitems', 'block_forumdashboard')
            )
        )
    );

    $settings->add(
        new admin_setting_configempty(
            'block_forumdashboard/cachingschedule_empty',
            get_string('cachingschedule', 'block_forumdashboard'),
            html_writer::div(get_string('lastupdated', 'block_forumdashboard', $lastcalculated)) .
                html_writer::div(get_string('nextschedule', 'block_forumdashboard', $nextscheduletimetext)) .
                html_writer::link(
                    new moodle_url('/blocks/forumdashboard/admin.php', ['action' => 'updatecaches']),
                    get_string('updatecaches', 'block_forumdashboard')
                )
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'block_forumdashboard/cachingtime',
            get_string('cachingtime', 'block_forumdashboard'),
            get_string('cachingtime_description', 'block_forumdashboard'),
            '7,19',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_forumdashboard/replynotifications',
            get_string('replynotifications', 'block_forumdashboard'),
            get_string('replynotifications_description', 'block_forumdashboard'),
            '1',
            '1',
            '0'
        )
    );

    $settings->add(
        new admin_setting_configselect(
            'block_forumdashboard/defaultengagementmethod',
            get_string('engagement_admin_defaultmethod', 'block_forumdashboard'),
            get_string('engagement_method_help', 'block_forumdashboard'),
            \block_forumdashboard_engagement\engagement::THREAD_ENGAGEMENT, \block_forumdashboard_engagement\engagement::getselectoptions()
        )
    );
}
