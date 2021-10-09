<?php

include_once(__DIR__ . '/metricitem.php');

const BLOCK_FORUMDASHBOARD_METRICITEMS = [
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
