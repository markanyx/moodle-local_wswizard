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
require_once(dirname(__FILE__) . '../../../config.php');
require_login(1, false);
require_admin();
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
use local_wswizard\web_service_data;
use local_wswizard\forms\ws_form;

function display_page() {
    global $CFG, $OUTPUT, $USER, $PAGE, $DB;
    if (!is_siteadmin($USER)) {
        redirect($CFG->wwwroot);
    }


    $mform = new ws_form();
    $PAGE->requires->js('/local/wswizard/js/wswizard.js');
    $context = CONTEXT_SYSTEM::instance();

    // Form processing and displaying is done here.
    if ($mform->is_cancelled()) {
        // Handle form cancel operation, if cancel button is present on form.
        redirect($CFG->wwwroot . '/local/wswizard/dashboard.php#nav-1');
    } else {
        if ($fromform = $mform->get_data()) {
            /* Begin processing form data
             *
             * !!fromform data!!
             * wsnng = The oame: string = Webservice name
             * wsshortname: string = Webservice short name
             * protocols: array = selected webservice protocols
             * create_webservice_user: int|bool = 0 (false) means select an existing user, 1 (true) means create a new user
             * ws_existing_user: int = id of the existing user selected
             * username: string = username of new user to create
             * email: string = email of new user to create
             * firstname: string = firstname of new user to create
             * lastname: string = lastname of new user to create
             * ws_role_select: int|bool = 0 (false) means select an existing user, 1 (true) means create a new user
             * ws_existing_role: int = Existing role id
             * role_full_name: string = Full name of role
             * role_short_name: string = Short name of the role
             * role_description: string = Description of thee role
             * ws_functions: array = all the functions selected for the web service user
             * allow_file_uploads: bool = Whether to allow web service user to upload files
             * allow_file_downloads: bool =  Whether to allow web service user to download files
             * restricted_ip: strinly IP address the web service user can execute calls from
             * valid_until: The date the token is valid until
             *
             * !!Objective!!
             * We need to create the following objects to successfully create and activate a webservice user & generate tokens
             * 1) activate selected web service protocols
             * 2) get a user id from either a selected existing user or from one created
             * 3) Assign selected role or create a new system level role for the user based on inserted data
             * 4) Assign the capabilities for the functions to the created role
             * 5) Assign selected/created user to role.
             * 6) If the external service does not already exist,
             *    create a new one. Assign whether the created service can upload & download files
             * 7) Assign selected functions to external service
             * 8) Assign the user the external services user table, assign IP restrictions or valid until date onto the user
             * 9) Generate a token if it does not already exist
             *    for external service & user. Also ensure to assign the IP restriction and valid until date here
             * 10) Create a wswizard web_service_user object.
             * 11) Redirect to web service dashboard page.
             *
             * */

            /*
             * check protocols, enable any unenabled protocols in Moodle configuration, return stringified protocols to insert
             * into custom web service user  data object
             */
            $base = new local_wswizard\Base();
            $datacontroller = new web_service_data();

            // Get or create a user.
            $fromform->create_webservice_user == 0 ? $wsuserid = $fromform->ws_existing_user
                : $wsuserid = $datacontroller->create_ws_user($fromform->username, $fromform->email, $fromform->firstname, $fromform->lastname);

            // Get or create ws role depending on the form input.
            $fromform->ws_role_select == 0 ? $wsroleid = $fromform->ws_existing_role
                : $wsroleid = $datacontroller->create_webservice_role($fromform->role_full_name, $fromform->role_short_name, $fromform->role_description);

            // Assign capabilities for functions to role.
            $datacontroller->assign_ws_capabilities_to_role($wsroleid, $fromform->ws_functions, $fromform->protocols, $context->id);

            // Assign the role to the user.
            role_assign($wsroleid, $wsuserid, $context->id);

            // Create  or get webservice.
            $webserviceobjectid
                = $datacontroller->edit_or_create_webservice_object(
                    null, $fromform->wsshortname, $fromform->wsname,
                    $fromform->allow_file_uploads, $fromform->allow_file_uploads,
                    $fromform->is_enabled);

            // Set webservice role.

            $datacontroller->set_webservice_role($wsroleid, $webserviceobjectid);

            /*
             * Assign  functions to WS.
             */
            $datacontroller->assign_functions_to_webservice($webserviceobjectid, $wsuserid, $fromform->ws_functions);

            // Check to se eif a valid until date was set.
            $validuntildate = $fromform->valid_until;
            if (!$fromform->add_expiry_date) {
                $validuntildate = null;
            }
            // Create token.
            $token = $datacontroller->create_webservice_token($webserviceobjectid, $wsuserid, $context, $fromform->restricted_ip, $validuntildate);

            ($webserviceobjectid > 0)
                ?
                redirect($CFG->wwwroot . '/local/wswizard/dashboard.php#nav-' . $webserviceobjectid)
                :
                redirect($CFG->wwwroot . '/local/wswizard/dashboard.php#nav-1');
        } else {
            /* This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
             or on the first display of the form. */
            // Set default data (if any).
            $mform->set_data($mform);
        }
    }
    echo \local_wswizard\Base::page($CFG->wwwroot
        . '/local/wswizard/ws.php', get_string('wswizard', 'local_wswizard'), get_string('wswizard', 'local_wswizard'), $context);

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}

display_page();
