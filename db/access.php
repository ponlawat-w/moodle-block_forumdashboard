<?php

$capabilities = [
  'block/forumdashboard:myaddinstance' => [
    'riskbitmask' => RISK_PERSONAL,
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archtypes' => []
  ],
  'block/forumdashboard:configmetricitems' => [
    'riskbitmask' => RISK_CONFIG,
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archetypes' => [
      'manager' => CAP_ALLOW
    ]
  ]
];
