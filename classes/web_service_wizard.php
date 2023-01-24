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

use local_wswizard\Base;

/**
 * Used to gather information for the dashboard.
 */
class web_service_wizard {

    /**
     * @var int The id of the webservice.
     */
    private $webserviceid;
    /**
     * @var string The name of the webservice.
     */
    private $webservicename;
    /**
     * @var string The shortname of the webservice.
     */
    private $webserviceshortname;
    /**
     * @var bool The enable status of the webservice.
     */
    private $enabled;
    /**
     * @var bool The enable status of the webservice.
     */
    private $requiredcapability;
    /**
     * @var array[] restricted users of webservice.
     */
    private $restrictedusers;
    /**
     * @var bool The can download files status of the webservice.
     */
    private $downloadfiles;
    /**
     * @var bool The can upload files status of the webservice.
     */
    private $uploadfiles;
    /**
     * @var int The time the webservice was created.
     */
    private $timecreated;
    /**
     * @var int The time the webservice was modified.
     */
    private $timemodified;
    /**
     * @var array[] All the tokens from the webservice.
     */
    private $tokens;
    /**
     * @var array[] All the functions from the webservice.
     */
    private $functions;
    /**
     * @var array[] All the authorised users of the webservice.
     */
    private $authosiedusers;
    /**
     * @var array[] All the protocols from the webservice.
     */
    private $protocols;
    /**
     * @var array[] The role id for the webservice.
     */
    private $roleid;


    /**
     * Gets all the information needed for a webservice.
     * @param $id
     */
    public function __construct($id = null) {
        $this->get_service_by_id($id);
        $this->get_functions_from_webservice_id($id);
        $this->get_authorised_users_from_ws_id($id);
        $this->get_tokens_from_webservice_id($id);
        $this->get_webservice_role($id);
        $this->get_webservice_protocols_from_webservice($id);
    }

    /**
     * Gets the web service id.
     * @return mixed
     */
    public function get_ws_id() {
        return $this->webserviceid;
    }

    /**
     * Gets the webservice name.
     * @return mixed
     */
    public function get_webservice_name() {
        return $this->webservicename;
    }

    /**
     * Gets the webservice's shortname.
     * @return mixed
     */
    public function get_webservice_shortname() {
        return $this->webserviceshortname;
    }

    /**
     * Gets the webservice's enable status.
     * @return mixed
     */
    public function get_enabled() {
        return $this->enabled;
    }

    /**
     * Gets the webservice capabilities.
     * @return mixed
     */
    public function get_required_capability() {
        return $this->requiredcapability;
    }

    /**
     * Gets the webservice's restricted users.
     * @return mixed
     */
    public function get_restricted_users() {
        return $this->restrictedusers;
    }

    /**
     * Gets the webservice's download files status.
     * @return mixed
     */
    public function get_download_files() {
        return $this->downloadfiles;
    }

    /**
     * Gets the webservice's upload files status.
     * @return mixed
     */
    public function get_upload_files() {
        return $this->uploadfiles;
    }

    /**
     * Gets the webservice's time created.
     * @return mixed
     */
    public function get_time_created() {
        return $this->timecreated;
    }

    /**
     * Gets the webservice's time modified.
     * @return mixed
     */
    public function get_time_modified() {
        return $this->timemodified;
    }

    /**
     * Gets all the user tokens assigned to webservice.
     * @return mixed
     */
    public function get_tokens() {
        return $this->tokens;
    }

    /**
     * Gets all the webservice functions assigned to it.
     * @return mixed
     */
    public function get_functions() {
        return $this->functions;
    }

    /**
     * Gets all authorised users for webservice.
     * @return mixed
     */
    public function get_authosied_users() {
        return $this->authosiedusers;
    }


    /**
     * Gets record of all external_services table.
     * @return mixed
     */
    public function get_all_services() {
        global $DB;
        return $DB->get_records('external_services');
    }


    /**
     * Gathers information from external_services table for a given id.
     * @param $id
     *
     * @return void
     */
    private function get_service_by_id($id) {
        global $DB;
        $data = $DB->get_record('external_services', array('id' => $id));

        $this->webserviceid = $id;
        $this->webservicename = $data->name;
        $this->enabled = $data->enabled;
        $this->requiredcapability = $data->requiredcapability;
        $this->restrictedusers = $data->restrictedusers;
        $this->timecreated = $data->timecreated;
        $this->timemodified = $data->timemodified;
        $this->webserviceshortname = $data->shortname;
        $this->downloadfiles = $data->downloadfiles;
        $this->uploadfiles = $data->uploadfiles;
    }

