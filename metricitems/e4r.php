<?php

namespace block_forumdashboard\metricitems;

class e4r extends engagement {
  
  public static $itemname = 'e4r';
  public static $nameidentifier = 'item_e4r';
  public static $valueidentifier = 'identifier_e4r';
  public static $default_bgcolor = '#86e327';
  public static $default_textcolor = '#000000';

  public function get_value($scope, $userid) {
    return static::getlevel($scope, $userid, 3);
  }

  public function get_average($scope) {
    return static::getlevelaverage($scope, 3);
  }
}
