<?php

defined('MOODLE_INTERNAL') or die();

$observers = [
  [
    'eventname' => '\mod_forum\event\post_created',
    'callback' => 'block_forumdashboard_observer::forum_post_created'
  ]
];
