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

namespace local_wswizard\forms;

use local_wswizard;
use local_wswizard\web_service_data;
use local_wswizard\web_service_wizard;
use Matrix\Exception;
use moodleform;

/**
 * Creates a dynamic moodle form as a popup modal to add functions to a webservice.
 */
class add_functions_to_webservice_form extends \core_form\dynamic_form {
    public function definition() {
        $base = new local_wswizard\Base();
        $mform = $this->_form;

        $mform->addElement('hidden', 'webserviceid');
        $mform->setType('webserviceid', PARAM_INT);
        $mform->addElement('hidden', 'wsroleid');
        $mform->setType('wsroleid', PARAM_INT);
        $externalfunctions = $base->get_external_functions();
        $options = array(
            'multiple' => true,
        );

        $mform->addElement('autocomplete', 'ws_functions',
            get_string('wsfunctions', 'local_wswizard'), $externalfunctions, $options);
        $mform->addRule('ws_functions', null, 'required');
        // Adding spacing!
        $mform->addElement('html', '<div style="width:100%" class="mt-5 mb-5"></div>');
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     */
    protected function check_access_for_dynamic_submission(): void {
        require_admin();
    }

    /**
     * Returns form context
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        return \context_system::instance();
    }

    /**
     * WS functions options
     *
     * @return array
     * @throws \coding_exception
     */
    protected function get_options(): array {
        return array();
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or array
     * s that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/webservice/lib.php');

        $fromform = $this->get_data();
        $wsfunctions = $fromform->ws_functions;
        $webserviceobjectid = $fromform->webserviceid;
        $wsroleid = $fromform->wsroleid;

        // This is from ws_edit.php.
        try {
            global $DB;
            $base = new local_wswizard\Base();

            // If there is a webservice role, give the role the function capabilities.
            $requiredcapabilities = $this->get_capabilities_from_webservice_functions($wsfunctions);
            $context = $this->get_context_for_dynamic_submission();
            if ($wsroleid) {
                if ($requiredcapabilities) {
                    set_role_contextlevels($wsroleid, [CONTEXT_SYSTEM]);
                    foreach ($requiredcapabilities as $capability) {
                        if ($capability && !empty($capability)) {
                            assign_capability(trim($capability), CAP_ALLOW, $wsroleid, $context, false);
                        }
                    }
                }
            }
            if ($wsfunctions) {
                $this->assign_functions_to_webservice($webserviceobjectid, $wsfunctions);
            }

            return json_encode($wsfunctions);
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }

        return $fromform;
    }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['id']));
     */
    public function set_data_for_dynamic_submission(): void {
        global $CFG, $DB;
        // Get ID.
        $wsid = $this->optional_param('webserviceid', 0, PARAM_INT);
        $wsroleid = $this->optional_param('ws_roleid', 0, PARAM_INT);
        $data = array();
        if ($wsid) {
            $data['webserviceid'] = $wsid;
            $sql = "
                SELECT
                    {external_services_functions}.functionname,
                    {external_services}.id,
                    {external_functions}.capabilities
                FROM
                    {external_services_functions}
                JOIN {external_services} ON
                            {external_services}.id = {external_services_functions}.externalserviceid
                JOIN {external_functions} ON {external_services_functions}.functionname = {external_functions}.name
                WHERE
                    {external_services_functions}.externalserviceid = ?";

            $wsfunctions = $DB->get_records_sql($sql, array($wsid));
            $wsfunctionsparsed = array_map(function ($wsfunc) {
                return $wsfunc->functionname;
            }, $wsfunctions);

            $data['ws_functions'] = array_values($wsfunctionsparsed);
            $data['wsroleid'] = $wsroleid;
        } else {
            $data['wsname'] = "No id was found";
        }

        $this->set_data($data);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * @return \moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        $webserviceid = $this->optional_param('webserviceid', null, PARAM_INT);
        if ($webserviceid) {
            $url = '/contentbank/view.php';
            $params['webserviceid'] = $webserviceid;
        } else {
            $url = '/dashboard.php#nav-' . $webserviceid;
        }

        return new \moodle_url($url, $params);
    }

    /**
     * Gets ta list of capabilities from a webservice function.
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
                }
            }
            return array_values(array_unique($singularcapabilities));
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
    }

    /**
     * Adds functions to a webservice
     * @param $webserviceobjectid
     * @param $wsfunctions
     *
     * @return bool|void
     */
    public function assign_functions_to_webservice($webserviceobjectid, $wsfunctions) {
        try {
            global $DB, $USER;
            $webservicemanager = new \webservice();
            // Remove any existing function and start from scratch.
            $servicefunctions = $DB->count_records('external_services_functions', ['externalserviceid' => $webserviceobjectid]);
            if ($servicefunctions > 0) {
                $servicefunctions = $DB->get_records('external_services_functions', ['externalserviceid' => $webserviceobjectid]);
                foreach ($servicefunctions as $sf) {
                    $DB->delete_records('external_services_functions',
                        array('externalserviceid' => $webserviceobjectid, 'functionname' => $sf->functionname));
                }
            }
            // Add functions.
            foreach ($wsfunctions as $wsf) {
                $addedfunction = new \stdClass();
                $addedfunction->externalserviceid = $webserviceobjectid;
                $addedfunction->functionname = $wsf;
                $DB->insert_record('external_services_functions', $addedfunction);
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
}
