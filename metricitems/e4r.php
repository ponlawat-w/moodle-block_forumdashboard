<?php

namespace block_forumdashboard\metricitems;

class e4r extends postcountbase {
  
  public static $itemname = 'e4r';
  public static $nameidentifier = 'item_e4r';
  public static $valueidentifier = 'identifier_e4r';
  public static $default_bgcolor = '#86e327';
  public static $default_textcolor = '#000000';

  protected static function getadditionaljoins() {
    return [
      'JOIN {forum_posts} parentposts1 ON posts.parent = parentposts1.id',
      'JOIN {forum_posts} parentposts2 ON parentposts1.parent = parentposts2.id',
      'JOIN {forum_posts} parentposts3 ON parentposts2.parent = parentposts3.id',
      'JOIN {forum_posts} parentposts4 ON parentposts3.parent = parentposts4.id AND parentposts4.parent = 0'
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
