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
 * The entry script which includes all necessary depedencies.
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once(__DIR__ . '/metricitem.php');

include_once(__DIR__ . '/engagement.php');
include_once(__DIR__ . '/postcountbase.php');
include_once(__DIR__ . '/textbase.php');

$BLOCK_FORUMDASHBOARD_METRICITEMS = [
    'discussioncount',
    'e1r',
    'e2r',
    'e3r',
    'e4r',
    'eavg',
    'emax',
    'mediacount',
    'participantcount',
    'postcount',
    'replycount',
    'test',
    'wordcount'
];

if (block_forumdashbaord_reactforuminstalled()) {
    $BLOCK_FORUMDASHBOARD_METRICITEMS[] = 'reactionsgiven';
    $BLOCK_FORUMDASHBOARD_METRICITEMS[] = 'reactionsreceived';
}

foreach ($BLOCK_FORUMDASHBOARD_METRICITEMS as $metricitem) {
    include_once(__DIR__ . '/' . $metricitem . '.php');
}

/**
 * Get metric item classes
 */
function block_forumdashboard_getclasses()
{
    global $BLOCK_FORUMDASHBOARD_METRICITEMS;

    return array_map(function ($name) {
        return 'block_forumdashboard\\metricitems\\' . $name;
    }, $BLOCK_FORUMDASHBOARD_METRICITEMS);
}
