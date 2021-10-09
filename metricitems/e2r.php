<?php

namespace block_forumdashboard\metricitems;

class e2r extends engagement {

  public $itemname = 'e2r';
  public $nameidentifier = 'item_e2r';
  public $valueidentifier = 'identifier_e2r';
  public $default_bgcolor = '#a2f54e';
  public $default_textcolor = '#000000';

  public function calculatecronvalue($scope, $userid) {
    return $this->getcronlevels($scope, $userid)[0];
  }

  public function get_value($scope, $userid) {
    return static::getlevel($scope, $userid, 1);
  }

  public function get_average($scope) {
    return static::getlevelaverage($scope, 1);
  }
}
