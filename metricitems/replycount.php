<?php

namespace block_forumdashboard\metricitems;

class replycount extends postcountbase {

  public $itemname = 'replycount';
  public $nameidentifier = 'item_replycount';
  public $valueidentifier = 'identifier_replycount';
  public $default_bgcolor = '#c21155';
  public $default_textcolor = '#ffffff';

  protected static function getadditionaljoins() {
    return [];
  }
  
  protected static function getadditionaljoinparameters() {
    return [];
  }

  protected static function getadditionalconditions() {
    return ['posts.parent > 0'];
  }

  protected static function getadditionalparameters() {
    return [];
  }
}
