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
 * Functions library
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once(__DIR__ . '/metricitems/entry.php');

/**
 * Get metric item object from an ID
 *
 * @param string $itemid
 * @return object|null
 */
function block_forumdashboard_getiteminstance($itemid)
{
    $config = get_config('block_forumdashboard');
    if (!isset($config->metricitems)) {
        return null;
    }
    $items = json_decode($config->metricitems);

    foreach ($items as $item) {
        if ($item->id == $itemid) {
            return $item;
        }
    }

    return null;
}

/**
 * Get all item instances being according class object
 *
 * @return \block_forumdashboard\metricitems\metricitem[]
 */
function block_forumdashboard_getiteminstances()
{
    $items = [];
    $config = get_config('block_forumdashboard');
    $iteminstances = $config && isset($config->metricitems) ? json_decode($config->metricitems) : [];

    foreach ($iteminstances as $iteminstance) {
        $class = '\\block_forumdashboard\\metricitems\\' . $iteminstance->item;
        array_push($items, new $class($iteminstance));
    }

    return $items;
}

/**
 * Execute cron script
 *
 * @param boolean $verbose
 * @return boolean
 */
function block_forumdashboard_executecron($verbose = true)
{
    global $DB;

    $DB->delete_records('block_forumdashboard_caches', []);

    $courses = get_courses();
    $courseids = array_merge([0], array_map(function ($course) {
        return $course->id;
    }, $courses));

    $items = block_forumdashboard_getiteminstances();

    foreach ($courseids as $courseid) {
        $verbose && mtrace("...... Executing course ID: {$courseid}...");
        $users = $courseid ? get_enrolled_users(context_course::instance($courseid)) : $DB->get_records('user');
        $itemcount = 0;
        foreach ($items as $item) {
            if ($courseid == 0 && !$item->globalsensitive) {
                continue;
            }
            foreach ($users as $user) {
                $record = new stdClass();
                $record->id = 0;
                $record->itemid = $item->itemid;
                $record->course = $courseid;
                $record->userid = $user->id;
                $record->calculated = time();
                $record->value = $item->get_value($courseid, $user->id);
                $DB->insert_record('block_forumdashboard_caches', $record);
            }
            $itemcount++;
        }
        $verbose && mtrace('......... ' . count($users) . ' user(s) and ' . $itemcount . ' metric items.');
    }

    $verbose && mtrace('...... Done');

    set_config('lastcalculated', time(), 'block_forumdashboard');
    return true;
}

/**
 * Get timestamp of next cron task scheduled time
 *
 * @return int|null
 */
function block_forumdashboard_getnextcronschedule()
{
    $config = get_config('block_forumdashboard');
    $lastcalculated = $config && isset($config->lastcalculated) ? $config->lastcalculated : 0;
    $times = array_filter(explode(',', $config && isset($config->cachingtime) ? $config->cachingtime : ''), function ($x) {
        return is_numeric($x) && $x >= 0 && $x <= 23;
    });
    if (!count($times)) {
        return null;
    }
    sort($times, SORT_NUMERIC);

    foreach ($times as $time) {
        $schduletime = mktime($time, 0, 0);
        if ($lastcalculated < $schduletime) {
            return $schduletime;
        }
    }

    return mktime($times[0] + 24, 0, 0);
}

/**
 * Notify a user
 *
 * @param object $post Source (Triggerer)
 * @param object $posttonotify Target (Receiver)
 * @return void
 */
function block_forumdashboard_notifyuser($post, $posttonotify)
{
    global $DB;
    $stringmanager = get_string_manager();
    $discussion = $DB->get_record('forum_discussions', ['id' => $post->discussion]);
    $fromuser = core_user::get_user($post->userid);
    $targetuser = core_user::get_user($posttonotify->userid);
    $targetlang = $targetuser->lang;
    $url = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id], 'p' . $post->id);

    $message = new \core\message\message();
    $message->component = 'block_forumdashboard';
    $message->name = 'newreply';
    $message->userfrom = core_user::get_user($post->userid);
    $message->userto = $targetuser;
    $subjectstrdata = new stdClass();
    $subjectstrdata->fromuser = fullname($fromuser);
    $subjectstrdata->discussionname = $discussion->name;
    $message->subject = $stringmanager->get_string('notification_newreplyin', 'block_forumdashboard', $subjectstrdata, $targetlang);
    $message->fullmessage = $message->subject;
    $message->fullmessageformat = FORMAT_HTML;
    $message->fullmessagehtml = html_writer::link($url, $post->subject);
    $message->smallmessage = $stringmanager->get_string('notification_newreply', 'block_forumdashboard', null, $targetlang);
    $message->notification = 1;
    $message->contexturl = $url;
    $message->contexturlname = $stringmanager->get_string('notification_linktopost', 'block_forumdashboard', null, $targetlang);
    $message->customdata = intval($discussion->course);
    message_send($message);
}

/**
 * Get notifications of current user
 *
 * @return object[]
 */
function block_forumdashboard_getmynotifications()
{
    global $USER, $DB;
    return $DB->get_records('notifications', ['component' => 'block_forumdashboard', 'useridto' => $USER->id, 'timeread' => NULL], 'timecreated desc');
}

/**
 * Count multimedia with methodologies from report_discussion_metrics plugin
 *
 * @param string $text
 * @return object
 */
function report_discussion_metrics_get_mulutimedia_num($text)
{
    global $CFG, $PAGE;

    if (!is_string($text) or empty($text)) {
        // non string data can not be filtered anyway
        return 0;
    }

    if (stripos($text, '</a>') === false && stripos($text, '</video>') === false && stripos($text, '</audio>') === false && (stripos($text, '<img') === false)) {
        // Performance shortcut - if there are no </a>, </video> or </audio> tags, nothing can match.
        return 0;
    }

    // Looking for tags.
    $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $count = new stdClass;
    $count->num = 0;
    $count->img = 0;
    $count->video = 0;
    $count->audio = 0;
    $count->link = 0;
    if (!$matches) {
        return 0;
    } else {
        // Regex to find media extensions in an <a> tag.
        $embedmarkers = core_media_manager::instance()->get_embeddable_markers();
        $re = '~<a\s[^>]*href="([^"]*(?:' .  $embedmarkers . ')[^"]*)"[^>]*>([^>]*)</a>~is';
        $tagname = '';
        foreach ($matches as $idx => $tag) {
            if (preg_match('/<(a|img|video|audio)\s[^>]*/', $tag, $tagmatches)) {
                $tagname = strtolower($tagmatches[1]);
                if ($tagname === "a" && preg_match($re, $tag)) {
                    $count->num++;
                    $count->link++;
                } else {
                    if ($tagname == "img") {
                        $count->img++;
                    } elseif ($tagname == "video") {
                        $count->video++;
                    } elseif ($tagname == "audio") {
                        $count->audio++;
                    }
                    $count->num++;
                }
            }
        }
    }
    return $count;
}
