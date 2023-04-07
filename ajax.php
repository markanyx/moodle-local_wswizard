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
require_login();
require_admin();
require_sesskey();
$action = required_param('action', PARAM_TEXT);

global $USER, $DB;

switch ($action)
{
    case 'deleteToken':

        // Token id.
        $tokenid = required_param('id', PARAM_INT);

        // Used to get webservice_id for log.
        $record = $DB->get_record('external_tokens', array('id' => $tokenid));
        $webserviceid = $record->externalserviceid;

        // Delete the token.
        $delete = $DB->delete_records('external_tokens', array('id' => $tokenid));


        $logdata = array(
            'webservice_id' => $webserviceid,
            'action' => get_string('ws_log_delete_token', 'local_wswizard', $tokenid),
            'createdby' => $USER->id,
            'timecreated' => time()
        );
        $logs = new \local_wswizard\Logs();
        $logs->insert($logdata);

        echo json_encode($delete);
        break;

    case 'deleteFunction':
        $id = required_param('id', PARAM_INT);
        $functionname = required_param('functionname', PARAM_RAW);

        $delete = $DB->delete_records('external_services_functions',
            array('externalserviceid' => $id, 'functionname' => $functionname));


        $logdata = array(
            'webservice_id' => $id,
            'action' => get_string('ws_log_delete_function', 'local_wswizard', $functionname),
            'createdby' => $USER->id,
            'timecreated' => time()
        );
        $logs = new \local_wswizard\Logs();
        $logs->insert($logdata);

        echo json_encode($delete);
        break;

    case 'deleteWebservice':
        $serviceid = required_param('id', PARAM_INT);
        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records('external_services_users', array('externalserviceid' => $serviceid));
            $DB->delete_records('external_services_functions', array('externalserviceid' => $serviceid));
            $DB->delete_records('external_tokens', array('externalserviceid' => $serviceid));
            $DB->delete_records('external_services', array('id' => $serviceid));
            $transaction->allow_commit();

            $logdata = array(
                'webservice_id' => $serviceid,
                'action' => get_string('ws_log_delete_webservice', 'local_wswizard'),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            $logs = new \local_wswizard\Logs();
            $logs->insert($logdata);

            echo json_encode(true);
        } catch (moodle_exception $e) {
            $logdata = array(
                'webservice_id' => $serviceid,
                'action' => $e->getMessage(),
                'createdby' => $USER->id,
                'timecreated' => time()
            );
            debugging($e->getMessage(), E_ERROR, $e->getTrace());
        }
        break;

    case 'enableWebservice':
        $serviceid = required_param('id', PARAM_INT);

        $webservice = $DB->get_record('external_services', array('id' => $serviceid));
        // Switches between enabled/disable.
        $newstate = null;
        if ($webservice->enabled) {
            $webservice->enabled = 0;
            $newstate = get_string('ws_log_change_webservice_state_disabled', 'local_wswizard');
        } else {
            $webservice->enabled = 1;
            $newstate = get_string('ws_log_change_webservice_state_enabled', 'local_wswizard');
        }
        $update = $DB->update_record('external_services', $webservice);

        $logdata = array(
            'webservice_id' => $serviceid,
            'action' => $newstate,
            'createdby' => $USER->id,
            'timecreated' => time()
        );
        $logs = new \local_wswizard\Logs();
        $logs->insert($logdata);

        echo json_encode($update);

        break;

    case 'updateUploadFiles':
        $serviceid = required_param('id', PARAM_INT);
        $webservice = $DB->get_record('external_services', array('id' => $serviceid));

        // Switches between enabled/disable.
        $newstate = null;
        if ($webservice->uploadfiles) {
            $webservice->uploadfiles = 0;
            $newstate = get_string('ws_log_change_uploadfiles_state_disabled', 'local_wswizard');
        } else {
            $webservice->uploadfiles = 1;
            $newstate = get_string('ws_log_change_uploadfiles_state_enabled', 'local_wswizard');
        }
        $update = $DB->update_record('external_services', $webservice);

        $logdata = array(
            'webservice_id' => $serviceid,
            'action' => $newstate,
            'createdby' => $USER->id,
            'timecreated' => time()
        );
        $logs = new \local_wswizard\Logs();
        $logs->insert($logdata);

        echo json_encode($update);

        break;

    case 'updateDownloadFiles':
        $serviceid = required_param('id', PARAM_INT);
        $webservice = $DB->get_record('external_services', array('id' => $serviceid));

        // Switches between enabled/disable.
        if ($webservice->downloadfiles) {
            $webservice->downloadfiles = 0;
            $newstate = get_string('ws_log_change_downloadfiles_state_disabled', 'local_wswizard');
        } else {
            $webservice->downloadfiles = 1;
            $newstate = get_string('ws_log_change_downloadfiles_state_enabled', 'local_wswizard');
        }

        $logdata = array(
            'webservice_id' => $serviceid,
            'action' => $newstate,
            'createdby' => $USER->id,
            'timecreated' => time()
        );
        $logs = new \local_wswizard\Logs();
        $logs->insert($logdata);
        $update = $DB->update_record('external_services', $webservice);
        echo json_encode($update);

        break;
}
