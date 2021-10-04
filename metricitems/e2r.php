<?php

namespace block_forumdashboard\metricitems;

class e2r extends postcountbase {

  public static $itemname = 'e2r';
  public static $nameidentifier = 'item_e2r';
  public static $valueidentifier = 'identifier_e2r';
  public static $default_bgcolor = '#a2f54e';
  public static $default_textcolor = '#000000';

  protected static function getadditionaljoins() {
    return [
      'JOIN {forum_posts} parentposts1 ON posts.parent = parentposts1.id',
      'JOIN {forum_posts} parentposts2 ON parentposts1.parent = parentposts2.id AND parentposts2.parent = 0'
    ];
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
