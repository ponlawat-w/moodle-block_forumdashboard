<?php

namespace block_forumdashboard\metricitems;

class e3r extends metricitem {

  public static $itemname = 'e3r';
  public static $nameidentifier = 'item_e3r';
  public static $valueidentifier = 'identifier_e3r';
  public static $default_bgcolor = '#95ed3b';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
