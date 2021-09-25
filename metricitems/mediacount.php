<?php

namespace block_forumdashboard\metricitems;

class mediacount extends metricitem {

  public static $itemname = 'mediacount';
  public static $nameidentifier = 'item_mediacount';
  public static $valueidentifier = 'identifier_mediacount';
  public static $default_bgcolor = '#bfbfbf';
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
