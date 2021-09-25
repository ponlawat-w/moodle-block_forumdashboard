<?php

namespace block_forumdashboard\metricitems;

class wordcount extends metricitem {
  
  public static $itemname = 'wordcount';
  public static $nameidentifier = 'item_wordcount';
  public static $valueidentifier = 'identifier_wordcount';
  public static $default_bgcolor = '#a411c2';
  public static $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
