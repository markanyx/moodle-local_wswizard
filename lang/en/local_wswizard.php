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
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Web Service Wizard';
$string['wswizard'] = 'Web Service Wizard';
$string['wswizard_add_new'] = 'Add new web service with webservice wizard';
$string['wswizard_dashboard'] = 'Web services dashboard';

$string['protocolsheader'] = 'Protocols';
$string['protocols'] = 'Please choose at least one protocol to activate';
$string['wsuserheader'] = 'Web Service user';
$string['wsuserchoice'] = 'Please choose if you will select an existing user or create a new one for this Web Service';
$string['selectwsuser'] = 'Please choose a user for the Web Service';

$string['username'] = 'Username';
$string['email'] = 'Email';
$string['firstname'] = 'Firstname';
$string['lastname'] = 'Lastname';

$string['role_header'] = 'Web service role';
$string['ws_role_select'] = 'Please choose if you will select an existing role or create a new one for this Web Service';
$string['existingroles'] = 'Please choose a role for the user of this Web Service';

$string['rolename'] = 'Please enter the role name';
$string['shortname'] = 'Please enter the role shortname';
$string['description'] = 'Please enter a description for the role';


$string['capabilities_header'] = 'Web service capabilities';
$string['capabilities'] = 'Please choose the list of capabilities for this Web service';

$string['wsheader'] = 'Web service';
$string['wsname'] = 'Please enter the Web Service name';
$string['wsshortname'] = 'Please enter the Web Service shortname';
$string['wsoptions'] = 'Please select any applicable options for this Web Service';

$string['functionsheader'] = 'Web Service functions';
$string['wsfunctions'] = 'Please choose the list of functions for this Web service';
$string['ws_form_heading'] = 'Create a new webservice user/token pair';
$string['unexpected_error'] = 'An error has occured, please try again.';

// Dashboard!
$string['dashboard_token_title'] = 'Tokens';
$string['dashboard_ws_name'] = 'Name:';
$string['dashboard_ws_shortname'] = 'Shortname:';
$string['dashboard_ws_enabled'] = 'Enabled';
$string['dashboard_ws_not_enabled'] = 'Not Enabled';
$string['dashboard_ws_edit'] = 'Edit Webservice';
$string['dashboard_ws_delete'] = 'Delete Webservice';
$string['dashboard_ws_can_upload'] = 'Can Upload Files:';
$string['dashboard_ws_yes'] = 'Yes';
$string['dashboard_ws_no'] = 'No';
$string['dashboard_ws_none'] = 'None';
$string['dashboard_ws_authorised_users'] = 'Webservice User:';
$string['dashboard_ws_can_download'] = 'Can Download Files:';
$string['dashboard_ws_required_capability'] = 'Required Capability:';
$string['dashboard_ws_add_new'] = 'Add New';
$string['dashboard_ws_role'] = 'Role:';


$string['dashboard_ws_token_table_column_token'] = 'Token';
$string['dashboard_ws_token_table_column_user'] = 'User';
$string['dashboard_ws_token_table_column_ip_restriction'] = 'IP Restriction';
$string['dashboard_ws_token_table_column_valid_until'] = 'Valid Until';
$string['dashboard_ws_token_table_column_creator'] = 'Creator';
$string['dashboard_ws_token_table_column_actions'] = 'Actions';
$string['dashboard_ws_token_table_no_data'] = 'No tokens to display';
$string['dashboard_ws_token_no_expiration'] = 'No expiration';

$string['dashboard_ws_functions'] = 'Functions';
$string['dashboard_ws_functions_table_column_function'] = 'Function';
$string['dashboard_ws_functions_table_column_description'] = 'Description';
$string['dashboard_ws_functions_table_column_capabilities'] = 'Capabilities';
$string['dashboard_ws_functions_table_column_actions'] = 'Actions';
$string['dashboard_ws_functions_table_no_data'] = 'No functions to display';

