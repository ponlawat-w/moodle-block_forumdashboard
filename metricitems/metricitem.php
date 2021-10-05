<?php

namespace block_forumdashboard\metricitems;

use html_writer;

abstract class metricitem {

  public const SCOPE_SITE = 0;
  public const SCOPE_COURSE = 1;
  
  public static $itemname = null;
  public static $nameidentifer = null;
  public static $valueidentifier = null;
  public static $averagevalueidentifier = null;
  public static $default_bgcolor = null;
  public static $default_textcolor = null;

  protected static $valueidentifier_plural = null;
  protected static $averagevalueidentifier_plural = null;

  public $itemid;
  public $bgcolor;
  public $textcolor;
  public $showaverage;
  public $initial;

  public function __construct($instanceconfig) {
    $this->itemid = $instanceconfig->id;
    $this->bgcolor = $instanceconfig && isset($instanceconfig->bgcolor) ? $instanceconfig->bgcolor : static::$default_bgcolor;
    $this->textcolor = $instanceconfig && isset($instanceconfig->textcolor) ? $instanceconfig->textcolor : static::$default_textcolor;
    $this->showaverage = $instanceconfig && isset($instanceconfig->showaverage) ? $instanceconfig->showaverage : false;
    $this->initial = $instanceconfig && isset($instanceconfig->initial) ? $instanceconfig->initial : false;

    if (!static::$averagevalueidentifier) {
      static::$averagevalueidentifier = 'identifier_averagedefault';
    }

    $stringmanager = get_string_manager();

    $valueidentifier_plural = static::$valueidentifier . '_plural';
    static::$valueidentifier_plural = $stringmanager->string_exists($valueidentifier_plural, 'block_forumdashboard') ?
      $valueidentifier_plural : static::$valueidentifier;

    $averagevalueidentifier_plural = static::$averagevalueidentifier . '_plural';
    static::$averagevalueidentifier_plural = $stringmanager->string_exists($averagevalueidentifier_plural, 'block_forumdashboard') ?
      $averagevalueidentifier_plural : static::$averagevalueidentifier;
  }
  
  public abstract function get_value($scope, $userid);
  public abstract function get_average($scope);

  public function get_valuetext($value) {
    $identifier = $value == 1 ? static::$valueidentifier : static::$valueidentifier_plural;
    return get_string($identifier, 'block_forumdashboard', html_writer::tag('span', $value, ['class' => 'block_forumdashboard_valuenumber']));
  }

  public function get_averagetext($averagevalue) {
    $identifier = $averagevalue == 1 ? static::$averagevalueidentifier : static::$averagevalueidentifier_plural;
    $numbertext = number_format($averagevalue, 2, get_string('decsep', 'langconfig'), get_string('thousandssep', 'langconfig'));
    return get_string($identifier, 'block_forumdashboard', html_writer::tag('span', $numbertext, ['class' => 'block_forumdashboard_valuenumber']));
  }

  public function renderinitial() {
    global $OUTPUT;
    $data = [
      'itemid' => $this->itemid,
      'bgcolor' => $this->bgcolor,
      'textcolor' => $this->textcolor,
      'initial' => $this->initial,
      'showaverage' => $this->showaverage
    ];
    return $OUTPUT->render_from_template('block_forumdashboard/metricitem', $data);
  }
}
