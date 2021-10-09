<?php

namespace block_forumdashboard\metricitems;

class postcount extends postcountbase {
  
  public static $itemname = 'postcount';
  public static $nameidentifier = 'item_postcount';
  public static $valueidentifier = 'identifier_postcount';
  public static $default_bgcolor = '#11a7c2';
  public static $default_textcolor = '#ffffff';

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