    /**
     * Gets all the tokens associated to a webservice.
     * @param $webserviceid
     *
     * @return array[]
     */
    public function get_tokens_from_webservice_id($webserviceid) {
        global $DB, $CFG;
        $this->tokens = $DB->get_records('external_tokens', array('externalserviceid' => $webserviceid));

        // Get user and creator of token.
        foreach ($this->tokens as $tok) {
            $tok->username = $this->get_username_from_id($tok->userid);
            $tok->creatorname = $this->get_username_from_id($tok->creatorid);
            $tok->userurl = $CFG->wwwroot."/user/profile.php?id=$tok->userid";
            $tok->creatorurl = $CFG->wwwroot."/user/profile.php?id=$tok->creatorid";
            // Checks if there is an expiration date.
            if ($tok->validuntil > 0) {
                $tok->validuntil = date('d F Y', $tok->validuntil);
            } else {
                $tok->validuntil = get_string('dashboard_ws_token_no_expiration', 'local_wswizard');
            }
        }
        return $this->tokens;
    }

    /**
     * Gets all the functions associated to a webservice.
     * @param $webserviceid
     *
     * @return array[]
     */
    public function get_functions_from_webservice_id($webserviceid) {
        global $DB, $CFG;

        // Get function name and capabilities.
        $sql = "Select
                    {external_services_functions}.functionname,
                    {external_services}.id,
                    {external_functions}.capabilities
                From
                    {external_services_functions} Join
                    {external_services} On
                            {external_services}.id = {external_services_functions}.externalserviceid Join
                    {external_functions} On {external_services_functions}.functionname =
                            {external_functions}.name
                Where
                    {external_services_functions}.externalserviceid = ?";

        $this->functions = $DB->get_records_sql($sql, array($webserviceid));

        // Get Descriptions for function.
        $functions = array();
        $coreservices = $CFG->dirroot . '/lib/db/services.php';
        foreach ($this->functions as $func) {
            /*
                Require must be inside or else
                issue where if there are more than 1
                instance of description it will not display
            */
            require($coreservices);

            $externalfunctions = $functions;
            // If the function has a description (fixed error message too).
            if (array_key_exists($func->functionname, $externalfunctions)) {
                $func->description = $externalfunctions[$func->functionname]['description'];
            }
        }

        return $this->functions;
    }

    /**
     * Calls the user table for a given user id.
     * @param $userid
     *
     * @return void
     */
    public function get_user_by_id($userid) {
        global $DB;
        $DB->get_record('user', array('id' => $userid));
    }

    /**
     * Gets the username of a user from their user id.
     * @param $userid
     *
     * @return mixed
     */
    public function get_username_from_id($userid) {
        global $DB;
        return $DB->get_record('user', array('id' => $userid), 'username')->username;
    }

    /**
     * Finds all authorised users for a webservice.
     * @param $webserviceid
     *
     * @return array[]
     */
    public function get_authorised_users_from_ws_id($webserviceid) {
        global $DB;

        $this->authosiedusers = $DB->get_records('external_services_users', array('externalserviceid' => $webserviceid));
        // Get user and creator of token.
        foreach ($this->authosiedusers as $tok) {
            $tok->auth_username = $this->get_username_from_id($tok->userid);
            if (property_exists($tok, 'creatorid')) {
                $tok->creatorName = $this->get_username_from_id($tok->creatorid);
            }
        }
        return $this->authosiedusers;
    }

    /**
     * Gets protocols.
     * @return mixed
     */
    public function get_protocols() {
        return $this->protocols;
    }

    /**
     * Returns role id.
     * @return mixed
     */
    public function get_role_id() {
        return $this->roleid;
    }


    /**
     * Get the role name from a role id.
     * @param $id
     *
     * @return false
     */
    public function get_role_name_from_id($id) {
        global $DB;
        $record = $DB->get_record('role', ['id' => $id]);
        if ($record) {
            if (property_exists($record, 'name')) {
                return $record->name;
            }
        }
        return false;
    }

    /**
     * Gets the role for the webservice.
     * @param $webserviceid
     *
     * @return array[]|void
     */
    private function get_webservice_role($webserviceid) {
        global $DB;
        $role = $DB->get_record('local_wswizard_ws_role', ['webservice_id' => $webserviceid], 'role_id');
        if ($role) {
            if (property_exists($role, 'role_id')) {
                $this->roleid = $role->role_id;
                return $this->roleid;
            }
        }
    }

    /**
     * Gets all the protocols for a given webservice.
     * @param $webserviceid
     *
     * @return void
     */
    public function get_webservice_protocols_from_webservice($webserviceid) {
        global $DB;
        $protocols = array();
        $role = $DB->get_record('role', ['id' => $this->roleid]);
        if (isset($role) && property_exists((object)$role, 'id')) {
            $capabilities = get_capabilities_from_role_on_context($role, \context_system::instance());
            $basefunctions = new Base();
            $siteprotocols = $basefunctions->get_webservice_protocols();

            foreach ($capabilities as $capability) {
                $matchedcapability = array_search($capability->capability, $siteprotocols);
                if ($matchedcapability) {
                    $protocols[$matchedcapability] = $matchedcapability;
                }
            }
            $this->protocols = $protocols;
        }
    }


}
