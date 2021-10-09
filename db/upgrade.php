<?php
defined('MOODLE_INTERNAL') or die();

function xmldb_block_forumdashboard_upgrade($oldversion) {
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
