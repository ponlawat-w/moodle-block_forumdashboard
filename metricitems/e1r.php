<?php

namespace block_forumdashboard\metricitems;

class e1r extends postcountbase {
  
  public static $itemname = 'e1r';
  public static $nameidentifier = 'item_e1r';
  public static $valueidentifier = 'identifier_e1r';
  public static $default_bgcolor = '#b3ff66';
  public static $default_textcolor = '#000000';

  protected static function getadditionaljoins() {
    return ['JOIN {forum_posts} parentposts1 ON posts.parent = parentposts1.id AND parentposts1.parent = 0'];
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
