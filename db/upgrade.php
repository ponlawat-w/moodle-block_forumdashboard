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

/**
 * Version upgrading
 * 
 * @package block_forumdashboard
 * @copyright 2022 Ponlawat Weerapanpisit
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

function xmldb_block_forumdashboard_upgrade($oldversion)
{
    global $DB;

    $dbmanager = $DB->get_manager();

    if ($oldversion < 2021100903) {
        $table = new xmldb_table('block_forumdashboard_caches');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('itemid', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, 'itemid');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, 'course');
        $table->add_field('calculated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, 'userid');
        $table->add_field('value', XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, null, 'calculated');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('itemid_idx', XMLDB_INDEX_NOTUNIQUE, ['itemid']);
        $table->add_index('course_idx', XMLDB_INDEX_NOTUNIQUE, ['course']);
        $table->add_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        if ($dbmanager->table_exists($table)) {
            $dbmanager->drop_table($table);
        }
        $dbmanager->create_table($table);
        if (!$dbmanager->table_exists($table)) {
            throw new moodle_exception('Table is not created');
        }

        upgrade_block_savepoint(true, 2021100903, 'forumdashboard');
    }

    return true;
}
