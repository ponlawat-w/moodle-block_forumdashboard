<?php

namespace block_forumdashboard\metricitems;

class e1r extends metricitem {
  
  public static $itemname = 'e1r';
  public static $nameidentifier = 'item_e1r';
  public static $valueidentifier = 'identifier_e1r';
  public static $default_bgcolor = '#b3ff66';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
