<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism_originality upgrade
 *
 * @package    plagiarism_originality
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade function for the plagiarism_originality plugin.
 * This function performs the necessary database upgrades based on the old version of the plugin.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool Returns true if the upgrade is successful, false otherwise.
 */
function xmldb_plagiarism_originality_upgrade($oldversion = 0) {
    global $DB, $CFG;
    $dbman = $DB->get_manager();

    if ($oldversion < 2023070500) {

        $accesskey = get_config('plagiarism_originality', 'originality_key');
        set_config('secret', $accesskey, 'plagiarism_originality');

        $table = new xmldb_table('plagiarism_originality_req');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('plagiarism_originality_resp');
        if ($dbman->table_exists($table)) {

            $field = new xmldb_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('ghostwriter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('file_report', XMLDB_TYPE_TEXT, 'medium', null, null, null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('attempts', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('created', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('updated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('objectid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $field = new xmldb_field('parent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $newtable = new xmldb_table('originality_submissions');
            if (!$dbman->table_exists($newtable)) {
                $dbman->rename_table($table, 'originality_submissions');
            }
        }

        $table = new xmldb_table('originality_groups');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, 0);
            $table->add_field('assignment', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            $table->add_field('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $dbman->create_table($table);
        }

        $table = new xmldb_table('plagiarism_originality_cnf');
        if ($dbman->table_exists($table)) {

            // Conditionally launch drop field name.
            $field = new xmldb_field('name');
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }

            // Check if 'value' field exists before renaming to 'ischeck'
            $valuefield = new xmldb_field('value');
            $ischeckfield = new xmldb_field('ischeck');

            if ($dbman->field_exists($table, $valuefield) && !$dbman->field_exists($table, $ischeckfield)) {
                // Rename field value to ischeck with proper type definition
                $newfield = new xmldb_field('ischeck', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0);
                $dbman->rename_field($table, $valuefield, 'ischeck');
                // Ensure the field has the correct definition
                $dbman->change_field_type($table, $newfield);
            } else if (!$dbman->field_exists($table, $ischeckfield)) {
                // Add ischeck field if it doesn't exist
                $newfield = new xmldb_field('ischeck', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0);
                $dbman->add_field($table, $newfield);
            }

            // Conditionally launch add field ischeckgw.
            $field = new xmldb_field('ischeckgw', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, 0, 'ischeck');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $newtable = new xmldb_table('plagiarism_originality_mod');
            if (!$dbman->table_exists($newtable)) {
                $dbman->rename_table($table, 'plagiarism_originality_mod');
            }
        }

        // Define table originality_submissions.
        $table = new xmldb_table('originality_submissions');
        if ($dbman->table_exists($table)) {

            // Conditionally launch add field cm.
            $field = new xmldb_field('cm', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            // Conditionally launch add field docid.
            $field = new xmldb_field('docid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            // Launch rename field actualuserid.
            $field = new xmldb_field('actual_userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if ($dbman->field_exists($table, $field)) {
                $dbman->rename_field($table, $field, 'actualuserid');
            }

            // Launch rename field fileid.
            $field = new xmldb_field('moodle_file_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
            if ($dbman->field_exists($table, $field)) {
                $dbman->rename_field($table, $field, 'fileid');
            }

            // Launch rename field filename.
            $field = new xmldb_field('file_report', XMLDB_TYPE_TEXT, 'medium', null, null, null);
            if ($dbman->field_exists($table, $field)) {
                $dbman->rename_field($table, $field, 'filename');
            }

            // Define table plagiarism_originality_sub.
            $newtable = new xmldb_table('plagiarism_originality_sub');
            if (!$dbman->table_exists($newtable)) {
                $dbman->rename_table($table, 'plagiarism_originality_sub');
            }
        }

        // Define table originality_groups.
        $table = new xmldb_table('originality_groups');
        if ($dbman->table_exists($table)) {

            // Define table plagiarism_originality_grp.
            $newtable = new xmldb_table('plagiarism_originality_grp');
            if (!$dbman->table_exists($newtable)) {
                $dbman->rename_table($table, 'plagiarism_originality_grp');
            }
        }

        // Define table plagiarism_originality_conf to be dropped.
        $table = new xmldb_table('plagiarism_originality_conf');

        // Conditionally launch drop table for plagiarism_originality_conf.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Originality savepoint reached.
        upgrade_plugin_savepoint(true, 2023070500, 'plagiarism', 'originality');
    }

    if ($oldversion < 2023070900) {

        // Define table plagiarism_originality_sub.
        $table = new xmldb_table('plagiarism_originality_sub');
        if ($dbman->table_exists($table)) {

            // Conditionally launch drop field parent.
            $field = new xmldb_field('parent');
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }

            // Conditionally launch drop field file_identifier.
            $field = new xmldb_field('file_identifier');
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }

            // Conditionally launch drop field actualuserid.
            $field = new xmldb_field('actualuserid');
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        // Define table plagiarism_originality_resp.
        $table = new xmldb_table('plagiarism_originality_resp');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Originality savepoint reached.
        upgrade_plugin_savepoint(true, 2023070900, 'plagiarism', 'originality');
    }

    if ($oldversion < 2023072000) {

        $table = new xmldb_table('plagiarism_originality_sub');
        if ($dbman->table_exists($table)) {

            // Launch rename field file.
            $fieldfile = new xmldb_field('file', XMLDB_TYPE_TEXT, 'medium', null, null, null);
            $fieldfilesubmited = new xmldb_field('filesubmited', XMLDB_TYPE_TEXT, 'medium', null, null, null);
            if ($dbman->field_exists($table, $fieldfile) && !$dbman->field_exists($table, $fieldfilesubmited)) {
                $dbman->rename_field($table, $fieldfile, 'filesubmited');
            }

            // Conditionally launch add file field.
            $field = new xmldb_field('filesubmited', XMLDB_TYPE_TEXT, 'medium', null, null, null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Originality savepoint reached.
        upgrade_plugin_savepoint(true, 2023072000, 'plagiarism', 'originality');
    }

    if ($oldversion < 2025012900) {
        // Define table plagiarism_originality_stats.
        $table = new xmldb_table('plagiarism_originality_stats');

        // Add fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Define keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('subid_idx', XMLDB_INDEX_NOTUNIQUE, ['subid']);

        // Create the table if it does not exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025012900, 'plagiarism', 'originality');
    }

    return true;
}
