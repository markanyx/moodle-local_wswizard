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

use html_writer;
use local_wswizard;
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/lib/formslib.php");

/**
 * Form for creating a webservice.
 */
class ws_form extends moodleform {

    /**
     * Adds elements and logic to the form.
     * @return void
     */
    public function definition() {
        global $CFG;
        $this->context = \context_system::instance();

        $mform = &$this->_form;

        $userchoice = ['Choose existing', 'Add new'];

        $base = new local_wswizard\Base();
        $wsexistingusers = $base->get_webservice_users();
        $wsroles = $base->get_roles_for_active_webservices();
        $protocols = $base->get_protocols();
        $capabilities = $base->get_capabilities();
        $externalfunctions = $base->get_external_functions();

        $headinghtml = html_writer::start_div("mb-4");
        $headinghtml .= html_writer::start_tag("h2");
        $headinghtml .= get_string('ws_form_heading', 'local_wswizard');
        $headinghtml .= html_writer::end_tag('h2');
        $headinghtml .= html_writer::end_tag('div');

        // Webservice id - for edit!
        $mform->addElement('hidden', 'ws_id');
        $mform->setType('ws_id', PARAM_INT);

        $mform->addElement('html', $headinghtml);
        // Give web service a name and short name.!
        $mform->addElement('text', 'wsname', get_string('wsname', 'local_wswizard'));
        $mform->addElement('text', 'wsshortname', get_string('wsshortname', 'local_wswizard'));
        $mform->addRule('wsname', null, 'required', null, 'client');
        $mform->addRule('wsshortname', null, 'required', null, 'client');

        // Enable Web Services and choose protocols!
        $options = array(
            'multiple' => true,
        );
        $mform->addElement('header', 'protocolsheader', get_string('protocolsheader', 'local_wswizard')); // Header.
        $mform->setExpanded('protocolsheader');
        $mform->addElement('autocomplete', 'protocols', get_string('protocols', 'local_wswizard'), $protocols, $options);
        $mform->addRule('protocols', null, 'required', null, 'client');

        // Web Service Users!
        $mform->addElement('header', 'wsuserheader', get_string('wsuserheader', 'local_wswizard'));
        $mform->setExpanded('wsuserheader');
        $mform->addElement('select', 'create_webservice_user', get_string('wsuserchoice', 'local_wswizard'), $userchoice);
        // List of users!
        // If choose existing grab Web service users and display them in a autocomplete field with one selection only!
        $options = array(
            'multiple' => false,
            'noselectionstring' =>  get_string('noselectionstring', 'local_wswizard'),
        );
        $mform->addElement('autocomplete', 'ws_existing_user',
                get_string('selectwsuser', 'local_wswizard'), $wsexistingusers, $options);
        $mform->hideIf('ws_existing_user', 'create_webservice_user', 'eq', '1');
        // Otherwise add new!
        $mform->addElement('text', 'username', get_string('username', 'local_wswizard'));
        $mform->addElement('text', 'email', get_string('email', 'local_wswizard'));
        $mform->addElement('text', 'firstname', get_string('firstname', 'local_wswizard'));
        $mform->addElement('text', 'lastname', get_string('lastname', 'local_wswizard'));
        $mform->hideIf('username', 'create_webservice_user', 'eq', '0');
        $mform->hideIf('email', 'create_webservice_user', 'eq', '0');
        $mform->hideIf('firstname', 'create_webservice_user', 'eq', '0');
        $mform->hideIf('lastname', 'create_webservice_user', 'eq', '0');

        // Web Service Roles!
        $mform->addElement('header', 'wsroleheader', get_string('role_header', 'local_wswizard'));
        $mform->setExpanded('wsroleheader');
        $mform->addElement('select', 'ws_role_select', get_string('ws_role_select', 'local_wswizard'), $userchoice);
        $mform->addElement('autocomplete', 'ws_existing_role', get_string('existingroles', 'local_wswizard'), $wsroles);
        // Else add new!
        // Grab all existing roles shortnames and validate the provided one hasn't been taken!
        $mform->addElement('text', 'role_full_name', get_string('rolename', 'local_wswizard'));
        $mform->addElement('text', 'role_short_name', get_string('shortname', 'local_wswizard'));
        $mform->addElement('text', 'role_description', get_string('description', 'local_wswizard'));
        $mform->hideIf('ws_existing_role', 'ws_role_select', 'eq', '1');
        $mform->hideIf('role_full_name', 'ws_role_select', 'eq', '0');
        $mform->hideIf('role_short_name', 'ws_role_select', 'eq', '0');
        $mform->hideIf('role_description', 'ws_role_select', 'eq', '0');

        $options = array(
            'multiple' => true,
        );

        // Add required functions!
        $mform->addElement('header', 'functionsheader', get_string('functionsheader', 'local_wswizard'));
        $mform->setExpanded('functionsheader');
        $mform->addElement('autocomplete', 'ws_functions',
                get_string('wsfunctions', 'local_wswizard'), $externalfunctions, $options);
        // Additional settings!
        $mform->addElement('header', 'additional_options_header', get_string('additional_options', 'local_wswizard'));
        $mform->setExpanded('additional_options_header');
        // Allow file uploads/downloads!
        $mform->addElement('advcheckbox', 'allow_file_uploads',
                get_string('allow_file_uploads', 'local_wswizard'), null, null, array(0, 1));
        $mform->addElement('advcheckbox', 'allow_file_downloads',
                get_string('allow_file_downloads', 'local_wswizard'), null, null, array(0, 1));
        // Restrict IP address!

        $mform->addElement('text', 'restricted_ip', get_string('restricted_ip', 'local_wswizard'));
        $mform->hideIf('restricted_ip', 'ws_id', 'neq', null);
        // Valid Until Date!
        $mform->addElement('advcheckbox', 'add_expiry_date', get_string('add_expiry_date', 'local_wswizard'));
        $mform->setDefault('add_expiry_date', 0);
        $mform->hideIf('add_expiry_date', 'ws_id', 'neq', null);
        $mform->addElement('date_selector', 'valid_until',
                get_string('dashboard_ws_token_table_column_valid_until', 'local_wswizard'));
        $mform->disabledIf('valid_until', 'add_expiry_date', 'notchecked');
        $mform->hideIf('valid_until', 'ws_id', 'neq', null);
        $mform->addElement('advcheckbox', 'is_enabled',
                get_string('enable_web_service', 'local_wswizard'));
        $mform->setDefault('is_enabled', 1);
        $mform->setType('username', PARAM_USERNAME);
        $mform->setType('email', PARAM_EMAIL);
        $mform->setType('firstname', PARAM_ALPHA);
        $mform->setType('lastname', PARAM_ALPHA);
        $mform->setType('role_full_name', PARAM_ALPHA);
        $mform->setType('role_short_name', PARAM_ALPHANUM);
        $mform->setType('role_description', PARAM_ALPHANUM);
        $mform->setType('wsname', PARAM_ALPHANUM);
        $mform->setType('wsshortname', PARAM_ALPHANUM);
        $mform->setType('restricted_ip', PARAM_HOST);

        $this->add_action_buttons();
    }

