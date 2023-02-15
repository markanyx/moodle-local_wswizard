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
 * *************************************************************************
 * *                     Web Service Wizard                               **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  wswizard                                                  **
 * @name        Web Service Wizard                                        **
 * @copyright   Markanyx Solutions Inc.                                   **
 * @link                                                                  **
 * @author      Kais Abid                                                 **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */


function xmldb_local_wswizard_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2022081700) {

        // Define field username to be dropped from local_wswizard_logs.
        $table = new xmldb_table('local_wswizard_logs');
        $field = new xmldb_field('username');

        // Conditionally launch drop field username.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('webservicename');

        // Conditionally launch drop field webservicename.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Wswizard savepoint reached.
        upgrade_plugin_savepoint(true, 2022081700, 'local', 'wswizard');
    }
    if ($oldversion < 2022081701) {

        // Define field ip to be added to local_wswizard_logs.
        $table = new xmldb_table('local_wswizard_logs');
        $field = new xmldb_field('ip', XMLDB_TYPE_TEXT, null, null, null, null, null, 'webservice_id');

        // Conditionally launch add field ip.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wswizard savepoint reached.
        upgrade_plugin_savepoint(true, 2022081701, 'local', 'wswizard');
    }
    return true;
}
