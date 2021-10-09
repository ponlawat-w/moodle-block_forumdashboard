<?php

namespace block_forumdashboard\metricitems;

class discussioncount extends postcountbase {

  public $itemname = 'discussioncount';
  public $nameidentifier = 'item_discussioncount';
  public $valueidentifier = 'identifier_discussioncount';
  public $default_bgcolor = '#f58442';
  public $default_textcolor = '#ffffff';

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
