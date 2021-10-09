<?php

namespace block_forumdashboard\metricitems;

class postcount extends postcountbase {
  
  public $itemname = 'postcount';
  public $nameidentifier = 'item_postcount';
  public $valueidentifier = 'identifier_postcount';
  public $default_bgcolor = '#11a7c2';
  public $default_textcolor = '#ffffff';

  protected static function getadditionaljoins() {
    return [];
  }
  
  protected static function getadditionaljoinparameters() {
    return [];
  }

  protected static function getadditionalconditions() {
    return [];
  }

  protected static function getadditionalparameters() {
    return [];
  }
}
