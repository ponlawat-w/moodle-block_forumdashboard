<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


namespace block_forumdashboard\metricitems;

use html_writer;

/**
 * Prototype of a metric item
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class metricitem
{
    public const SCOPE_SITE = 0;
    public const SCOPE_COURSE = 1;

    /**
     * Name of metric item
     * 
     * @var string|null
     */
    public $itemname = null;

    /**
     * String identifier of item name
     * 
     * @var string|null
     */
    public $nameidentifer = null;

    /**
     * String identifier of item value display
     * 
     * @var string|null
     */
    public $valueidentifier = null;

    /**
     * String identifier of item average value display
     * 
     * @var string|null
     */
    public $averagevalueidentifier = null;

    /**
     * @var string|null
     */
    public $default_bgcolor = null;

    /**
     * @var string|null
     */
    public $default_textcolor = null;

    /**
     * @var string|null
     */
    public $globalsensitive = false;

    protected $valueidentifier_plural = null;
    protected $averagevalueidentifier_plural = null;

    /**
     * @var string
     */
    public $itemid;

    /**
     * @var string
     */
    public $bgcolor;

    /**
     * @var string
     */
    public $textcolor;

    /**
     * @var bool
     */
    public $showaverage;

    /**
     * @var bool
     */
    public $initial;

    /**
     * @var bool
     */
    public $caching;

    /**
     * Constructor
     *
     * @param object $instanceconfig
     */
    public function __construct($instanceconfig)
    {
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

    /**
     * Calculate and get item vlaue
     *
     * @param int|null $scope Course ID
     * @param int $userid
     * @return int
     */
    public abstract function get_value($scope, $userid);

    /**
     * Calculate and get item average value
     *
     * @param int|null $scope Course ID
     * @return double
     */
    public abstract function get_average($scope);

    /**
     * @param int|null $scope
     * @param int $userid
     * @return int
     */
    public function calculatecronvalue($scope, $userid)
    {
        return $this->get_value($scope, $userid);
    }

    /**
     * Get value for display, can be from caching or live calculation
     *
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_displayvalue($scope, $userid)
    {
        if ($this->caching) {
            global $DB;
            $record = ($scope || $this->globalsensitive) ?
                $DB->get_record('block_forumdashboard_caches', ['itemid' => $this->itemid, 'course' => $scope, 'userid' => $userid]) :
                $DB->get_record_sql('SELECT sum(value) value FROM {block_forumdashboard_caches} WHERE itemid = ? AND userid = ?', [$this->itemid, $userid]);
            return $record ? $record->value : null;
        }
        return $this->get_value($scope, $userid);
    }

    /**
     * Get average value for display, can be from caching or live calculation
     *
     * @param int $scope
     * @return double
     */
    public function get_displayaverage($scope)
    {
        if ($this->caching) {
            global $DB;
            $record = ($scope || $this->globalsensitive) ?
                $DB->get_record_sql('SELECT avg(value) averagevalue FROM {block_forumdashboard_caches} WHERE itemid = ? AND course = ?', [$this->itemid, $scope]) :
                $DB->get_record_sql('SELECT avg(value) averagevalue FROM {block_forumdashboard_caches} WHERE itemid = ?', [$this->itemid]);
            return $record ? $record->averagevalue : 0;
        }
        return $this->get_average($scope);
    }

    /**
     * Get stringified value with text
     *
     * @param int $value
     * @return string
     */
    public function get_valuetext($value)
    {
        $identifier = $value == 1 ? $this->valueidentifier : $this->valueidentifier_plural;
        return get_string($identifier, 'block_forumdashboard', html_writer::tag('span', $value, ['class' => 'block_forumdashboard_valuenumber']));
    }

    /**
     * Get stringified average value with text
     *
     * @param int $averagevalue
     * @return string
     */
    public function get_averagetext($averagevalue)
    {
        $identifier = $averagevalue == 1 ? $this->averagevalueidentifier : $this->averagevalueidentifier_plural;
        $numbertext = number_format($averagevalue, 2, get_string('decsep', 'langconfig'), get_string('thousandssep', 'langconfig'));
        return get_string($identifier, 'block_forumdashboard', html_writer::tag('span', $numbertext, ['class' => 'block_forumdashboard_valuenumber']));
    }

    /**
     * Render item value with template
     *
     * @return string
     */
    public function renderinitial()
    {
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
