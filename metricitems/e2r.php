<?php

namespace block_forumdashboard\metricitems;

class e2r extends metricitem {

  public static $itemname = 'e2r';
  public static $nameidentifier = 'item_e2r';
  public static $valueidentifier = 'identifier_e2r';
  public static $default_bgcolor = '#a2f54e';
  public static $default_textcolor = '#000000';

  public function __construct($instanceconfig) {
    parent::__construct($instanceconfig);
  }

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
