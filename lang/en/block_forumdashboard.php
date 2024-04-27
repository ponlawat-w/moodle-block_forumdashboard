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
 * String identifiers
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Forum Summary Dashboard';
$string['loading'] = 'Loading…';
$string['configmetricitems'] = 'Configure metric items';
$string['clicktoconfigmetricitems'] = 'Click here to configure metric items';

$string['forumdashboard:myaddinstance'] = 'Add an instance';
$string['forumdashboard:configmetricitems'] = 'Configure metric items';
$string['forumdashboard:runupdatecaches'] = 'Run update caches manually';
$string['messageprovider:newreply'] = 'Notification of new reply';

$string['calculate_task'] = 'Calculation Caching Task';

$string['blocktitle'] = 'Forum Dashboard';
$string['viewsummaryof'] = 'View summary of: ';
$string['entiresite'] = 'Entire Site';
$string['viewcourse'] = 'Course: {$a}';
$string['viewmore'] = 'View more…';
$string['dismissall'] = 'Dismiss all';

$string['initial'] = 'Initial display';
$string['average'] = 'Include average';
$string['caching'] = 'Caching value';
$string['bgcolor'] = 'Background color';
$string['textcolor'] = 'Text color';
$string['addmetricitem'] = 'Add a new item';

$string['identifier_averagedefault'] = 'Average: {$a}';
$string['notavailable'] = 'Data not available';
$string['now'] = 'now';
$string['lastupdated'] = '<strong>Last updated:</strong> {$a}';
$string['nextschedule'] = '<strong>Next schedule:</strong> {$a}';
$string['cachingschedule'] = 'Caching schedule';
$string['cachingtime'] = 'Caching time of the day';
$string['cachingtime_description'] = 'Caching time of the day (separated with comma) For example "7,19" will be 07:00 and 19:00<br>※ at server time';
$string['updatecaches'] = 'Click here to update caches now';
$string['replynotifications'] = 'Enable reply notifications';
$string['replynotifications_description'] = 'If checked, notifications of recent replies to the user will be sent and shown in dashboard';

$string['confirmremove'] = 'Are you sure you want to remove this item?';
$string['item_discussioncount'] = 'Discussion count';
$string['identifier_discussioncount'] = '{$a} discussion';
$string['identifier_discussioncount_plural'] = '{$a} discussions';
$string['item_e1r'] = '1st engagement';
$string['identifier_e1r'] = '{$a} 1<sup>st</sup> engagement';
$string['identifier_e1r_plural'] = '{$a} 1<sup>st</sup> engagements';
$string['item_e2r'] = '2nd engagement';
$string['identifier_e2r'] = '{$a} 2<sup>nd</sup> engagement';
$string['identifier_e2r_plural'] = '{$a} 2<sup>nd</sup> engagements';
$string['item_e3r'] = '3rd engagement';
$string['identifier_e3r'] = '{$a} 3<sup>rd</sup> engagement';
$string['identifier_e3r_plural'] = '{$a} 3<sup>rd</sup> engagements';
$string['item_e4r'] = '4th+ engagement';
$string['identifier_e4r'] = '{$a} 4<sup>th</sup>+ engagement';
$string['identifier_e4r_plural'] = '{$a} 4<sup>th</sup>+ engagements';
$string['item_eavg'] = 'Average engagement';
$string['identifier_eavg'] = '{$a} average engagement level';
$string['identifier_eavg_plural'] = '{$a} average engagement level';
$string['item_emax'] = 'Maximum engagement';
$string['identifier_emax'] = '{$a} maximum engagement level';
$string['identifier_emax_plural'] = '{$a} maximum engagement level';
$string['item_mediacount'] = 'Media count';
$string['identifier_mediacount'] = '{$a} media';
$string['identifier_mediacount_plural'] = '{$a} medias';
$string['item_participantcount'] = 'Participants count';
$string['identifier_participantcount'] = '{$a} participant';
$string['identifier_participantcount_plural'] = '{$a} participants';
$string['item_postcount'] = 'Posts count';
$string['identifier_postcount'] = '{$a} post';
$string['identifier_postcount_plural'] = '{$a} posts';
$string['item_replycount'] = 'Replies count';
$string['identifier_replycount'] = '{$a} reply';
$string['identifier_replycount_plural'] = '{$a} replies';
$string['item_test'] = 'Test';
$string['identifier_test'] = 'Test';
$string['item_wordcount'] = 'Words count';
$string['identifier_wordcount'] = '{$a} word';
$string['identifier_wordcount_plural'] = '{$a} words';
$string['item_reactionsgiven'] = 'Reactions given';
$string['identifier_reactionsgiven'] = '{$a} reaction given';
$string['identifier_reactionsgiven_plural'] = '{$a} reactions given';
$string['item_reactionsreceived'] = 'Reactions received';
$string['identifier_reactionsreceived'] = '{$a} reaction received';
$string['identifier_reactionsreceived_plural'] = '{$a} reactions received';

$string['notification_newreply'] = 'New reply to your post';
$string['notification_newreplyin'] = 'New reply from {$a->fromuser} to your message in: {$a->discussionname}';
$string['notification_linktopost'] = 'Post';

$string['engagement_method'] = 'Engagement Method';
$string['engagement_method_help'] = '<p>Engagement Calculation Method</p><strong>Person-to-Person Engagement:</strong> The engagement level increases each time a user replies to the same user in the same thread.<br><strong>Thread Total Count Engagement:</strong> The engagement level increases each time a user participate in the same thread.<br><strong>Thread Engagement:</strong> The engagement level increases each time a user participates in a reply where they already participated in the parent posts.';
$string['engagement_persontoperson'] = 'Person-to-Person Engagement';
$string['engagement_persontoperson_description'] = 'The engagement level increases each time a user replies to the same user in the same thread.';
$string['engagement_threadtotalcount'] = 'Thread Total Count Engagement';
$string['engagement_threadtotalcount_description'] = 'The engagement level increases each time a user participate in the same thread.';
$string['engagement_threadengagement'] = 'Thread Engagement';
$string['engagement_threadengagement_description'] = 'The engagement level increases each time a user participates in a reply where they already participated in the parent posts.';

$string['engagement_admin_defaultmethod'] = 'Default Engagement Calculation Method';
$string['engagement_admin_international'] = 'International Engagement Only';
$string['engagement_admin_international_description'] = 'If true, engagement value increases only the interaction are from users with different country.';

$string['engagement_international'] = 'International engagement only';
