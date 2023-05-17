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
 * @package     local_wswizard                                            **
 * @name        Web Service Wizard                                        **
 * @copyright   Markanyx Solutions Inc.                                   **
 * @link                                                                  **
 * @author      Kais Abid                                                 **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

namespace local_wswizard;

/**
 * Handles the logs.
 */
class Logs {

    /**
     * @var int The id of the log.
     */
    private $id;
    /**
     * @var id The user's id of the log.
     */
    private $userid;
    /**
     * @var string The action performed of the log.
     */
    private $action;
    /**
     * @var string The user's IP for the log.
     */
    private $ip;
    /**
     * @var int The time the log took place.
     */
    private $timecreated;

    /**
     * Constructor of class.
     */
    public function __construct() {
    }

    /**
     * Inserts the log to database.
     * @param type $dataobject
     *
     * @global \moodle_database $DB
     */
    public function insert($dataobject) {
        global $DB;

        // Get webservice name.
        $dataobject['webservicename'] = $DB->get_record(
            'external_services',
            array('id' => $dataobject['webservice_id'])
        );

        // Get user's username.
        $dataobject['username'] = $DB->get_record(
            'user',
            array('id' => $dataobject['createdby'])
        )->username;
        $dataobject['ip'] = $this->get_client_ip();
        // Add to log table.
        $DB->insert_record('local_wswizard_logs', $dataobject);
    }

    /**
     * Gets all the logs for display.
     * @return type
     * @global \moodle_database $DB
     */
    public function get_all() {
        global $DB;
        $logs = $DB->get_records('local_wswizard_logs');
        $this->format_logs($logs);
        return $logs;
    }

    /**
     * Gets the user's IP.
     * @return string
     */
    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    /**
     * Takes an array of logs and converts timestamp to date.
     * @param $logs
     *
     * @return void
     */
    public function format_logs($logs) {
        global $DB;
        foreach ($logs as $log) {
            $log->timecreated = date('Y-m-d H:i', $log->timecreated);
            $ws = $DB->get_record('external_services', ['id' => $log->webservice_id]);
            $log->webservice = $ws->name;
            $user = user_get_users_by_id([$log->createdby]);
            $log->createdby = fullname(array_pop($user));
        }
    }
}
