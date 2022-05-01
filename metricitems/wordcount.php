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

use context_course;

/**
 * Word count
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wordcount extends textbase
{
    /**
     * @var string
     */
    public $itemname = 'wordcount';

    /**
     * @var string
     */
    public $nameidentifier = 'item_wordcount';

    /**
     * @var string
     */
    public $valueidentifier = 'identifier_wordcount';

    /**
     * @var string
     */
    public $default_bgcolor = '#a411c2';

    /**
     * @var string
     */
    public $default_textcolor = '#ffffff';

    /**
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function calculatecronvalue($scope, $userid)
    {
        $wordcount = 0;
        $msgrecords = static::get_cronmessagerecords($scope, $userid);
        foreach ($msgrecords as $msgrecord) {
            $wordcount += count_words($msgrecord->message);
        }

        return $wordcount;
    }

    /**
     * @param int $scope
     * @param int $userid
     * @return int
     */
    public function get_value($scope, $userid)
    {
        $wordcount = 0;
        $msgrecords = static::get_messagerecords($scope, $userid);
        foreach ($msgrecords as $msgrecord) {
            $wordcount += count_words($msgrecord->message);
        }

        return $wordcount;
    }

    /**
     * @param int $scope
     * @return double
     */
    public function get_average($scope)
    {
        $users = $scope ? get_enrolled_users(context_course::instance($scope)) : get_users();
        $sum = 0;
        foreach ($users as $user) {
            $sum += $this->get_value($scope, $user->id);
        }

        return $sum / count($users);
    }
}
