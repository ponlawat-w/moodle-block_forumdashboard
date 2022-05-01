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
 * Discussion counter
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussioncount extends postcountbase
{

    /**
     * @var string
     */
    public $itemname = 'discussioncount';

    /**
     * @var string
     */
    public $nameidentifier = 'item_discussioncount';

    /**
     * @var string
     */
    public $valueidentifier = 'identifier_discussioncount';

    /**
     * @var string
     */
    public $default_bgcolor = '#f58442';

    /**
     * @var string
     */
    public $default_textcolor = '#ffffff';

    /**
     * @return string[]
     */
    protected static function getadditionaljoins()
    {
        return [];
    }

    /**
     * @return array
     */
    protected static function getadditionaljoinparameters()
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected static function getadditionalconditions()
    {
        return ['posts.parent = 0'];
    }

    /**
     * @return array
     */
    protected static function getadditionalparameters()
    {
        return [];
    }
}
