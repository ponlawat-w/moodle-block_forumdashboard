<?php

namespace block_forumdashboard\metricitems;

class e4r extends metricitem {
  
  public static $itemname = 'e4r';
  public static $nameidentifier = 'item_e4r';
  public static $valueidentifier = 'identifier_e4r';
  public static $default_bgcolor = '#86e327';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
