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
global $ADMIN, $CFG, $DB, $USER;
if(is_siteadmin($USER)){
$ADMIN->add('webservicesettings', new admin_externalpage('local_wswizard_form' ,
        get_string('wswizard_add_new', 'local_wswizard'), $CFG->wwwroot . '/local/wswizard/ws.php'));
$existingwebservices = $DB->get_records('external_services');
$dashboardroot = $CFG->wwwroot . '/local/wswizard/dashboard.php';
$logsroot = $CFG->wwwroot . '/local/wswizard/logs.php';
if ($existingwebservices) {
    $dashboardroot = $CFG->wwwroot . '/local/wswizard/dashboard.php#nav-' . array_values($existingwebservices)[0]->id;
}
$ADMIN->add('webservicesettings', new admin_externalpage('local_wswizard     _dashboard',
        get_string('wswizard_dashboard', 'local_wswizard'), $dashboardroot));

$ADMIN->add('webservicesettings', new admin_externalpage('local_wswizard_logs',
    get_string('ws_log_page_title', 'local_wswizard'), $logsroot));

}