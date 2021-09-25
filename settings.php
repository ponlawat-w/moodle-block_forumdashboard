<?php
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
  $settings->add(
    new admin_setting_configempty(
      'block_forumdashboard/metricitems',
      get_string('configmetricitems', 'block_forumdashboard'),
      html_writer::link(
        new moodle_url('/blocks/forumdashboard/admin.php'),
        get_string('clicktoconfigmetricitems', 'block_forumdashboard')
      )
    )
  );
}
