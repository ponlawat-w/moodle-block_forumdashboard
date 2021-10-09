<?php

defined('MOODLE_INTERNAL') or die();

$tasks = [
  [
    'classname' => 'block_forumdashboard\\task\\calculate_task',
    'blocking' => 0,
    'minute' => '*',
    'hour' => '*',
    'day' => '*',
    'month' => '*',
    'dayofweek' => '*'
  ]
];
