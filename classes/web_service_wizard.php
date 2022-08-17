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

require_once(dirname(__FILE__) . '../../../../config.php');
require_login();

use local_wswizard\Base;

class web_service_wizard {

    private $webserviceid;
    private $webservicename;
    private $webserviceshortname;
    private $enabled;
    private $requiredcapability;
    private $restrictedusers;
    private $downloadfiles;
    private $uploadfiles;
    private $timecreated;
    private $timemodified;
    private $tokens;
    private $functions;
    private $username;
    private $authosiedusers;
    private $protocols;
    private $roleid;


    public function __construct($id = null) {
        $this->get_service_by_id($id);
        $this->get_functions_from_webservice_id($id);
        $this->get_authorised_users_from_ws_id($id);
        $this->get_tokens_from_webservice_id($id);
        $this->get_webservice_role($id);
        $this->get_webservice_protocols_from_webservice($id);
    }

    /**
     * @return mixed
     */
    public function get_ws_id() {
        return $this->webserviceid;
    }

    /**
     * @return mixed
     */
    public function get_webservice_name() {
        return $this->webservicename;
    }

    /**
     * @return mixed
     */
    public function get_webservice_shortname() {
        return $this->webserviceshortname;
    }

    /**
     * @return mixed
     */
    public function get_enabled() {
        return $this->enabled;
    }

    /**
     * @return mixed
     */
    public function get_required_capability() {
        return $this->requiredcapability;
    }

    /**
     * @return mixed
     */
    public function get_restricted_users() {
        return $this->restrictedusers;
    }

    /**
     * @return mixed
     */
    public function get_download_files() {
        return $this->downloadfiles;
    }

    /**
     * @return mixed
     */
    public function get_upload_files() {
        return $this->uploadfiles;
    }

    /**
     * @return mixed
     */
    public function get_time_created() {
        return $this->timecreated;
    }

    /**
     * @return mixed
     */
    public function get_time_modified() {
        return $this->timemodified;
    }

    /**
     * @return mixed
     */
    public function get_tokens() {
        return $this->tokens;
    }

    /**
     * @return mixed
     */
    public function get_functions() {
        return $this->functions;
    }

    /**
     * @return mixed
     */
    public function get_username() {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function get_authosied_users() {
        return $this->authosiedusers;
    }


    public function get_all_services() {
        global $DB;
        return $DB->get_records('external_services');
    }

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

    public function get_tokens_from_webservice_id($webserviceid) {
        global $DB;
        $this->tokens = $DB->get_records('external_tokens', array('externalserviceid' => $webserviceid));

        // Get user and creator of token.
        foreach ($this->tokens as $tok) {
            $tok->username = $this->get_username_from_id($tok->userid);
            $tok->creatorName = $this->get_username_from_id($tok->creatorid);

            // Checks if there is an expiration date.
            if ($tok->validuntil > 0) {
                $tok->validuntil = date('d F Y', $tok->validuntil);
            } else {
                $tok->validuntil = get_string('dashboard_ws_token_no_expiration', 'local_wswizard');
            }
        }
        return $this->tokens;
    }

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

    public function get_user_by_id($userid) {
        global $DB;
        $DB->get_record('user', array('id' => $userid));
    }

    public function get_username_from_id($userid) {
        global $DB;
        return $DB->get_record('user', array('id' => $userid), 'username')->username;
    }

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
     * @return mixed
     */
    public function get_protocols() {
        return $this->protocols;
    }

    /**
     * @return mixed
     */
    public function get_role_id() {
        return $this->roleid;
    }

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
