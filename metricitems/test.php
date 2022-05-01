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

/**
 * Test block
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test extends metricitem
{
    /**
     * @var string
     */
    public $itemname = 'test';

    /**
     * @var string
     */
    public $nameidentifier = 'item_test';

    /**
     * @var string
     */
    public $valueidentifier = 'identifier_test';

    /**
     * @var string
     */
    public $default_bgcolor = '#ff0000';

    /**
     * @var string
     */
    public $default_textcolor = '#ffffff';

    /**
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_value($scope, $userid)
    {
        return 0;
    }

    /**
     * @param int $scope
     * @return double
     */
    public function get_average($scope)
    {
        return 0;
    }
}
