<?php

namespace block_forumdashboard\metricitems;

class participantcount extends metricitem {
  
  public static $itemname = 'participantcount';
  public static $nameidentifier = 'item_participantcount';
  public static $valueidentifier = 'identifier_participantcount';
  public static $default_bgcolor = '#3bad6e';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
