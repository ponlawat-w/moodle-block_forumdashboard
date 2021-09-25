<?php

namespace block_forumdashboard\metricitems;

class replycount extends metricitem {

  public static $itemname = 'replycount';
  public static $nameidentifier = 'item_replycount';
  public static $valueidentifier = 'identifier_replycount';
  public static $default_bgcolor = '#c21155';
  public static $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
