<?php

namespace block_forumdashboard\metricitems;

class e3r extends engagement {

  public $itemname = 'e3r';
  public $nameidentifier = 'item_e3r';
  public $valueidentifier = 'identifier_e3r';
  public $default_bgcolor = '#95ed3b';
  public $default_textcolor = '#000000';

  public function calculatecronvalue($scope, $userid) {
    return $this->getcronlevels($scope, $userid)[0];
  }

  public function get_value($scope, $userid) {
    return static::getlevel($scope, $userid, 2);
  }

  public function get_average($scope) {
    return static::getlevelaverage($scope, 2);
  }
}