// Dashboard Modal!
$string['dashboard_modal_delete'] = 'Delete';
$string['dashboard_modal_change_state'] = 'Change State';
$string['dashboard_delete_token_title'] = 'Confirmation';
$string['dashboard_delete_token_body'] = 'Do you really want to delete this web service token?';
$string['dashboard_delete_function_title'] = 'Confirmation';
$string['dashboard_delete_function_body'] = 'Do you really want to delete this web service function?';
$string['dashboard_delete_webservice_title'] = 'Confirmation';
$string['dashboard_delete_webservice_body'] = 'Do you really want to delete this web service? The user and role associated with the webservice will not be deleted';
$string['dashboard_update_state_title'] = 'Confirmation';
$string['dashboard_update_state_body'] = 'Do you really want to change the state of this web service?';
$string['no_ws_found'] = 'No webservices found';
$string['additional_options'] = 'Additional Options';
$string['file_permissions'] = 'Webservice file permissions';
$string['allow_file_uploads'] = "Allow file uploads";
$string['allow_file_downloads'] = "Allow file downloads";
$string['restricted_ip'] = 'Restrict IP address';

// Form validation!
$string['ws_nametaken'] = 'A web service with this name already exists! Please choose a new one.';
$string['ws_shortnametaken'] = 'A web service with this short name already exists! Please choose a new one.';
$string['ws_usernameexists'] = 'A user with this username already exists. Please choose a new username.';
$string['ws_useremailexists'] = 'A user with this email already exists. Please choose a new email address.';
$string['ws_roleshortnameexists'] = 'A role with this shortname already exists. Please choose a new one.';
$string['ws_ipaddress_not_valid'] = 'this ip address is not valid. Please enter a valid IP address.';

$string['add_new_functions'] = 'Add new functions';
$string['add_expiry_date'] = 'Add an expiry date for the webservice?';
$string['add_new_token'] = 'Add new token';
$string['enable_web_service'] = 'Enable this webservice?';

// Logs!
$string['ws_log_delete_token'] = 'Deleted token with id {$a}';
$string['ws_log_delete_function'] = 'Removed function {$a} from webservice';
$string['ws_log_delete_webservice'] = 'Deleted webservice';
$string['ws_log_change_webservice_state_disabled'] = 'Changed webservice state to disabled';
$string['ws_log_change_webservice_state_enabled'] = 'Changed webservice state to enabled';
$string['ws_log_delete_webservice'] = 'Deleted webservice';
$string['ws_log_change_uploadfiles_state_enabled'] = 'Changed upload files state to enabled';
$string['ws_log_change_uploadfiles_state_disabled'] = 'Changed upload files state to disabled';
$string['ws_log_change_downloadfiles_state_enabled'] = 'Changed download files state to enabled';
$string['ws_log_change_downloadfiles_state_disabled'] = 'Changed download files state to disabled';
$string['ws_log_create_token'] = 'Created token with id {$a->tokenid} for userid {$a->for_wsuserid}';
$string['ws_log_change_functions'] = 'Changed functions';
$string['ws_log_create_webservice_user'] = 'Created webservice user with id {$a}';
$string['ws_log_create_webservice_role'] = 'Created webservice role with id {$a}';
$string['ws_log_assign_capabilities_to_role'] = 'Assigned capabilities to role with id {$a}';
$string['ws_log_create_webservice'] = 'Created webservice';
$string['ws_log_update_webservice'] = 'Updated webservice';
$string['ws_log_set_webservice_role'] = 'Set webservice role with id {$a}';

// Log Table!
$string['ws_log_page_title'] = 'Web Service Wizard Logs';
$string['ws_log_page_header'] = 'Web Service Wizard Logs';
$string['ws_log_column_id'] = 'ID';
$string['ws_log_column_action'] = 'Action';
$string['ws_log_column_ws_id'] = 'Webservice ID';
$string['ws_log_column_ws_name'] = 'Webservice Name';
$string['ws_log_column_timecreated'] = 'Time Created';
$string['ws_log_column_createdby_id'] = 'Created By ID';
$string['ws_log_column_createdby_username'] = 'Created By Username';

// Capability!
$string['ws_does_not_have_capability'] = 'Sorry, you do not have the required local_wswizard:use capability to view this page.';


