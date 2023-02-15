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
use local_wswizard\web_service_wizard;
global $CFG, $OUTPUT, $USER, $PAGE, $DB;

require_login(1, false);
require_admin();

$context = CONTEXT_SYSTEM::instance();
$PAGE->requires->jquery();

// DataTable inclusion.
$PAGE->requires->js(
    new moodle_url('https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js'),
    true);
$PAGE->requires->css(
    new moodle_url('https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css')
);

echo \local_wswizard\Base::page(
    $CFG->wwwroot . '/local/wswizard/dashboard.php',
    get_string('ws_log_page_title', 'local_wswizard'),
    get_string('ws_log_page_header', 'local_wswizard'),
    $context);

echo $OUTPUT->header();

// Adds Datable functionality.
$initjs = "$(document).ready(function() { $('#logsTable').DataTable({    'order': [[ 0, 'desc' ]]});   });";
echo html_writer::script($initjs);

$logs = new \local_wswizard\Logs();
$alllogs = $logs->get_all();
// Display a date on the table, not timestamp.

$data = [
    'logs' => array_values($alllogs)
];

echo $OUTPUT->render_from_template("local_wswizard/logs_report", $data);

echo $OUTPUT->footer();
