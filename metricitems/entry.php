<?php

include_once(__DIR__ . '/metricitem.php');

include_once(__DIR__ . '/engagement.php');
include_once(__DIR__ . '/postcountbase.php');

const BLOCK_FORUMDASHBOARD_METRICITEMS = [
  'discussioncount',
  'e1r',
  'e2r',
  'e3r',
  'e4r',
  'mediacount',
  'participantcount',
  'postcount',
  'replycount',
  'test',
  'wordcount'
];

foreach (BLOCK_FORUMDASHBOARD_METRICITEMS as $metricitem) {
  include_once(__DIR__ . '/' . $metricitem . '.php');
}

function block_forumdashboard_getclasses() {
  return array_map(function($name) { return 'block_forumdashboard\\metricitems\\' . $name; }, BLOCK_FORUMDASHBOARD_METRICITEMS);
}
