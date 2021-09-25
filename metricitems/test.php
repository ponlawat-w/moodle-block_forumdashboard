<?php

namespace block_forumdashboard\metricitems;

class test extends metricitem {

  public static $itemname = 'test';
  public static $nameidentifier = 'item_test';
  public static $valueidentifier = 'identifier_test';
  public static $default_bgcolor = '#ff0000';
  public static $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
