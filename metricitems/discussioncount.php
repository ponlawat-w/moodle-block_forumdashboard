<?php

namespace block_forumdashboard\metricitems;

class discussioncount extends postcountbase {

  public static $itemname = 'discussioncount';
  public static $nameidentifier = 'item_discussioncount';
  public static $valueidentifier = 'identifier_discussioncount';
  public static $default_bgcolor = '#f58442';
  public static $default_textcolor = '#ffffff';

  protected static function getadditionaljoins() {
    return [];
  }
  
  protected static function getadditionaljoinparameters() {
    return [];
  }

  protected static function getadditionalconditions() {
    return ['posts.parent = 0'];
  }

  protected static function getadditionalparameters() {
    return [];
  }
}
