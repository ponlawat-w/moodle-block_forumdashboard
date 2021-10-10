<?php

defined('MOODLE_INTERNAL') or die();

$messageproviders = [
  'newreply' => [
    'defaults' => [
      'popup' => MESSAGE_PERMITTED | MESSAGE_DEFAULT_LOGGEDIN | MESSAGE_DEFAULT_LOGGEDOFF,
      'email' => MESSAGE_PERMITTED
    ]
  ]
];
