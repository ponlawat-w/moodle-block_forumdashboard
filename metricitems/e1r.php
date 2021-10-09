<?php

namespace block_forumdashboard\metricitems;

class e1r extends engagement {
  
  public $itemname = 'e1r';
  public $nameidentifier = 'item_e1r';
  public $valueidentifier = 'identifier_e1r';
  public $default_bgcolor = '#b3ff66';
  public $default_textcolor = '#000000';

  public function calculatecronvalue($scope, $userid) {
    return $this->getcronlevels($scope, $userid)[0];
  }

  public function get_value($scope, $userid) {
    return static::getlevel($scope, $userid, 0);
  }

  public function get_average($scope) {
    return static::getlevelaverage($scope, 0);
  }
}
