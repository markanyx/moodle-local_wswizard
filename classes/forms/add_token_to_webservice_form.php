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
namespace local_wswizard\forms;

use local_wswizard;
use local_wswizard\web_service_data;
use moodleform;

class add_token_to_webservice_form extends \core_form\dynamic_form {
    public function definition() {
        $base = new local_wswizard\Base();
        $mform = $this->_form;

        $mform->addElement('hidden', 'webservice_id');
        $mform->setType('webservice_id', PARAM_INT);

        $base = new local_wswizard\Base();
        $wsexistingusers = $base->get_webservice_users();

        // User.
        $options = array(
            'multiple' => false,
            'noselectionstring' => 'Choose an existing user from the list',
        );
        $mform->addElement('autocomplete', 'ws_existing_user',
            get_string('selectwsuser', 'local_wswizard'), $wsexistingusers, $options);

        // IP restriction.
        $mform->addElement('text', 'restricted_ip',
            get_string('restricted_ip', 'local_wswizard'));
        // Valid Until Date.
        $mform->addElement('advcheckbox', 'add_expiry_date',
            get_string('add_expiry_date', 'local_wswizard'));
        $mform->setDefault('add_expiry_date', 0);
        $mform->addElement('date_selector', 'valid_until',
            get_string('dashboard_ws_token_table_column_valid_until', 'local_wswizard'));
        $mform->disabledIf('valid_until', 'add_expiry_date', 'notchecked');
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
        global $USER, $DB;
        $fromform = $this->get_data();
        $webserviceobjectid = $fromform->webservice_id;
        $wsuserid = $fromform->ws_existing_user;
        $iprestriction = $fromform->restricted_ip;

        // If user checked expiry date box.
        ($fromform->add_expiry_date) ?
            $validuntil = $fromform->valid_until : $validuntil = 0;

        $context = $this->get_context_for_dynamic_submission();

        try {
            $wstoken = external_generate_token(EXTERNAL_TOKEN_PERMANENT,
                $webserviceobjectid,
                $wsuserid,
                $context,
                $validuntil,
                $iprestriction
            );

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

            return $wstoken;
        } catch (\moodle_exception $e) {
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }

        return json_encode($fromform);
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
        $data = array();
        $data['webservice_id'] = $this->optional_param('webservice_id', 0, PARAM_INT);
        $data['context'] = \context_system::instance();
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
        $webserviceid = $this->optional_param('webservice_id', null, PARAM_INT);
        if ($webserviceid) {
            $url = '/contentbank/view.php';
            $params['webservice_id'] = $webserviceid;
        } else {
            $url = '/dashboard.php#nav-' . $webserviceid;
        }

        return new \moodle_url($url, $params);
    }

    public function validation($data, $files) {
        $errors = array();

        // Ensure restrict IP address is valid.
        if ($data['restricted_ip'] &&
            preg_match('/^((25[0-5]|(2[0-4]|1[0-9]|[1-9]|)[0-9])(\.(?!$)|$)){4}$/', $data['restricted_ip']) == false) {
            $errors['restricted_ip'] = get_string('ws_ipaddress_not_valid', 'local_wswizard');
        }

        return $errors;
    }
}
