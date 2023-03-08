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

use core\check\performance\debugging;

/**
 * Manipulates all web service data.
 */
class web_service_data {
    /**
     * Sets the active protocols for the protocol given.
     * @param array $protocols
     *
     * @return string|void
     */
    public function set_active_protocol_config(array $protocols) {
        global $CFG;
        try {
            $stringifiedprotocols = implode(',', $protocols);
            if (!isset($CFG->webserviceprotocols)) { // The config doesn't exist we add it.
                set_config('webserviceprotocols', $stringifiedprotocols);
            } else {
                $activewebservices = explode(',', $CFG->webserviceprotocols);
                // Add the selected protocol, ensure the string values returned are unique.
                $adjustedactivewebservices = array_unique(
                    array_merge($activewebservices, $protocols)
                );
                set_config('webserviceprotocols', implode(',', $adjustedactivewebservices));
            }
            return $stringifiedprotocols;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Creates a webservice user.
     * @param string $username
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     *
     * @return int|void
     */
    public function create_ws_user(string $username, string $email, string $firstname, string $lastname) {
        global $USER;
        try {
            $newuser = [
                'username' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'auth' => 'webservice',
                'mnethostid' => 1,
                'confirmed' => 1,
                'timemodified' => time()
            ];

            $createduser = user_create_user($newuser);

            $logdata = array(
                'webservice_id' => null,
                'action' => get_string('ws_log_create_webservice_user', 'local_wswizard', $createduser),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);

            return $createduser;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Gets the capabilities from the webservice function.
     * @param array $wsfunctions
     *
     * @return array|void
     */
    public function get_capabilities_from_webservice_functions(array $wsfunctions) {
        global $DB;
        try {
            $singularcapabilities = array();
            foreach ($wsfunctions as $function) {
                $allcapabilitiesforfunction = $DB->get_record('external_functions', ['name' => $function], 'capabilities');
                // Turns the strings of capabilities into an array.
                $capabilitiesforsinglefunctionarray = explode(',', $allcapabilitiesforfunction->capabilities);
                foreach ($capabilitiesforsinglefunctionarray as $singlecapability) {
                    array_push($singularcapabilities, $singlecapability);
                } //End of internal loop
            } // End of parent loop
            return array_values(array_unique($singularcapabilities));
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Creates a webservice role.
     * @param string $fullname
     * @param string $shortname
     * @param string $description
     *
     * @return int|void
     */
    public function create_webservice_role(string $fullname, string $shortname, string $description) {
        try {
            global $USER;
            $role = create_role($fullname, $shortname, $description);
            $logdata = array(
                'webservice_id' => null,
                'action' => get_string('ws_log_create_webservice_role', 'local_wswizard', $role),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);
            return $role;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Assigns the proper capabilities to a role.
     * @param int   $roleid
     * @param array $wsfunctions
     * @param array $protocols
     * @param int   $contextid
     *
     * @return bool|void
     */
    public function assign_ws_capabilities_to_role(int $roleid, array $wsfunctions, array $protocols, int $contextid) {
        try {
            global $USER;
            $protocolcapabilities = array_map(function ($p) { // Generate needed webservice protocol capabilities.
                return "webservice/{$p}:use";
            }, $protocols);
            $requiredcapabilities = $this->get_capabilities_from_webservice_functions($wsfunctions);
            if ($requiredcapabilities || $protocolcapabilities) {
                $allcapabilities = array_merge($requiredcapabilities, $protocolcapabilities);

                set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);
                foreach ($allcapabilities as $capability) {
                    if ($capability && !empty($capability)) {
                        assign_capability(trim($capability), CAP_ALLOW, $roleid, $contextid, false);
                    }
                }
            } // End of loop.

            $logdata = array(
                'webservice_id' => null,
                'action' => get_string('ws_log_assign_capabilities_to_role', 'local_wswizard', $roleid),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);

            return true;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Sets the values of the webservice.
     * @param $webserviceid
     * @param $shortname
     * @param $name
     * @param $allowfileuploads
     * @param $allowfiledownloads
     * @param $isenabled
     *
     * @return void
     */
    public function edit_or_create_webservice_object
    ($webserviceid, $shortname, $name, $allowfileuploads, $allowfiledownloads, $isenabled) {
        try {
            global $USER;
            $webservicemanager = new \webservice();

            $service = $webservicemanager->get_external_service_by_id($webserviceid);
            $service = new \stdClass();
            $service->name = $name;
            $service->enabled = $isenabled;
            $service->restrictedusers = 1;
            $service->shortname = $shortname;
            $service->timecreated = time();
            $service->timemodified = time();
            $service->downloadfiles = $allowfiledownloads;
            $service->uploadfiles = $allowfileuploads;
            if (!$webserviceid) {
                $webservice = $webservicemanager->add_external_service($service);
                $logdata = array(
                    'webservice_id' => $webservice,
                    'action' => get_string('ws_log_create_webservice', 'local_wswizard'),
                    'createdby' => $USER->id,
                    'timecreated' => time()
                );
                $logs = new \local_wswizard\Logs();
                $logs->insert($logdata);
            } else {
                $service->id = $webserviceid;
                $webservice = $webservicemanager->update_external_service($service);

                $logdata = array(
                    'webservice_id' => $webserviceid,
                    'action' => get_string('ws_log_update_webservice', 'local_wswizard'),
                    'createdby' => $USER->id,
                    'timecreated' => time()
                );
                $logs = new \local_wswizard\Logs();
                $logs->insert($logdata);
            }

            return $webservice;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Adds functions to the webservice.
     * @param $webserviceobjectid
     * @param $wsuserid
     * @param $wsfunctions
     *
     * @return bool|void
     */
    public function assign_functions_to_webservice($webserviceobjectid, $wsuserid, $wsfunctions) {
        try {
            global $DB, $USER;
            $webservicemanager = new \webservice();
            // Remove any existing function and start from scratch.
            $servicefunctions = $DB->count_records('external_services_functions', ['externalserviceid' => $webserviceobjectid]);
            if ($servicefunctions > 0) {
                $servicefunctions = $DB->get_records('external_services_functions', ['externalserviceid' => $webserviceobjectid]);
                foreach ($servicefunctions as $sf) {
                    $webservicemanager->remove_external_function_from_service($sf->functionname, $webserviceobjectid);
                }
            }
            // Add functions.
            foreach ($wsfunctions as $wsf) {
                $webservicemanager->add_external_function_to_service($wsf, $webserviceobjectid);
            }

            // Authorise the user to use the service.
            if (!$DB->record_exists('external_services_users', [
                'externalserviceid' => $webserviceobjectid,
                'userid' => $wsuserid
            ])) {
                $webservicemanager->add_ws_authorised_user((object)[
                    'externalserviceid' => $webserviceobjectid,
                    'userid' => $wsuserid
                ]);
            }

            $logdata = array(
                'webservice_id' => $webserviceobjectid,
                'action' => get_string('ws_log_change_functions', 'local_wswizard'),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);
            return true;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Creates a user token for a given webservice.
     * @param $webserviceobjectid
     * @param $wsuserid
     * @param $context
     * @param $iprestriction
     * @param $validuntil
     *
     * @return void
     */
    public function create_webservice_token($webserviceobjectid, $wsuserid, $context, $iprestriction, $validuntil) {
        try {
            global $DB, $USER;
            $wstoken = null;
            $sql = "SELECT * FROM {external_tokens}
            where externalserviceid = ? and userid = ? and contextid = ? and (validuntil = 0 OR validuntil > ?)";
            if ($DB->record_exists_sql($sql, [
                'externalserviceid' => $webserviceobjectid,
                'userid' => $wsuserid,
                'contextid' => $context->id,
                'validuntil' => time()
            ])) {
                $tokenobject = $DB->get_record_sql($sql, [
                    'externalserviceid' => $webserviceobjectid,
                    'userid' => $wsuserid,
                    'contextid' => $context->id,
                    'validuntil' => time()
                ]);

                $wstoken = $tokenobject->token;
            } else {
                $wstoken = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $webserviceobjectid, $wsuserid,
                    $context, $validuntil, $iprestriction);

                // Used to get generated token id for log.
                $record = $DB->get_record('external_tokens', array('token' => $wstoken));
                $tokenid = $record->id;

                $logdata = array(
                    'webservice_id' => $webserviceobjectid,
                    'action' => get_string('ws_log_create_token', 'local_wswizard',
                        ['tokenid' => $tokenid, 'for_wsuserid' => $wsuserid]),
                    'createdby' => $USER->id,
                    'timecreated' => time()
                );
                $logs = new \local_wswizard\Logs();
                $logs->insert($logdata);
            }

            return $wstoken;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Sets the appopriate role to a given webservice.
     * @param $roleid
     * @param $webserviceid
     *
     * @return bool|void
     */
    public function set_webservice_role($roleid, $webserviceid) {
        global $DB, $USER;
        try {
            $recordarray = array();
            $recordarray['webservice_id'] = $webserviceid;
            $recordarray['role_id'] = $roleid;
            if ($DB->record_exists('local_wswizard_ws_role', ['webservice_id' => $webserviceid, 'role_id' => $roleid])) {
                $DB->delete_records('local_wswizard_ws_role', ['webservice_id' => $webserviceid, 'role_id' => $roleid]);
            }
            $DB->insert_record('local_wswizard_ws_role', $recordarray);

            $logdata = array(
                'webservice_id' => $webserviceid,
                'action' => get_string('ws_log_set_webservice_role', 'local_wswizard', $roleid),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);

            return true;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }
}
