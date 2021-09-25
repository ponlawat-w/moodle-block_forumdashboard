<?php

namespace block_forumdashboard\metricitems;

class postcount extends metricitem {
  
  public static $itemname = 'postcount';
  public static $nameidentifier = 'item_postcount';
  public static $valueidentifier = 'identifier_postcount';
  public static $default_bgcolor = '#11a7c2';
  public static $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
