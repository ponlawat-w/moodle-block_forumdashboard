<?php

namespace block_forumdashboard\metricitems;

class test extends metricitem {

  public $itemname = 'test';
  public $nameidentifier = 'item_test';
  public $valueidentifier = 'identifier_test';
  public $default_bgcolor = '#ff0000';
  public $default_textcolor = '#ffffff';

  public function get_value($scope, $userid) {
    return 0;
  }

  public function get_average($scope) {
    return 0;
  }
}
