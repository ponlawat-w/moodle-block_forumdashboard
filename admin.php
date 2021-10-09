<?php

include_once(__DIR__ . '/../../config.php');
include_once(__DIR__ . '/lib.php');

require_login();

$action = optional_param('action', 'configmetircitems', PARAM_TEXT);
if ($action == 'updatecaches') {
  require_capability('block/forumdashboard:runupdatecaches', context_system::instance());
  block_forumdashboard_executecron(false);
  redirect(new moodle_url('/admin/settings.php', ['section' => 'blocksettingforumdashboard']));
  exit;
}

require_capability('block/forumdashboard:configmetricitems', context_system::instance());

if (optional_param('block_forumdashboard_action', null, PARAM_TEXT) === 'submit') {
  $data = required_param('block_forumdashboard_configdata', PARAM_RAW);
  set_config('metricitems', $data, 'block_forumdashboard');
  redirect(new moodle_url('/admin/settings.php', ['section' => 'blocksettingforumdashboard']));
  exit;
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/forumdashboard/admin.php');
$PAGE->set_title(get_string('configmetricitems', 'block_forumdashboard'));
$PAGE->set_heading($PAGE->title);

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('/blocks/forumdashboard/admin.js'));
$PAGE->requires->strings_for_js(['confirmremove'], 'block_forumdashboard');

$PAGE->navbar
  ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
  ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
  ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
  ->add(get_string('pluginname', 'block_forumdashboard'), new moodle_url('/admin/settings.php', ['section' => 'blocksettingforumdashboard']))
  ->add(get_string('configmetricitems', 'block_forumdashboard'));

$metricitems = [];
foreach (block_forumdashboard_getclasses() as $class) {
  $immediateconfig = new stdClass();
  $immediateconfig->id = '';
  $immediateclassinstance = new $class($immediateconfig);
  array_push($metricitems, [
    'name' => $immediateclassinstance->itemname,
    'nameidentifier' => $immediateclassinstance->nameidentifier,
    'default_bgcolor' => $immediateclassinstance->default_bgcolor,
    'default_textcolor' => $immediateclassinstance->default_textcolor,
    'namestr' => get_string($immediateclassinstance->nameidentifier, 'block_forumdashboard')
  ]);
}

$pluginconfig = get_config('block_forumdashboard');

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_forumdashboard/admin', [
  'action' => new moodle_url('/blocks/forumdashboard/admin.php'),
  'cancel' => new moodle_url('/admin/settings.php', ['section' => 'blocksettingforumdashboard']),
  'metricitems' => $metricitems,
  'metricitems_json' => json_encode($metricitems),
  'config' => $pluginconfig && isset($pluginconfig->metricitems) ? $pluginconfig->metricitems : '[]'
]);

echo $OUTPUT->footer();
