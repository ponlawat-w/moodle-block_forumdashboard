<?php

namespace block_forumdashboard\metricitems;

class replycount extends postcountbase {

  public static $itemname = 'replycount';
  public static $nameidentifier = 'item_replycount';
  public static $valueidentifier = 'identifier_replycount';
  public static $default_bgcolor = '#c21155';
  public static $default_textcolor = '#ffffff';

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
