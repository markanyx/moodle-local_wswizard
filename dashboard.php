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
use local_wswizard\web_service_data;
use local_wswizard\web_service_wizard;
require_login(1, false);
require_admin();
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);

global $CFG, $OUTPUT, $USER, $PAGE, $DB;

$PAGE->requires->jquery();

$PAGE->requires->js_call_amd('local_wswizard/dashboardModal');
$PAGE->requires->js_call_amd('local_wswizard/add_functions_to_webservice');
$PAGE->requires->js_call_amd('local_wswizard/add_tokens_to_webservice');
$PAGE->requires->js_call_amd(
    'local_wswizard/add_functions_to_webservice',
    'initModal'
);
$PAGE->requires->js_call_amd(
    'local_wswizard/add_tokens_to_webservice',
    'tokensModal'
);
$PAGE->requires->js('/local/wswizard/js/copytext.js', true);



require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

$PAGE->requires->js(
    new moodle_url('https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js'),
    true);
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css'));

$context = CONTEXT_SYSTEM::instance();

echo \local_wswizard\Base::page(
    $CFG->wwwroot . '/local/wswizard/dashboard.php',
    get_string('wswizard', 'local_wswizard'),
    get_string('wswizard', 'local_wswizard'),
    $context);

echo $OUTPUT->header();
$all_records = $DB->get_records('external_services');
// Remove built-in webservices
$records = array_filter(array_map(function($r){
    if ($r->component == ''){
        return $r;
    }
},$all_records));


foreach ($records as $rec){
        $test = new web_service_wizard($rec->id);
        $rec->alltokens = array_values($test->get_tokens_from_webservice_id($rec->id));
        $rec->allFunctions = array_values($test->get_functions_from_webservice_id($rec->id));
        $rec->authUsers = array_values($test->get_authorised_users_from_ws_id($rec->id));
        $rec->hasAuthUsers = count($rec->authUsers); // For mustache to display specific div.
        $rec->ws_role_id = $test->get_role_id();
        $rec->role_name = $test->get_role_name_from_id($rec->ws_role_id);

}
$records = array_values($records);


// For mustache navbar to display the first webservice as active.
if ($records[0]) {
    $records[0]->activeTab = true;
}

$data = [
    'service' => $records,
    'edit_link' => new moodle_url('/local/wswizard/ws_edit.php')
];

echo $OUTPUT->render_from_template("local_wswizard/dashboard", $data);

echo $OUTPUT->footer();