    /**
     * Upon submitting the form, validate the given data.
     * @param $data
     * @param $files
     *
     * @return array
     */
    public function validation($data, $files) {
        global $DB;
        // Start of validation!
        $errors = array();
        // This is just to  avoid 'undefined index' notice!
        $username = '';
        $email = '';
        $roleshortname = '';
        $restrictedip = '';
        if (isset($data['username'])) {
            $username = $data['username'];
        }
        if (isset($data['email'])) {
            $email = $data['email'];
        }
        if (isset($data['role_short_name'])) {
            $roleshortname = $data['role_short_name'];
        }
        if (isset($data['restricted_ip'])) {
            $restrictedip = $data['restricted_ip'];
        }
        // Check if a user with the same username exists.
        if ($DB->get_record('user', array('username' => $username))) {
            $errors['username'] = get_string('ws_usernameexists', 'local_wswizard');
        }
        // Check if a user with the same email exists.
        if ($DB->get_record('user', array('email' => $email))) {
            $errors['email'] = get_string('ws_useremailexists', 'local_wswizard');
        }
        // Check that role shortname exists.
        if ($DB->get_record('role', array('shortname' => $roleshortname))) {
            $errors['role_short_name'] = get_string('ws_roleshortnameexists', 'local_wswizard');
        }
        // Ensure restrict IP address is valid.
        if ($restrictedip && preg_match('/^((25[0-5]|(2[0-4]|1[0-9]|[1-9]|)[0-9])(\.(?!$)|$)){4}$/', $restrictedip) == false) {
            $errors['restricted_ip'] = get_string('ws_ipaddress_not_valid', 'local_wswizard');
        }
        return $errors;
    }
}
