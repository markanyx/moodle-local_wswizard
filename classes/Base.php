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


// TO DO: Change this into a Singleton and get rid of static functions.
use core\notification;

class Base {

    /**
     * Creates the Moodle page header
     *
     * @param string            $url         Current page url
     * @param string            $pagetitle   Page title
     * @param string            $pageheading Page heading (Note hard coded to site fullname)
     * @param array             $context     The page context (SYSTEM, COURSE, MODULE etc)
     *
     * @return HTML Contains page information and loads all Javascript and CSS
     * @global \moodle_database $DB
     * @global \moodle_page     $PAGE
     * @global \stdClass        $SITE
     * @global \stdClass        $CFG
     */
    public static function page($url, $pagetitle, $pageheading, $context, $pagelayout = 'admin') {
        global $CFG, $PAGE, $SITE;

        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings('local_wswizard', current_language());

        $PAGE->set_url($url);
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pageheading);
        $PAGE->set_pagelayout('base');
        $PAGE->set_context($context);
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->strings_for_js(array_keys($strings), 'local_wswizard');
    }

    /**
     * @return array
     * @throws \dml_exception
     */
    public function get_webservice_users() {
        global $DB;
        $wsusers = $DB->get_records('user', array('auth' => 'webservice'));
        $wsusersarray = [];
        foreach ($wsusers as $wsuser) {
            $wsusersarray[$wsuser->id] = $wsuser->username . ' - ' . $wsuser->firstname . ' ' . $wsuser->lastname;
        }
        return $wsusersarray;
    }

    // Modifed by Karl Thibaudeau, only will return roles with web service protocol capabilities.

    /**
     * @return array|void
     */
    public function get_roles_for_active_webservices() {
        try {
            $nonuniqueroles = array();
            $rolelist = array();
            $webservicecapabilities = $this->get_webservice_protocols();

            foreach ($webservicecapabilities as $capability) { // Loop through the web service protocol capabilities.

                $roleswithcapability = array_values(get_roles_with_capability($capability));
                $nonuniqueroles = array_merge($nonuniqueroles, $roleswithcapability);
            }

            $uniqueroles = array_filter(
                array_unique($nonuniqueroles, SORT_REGULAR)
            ); // 1 role and have multiple capabilities, we only want 1 of each role.

            foreach ($uniqueroles as $role) {
                $rolelist[$role->id] = $role->name;
            }

            return $rolelist;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * @return string[]
     */
    public function get_webservice_protocols() {
        $protocols = $this->get_protocols();
        return array_map(function ($p) { // Only return capabilities for existing web services.
            return "webservice/{$p}:use"; // Identification string for web service capability. works like an ID.
        }, $protocols);
    }

    /**
     * @return array
     */
    public function get_protocols() {
        $protocolsavailable = \core_component::get_plugin_list('webservice');
        $protocolsarray = [];
        $protocols = array_keys($protocolsavailable);
        foreach ($protocols as $p) {
            $protocolsarray[$p] = $p;
        }
        return $protocolsarray;
    }

    /**
     * @return array
     */
    public function get_capabilities() {
        $capabilities = get_all_capabilities();
        $capabilitiesarray = [];

        foreach ($capabilities as $c) {
            $capabilitiesarray[$c['name']] = $c['name'];
        }
        return $capabilitiesarray;
    }

    /**
     * @return array
     * @throws \dml_exception
     */
    public function get_external_functions() {
        global $DB;
        $externalfunctions = $DB->get_records('external_functions');
        $externalfunctionsarray = [];

        foreach ($externalfunctions as $ef) {
            $externalfunctionsarray[$ef->name] = $ef->name . ' **CAPABILITIES: ' . $ef->capabilities;
        }
        return $externalfunctionsarray;
    }


    /**
     * @param $id
     * @param $username
     * @param $idnumber
     * @return false|mixed|\stdClass|void
     * @throws \dml_exception
     */
    public static function get_user_record($id = 0, $username = '', $idnumber = '') {
        global $DB;

        if ($id != 0) {
            return $DB->get_record('user', array('id' => $id));
        }

        if ($username != '') {
            return $DB->get_record('user', array('username' => $username));
        }

        if ($idnumber != '') {
            return $DB->get_record('user', array('idnumber' => $idnumber));
        }
    }


    /**
     * @param $userid
     * @return string
     * @throws \dml_exception
     */
    public function get_user_name_by_userid($userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));
        return $user->firstname . ' ' . $user->lastname;
    }


    /**
     * @return void
     */
    public function get_capability_categories() {
        try {
            $allcapabilities = $this->get_capabilities();
            $capabilitycategories = array_map(function ($capability) {
            }, $allcapabilities);
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

}
