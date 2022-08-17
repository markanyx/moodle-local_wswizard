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

namespace local_wswizard;
class Logs {

    private $id;
    private $userid;
    private $action;
    private $ip;
    private $timecreated;

    public function __construct() {
    }

    /**
     *
     * @param type              $dataobject
     *
     * @global type             $USER
     * @global \moodle_database $DB
     */
    public function insert($dataobject) {
        global $DB;

        // Get webservice name.
        $dataobject['webservicename'] = $DB->get_record(
            'external_services',
            array('id'=>$dataobject['webservice_id'])
        )->name;

        // Get user's username.
        $dataobject['username'] = $DB->get_record(
            'user',
            array('id'=>$dataobject['createdby'])
        )->username;

        // Add to log table.
        $DB->insert_record('local_wswizard_logs', $dataobject);
    }

    /**
     *
     * @return type
     * @global \moodle_database $DB
     */
    public function get_all() {
        global $DB;
        $logs = $DB->get_records('local_wswizard_logs', array('archived' => 0), 'id DESC');
        return $logs;
    }

    /**
     * @return void
     * @throws \dml_exception
     */
    public function archive() {
        global $DB;
        // A year is 3600 * 24 * 365.
        $year = 31556736;
        $now = time();
        $yearago = $now - $year;
        $sql = 'UPDATE {local_wswizard_logs} SET archived = ' . 1 . ' WHERE timecreated > ?';
        $DB->execute($sql, array($yearago));
    }

}
