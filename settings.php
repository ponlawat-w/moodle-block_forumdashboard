<?php
defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');

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
      '7,19', PARAM_TEXT
    )
  );
}
