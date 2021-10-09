<?php

namespace block_forumdashboard\metricitems;

use html_writer;

abstract class metricitem {

  public const SCOPE_SITE = 0;
  public const SCOPE_COURSE = 1;
  
  public $itemname = null;
  public $nameidentifer = null;
  public $valueidentifier = null;
  public $averagevalueidentifier = null;
  public $default_bgcolor = null;
  public $default_textcolor = null;
  public $globalsensitive = false;

  protected $valueidentifier_plural = null;
  protected $averagevalueidentifier_plural = null;

  public $itemid;
  public $bgcolor;
  public $textcolor;
  public $showaverage;
  public $initial;
  public $caching;

  public function __construct($instanceconfig) {
    $this->itemid = $instanceconfig->id;
    $this->bgcolor = $instanceconfig && isset($instanceconfig->bgcolor) ? $instanceconfig->bgcolor : $this->default_bgcolor;
    $this->textcolor = $instanceconfig && isset($instanceconfig->textcolor) ? $instanceconfig->textcolor : $this->default_textcolor;
    $this->showaverage = $instanceconfig && isset($instanceconfig->showaverage) ? $instanceconfig->showaverage : false;
    $this->initial = $instanceconfig && isset($instanceconfig->initial) ? $instanceconfig->initial : false;
    $this->caching = $instanceconfig && isset($instanceconfig->caching) ? $instanceconfig->caching : false;

    if (!$this->averagevalueidentifier) {
      $this->averagevalueidentifier = 'identifier_averagedefault';
    }

    $stringmanager = get_string_manager();

    $valueidentifier_plural = $this->valueidentifier . '_plural';
    $this->valueidentifier_plural = $stringmanager->string_exists($valueidentifier_plural, 'block_forumdashboard') ?
      $valueidentifier_plural : $this->valueidentifier;

    $averagevalueidentifier_plural = $this->averagevalueidentifier . '_plural';
    $this->averagevalueidentifier_plural = $stringmanager->string_exists($averagevalueidentifier_plural, 'block_forumdashboard') ?
      $averagevalueidentifier_plural : $this->averagevalueidentifier;
  }
  
  public abstract function get_value($scope, $userid);
  public abstract function get_average($scope);

  public function calculatecronvalue($scope, $userid) {
    return $this->get_value($scope, $userid);
  }

  public function get_displayvalue($scope, $userid) {
    if ($this->caching) {
      global $DB;
      $record = ($scope || $this->globalsensitive) ?
        $DB->get_record('block_forumdashboard_caches', ['itemid' => $this->itemid, 'course' => $scope, 'userid' => $userid]) :
        $DB->get_record_sql('SELECT sum(value) value FROM {block_forumdashboard_caches} WHERE itemid = ? AND userid = ?', [$this->itemid, $userid]);
      return $record ? $record->value : null;
    }
    return $this->get_value($scope, $userid);
  }

  public function get_displayaverage($scope) {
    if ($this->caching) {
      global $DB;
      $record = ($scope || $this->globalsensitive) ?
        $DB->get_record_sql('SELECT avg(value) averagevalue FROM {block_forumdashboard_caches} WHERE itemid = ? AND course = ?', [$this->itemid, $scope]) :
        $DB->get_record_sql('SELECT avg(value) averagevalue FROM {block_forumdashboard_caches} WHERE itemid = ?', [$this->itemid]);
      return $record ? $record->averagevalue : 0;
    }
    return $this->get_average($scope);
  }

  public function get_valuetext($value) {
    $identifier = $value == 1 ? $this->valueidentifier : $this->valueidentifier_plural;
    return get_string($identifier, 'block_forumdashboard', html_writer::tag('span', $value, ['class' => 'block_forumdashboard_valuenumber']));
  }

  public function get_averagetext($averagevalue) {
    $identifier = $averagevalue == 1 ? $this->averagevalueidentifier : $this->averagevalueidentifier_plural;
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
      'showaverage' => $this->showaverage,
      'caching' => $this->caching
    ];
    return $OUTPUT->render_from_template('block_forumdashboard/metricitem', $data);
  }
}
