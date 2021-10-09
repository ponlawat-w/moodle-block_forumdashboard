<?php

namespace block_forumdashboard\metricitems;

class e4r extends engagement {
  
  public $itemname = 'e4r';
  public $nameidentifier = 'item_e4r';
  public $valueidentifier = 'identifier_e4r';
  public $default_bgcolor = '#86e327';
  public $default_textcolor = '#000000';

  public function calculatecronvalue($scope, $userid) {
    return $this->getcronlevels($scope, $userid)[0];
  }

  public function get_value($scope, $userid) {
    return static::getlevel($scope, $userid, 3);
  }

  public function get_average($scope) {
    return static::getlevelaverage($scope, 3);
  }
}
