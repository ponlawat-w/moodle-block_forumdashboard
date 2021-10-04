<?php

namespace block_forumdashboard\metricitems;

class e3r extends postcountbase {

  public static $itemname = 'e3r';
  public static $nameidentifier = 'item_e3r';
  public static $valueidentifier = 'identifier_e3r';
  public static $default_bgcolor = '#95ed3b';
  public static $default_textcolor = '#000000';

  protected static function getadditionaljoins() {
    return [
      'JOIN {forum_posts} parentposts1 ON posts.parent = parentposts1.id',
      'JOIN {forum_posts} parentposts2 ON parentposts1.parent = parentposts2.id',
      'JOIN {forum_posts} parentposts3 ON parentposts2.parent = parentposts3.id AND parentposts3.parent = 0'
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
