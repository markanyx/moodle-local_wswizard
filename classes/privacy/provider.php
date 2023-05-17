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

namespace local_wswizard\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\approved_userlist;

if (interface_exists('\core_privacy\local\request\userlist')) {
    interface my_userlist extends \core_privacy\local\request\userlist {
    }
} else {
    interface my_userlist {
    }
}

class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\data_provider {

    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table(
            'local_wswizard_logs',
            [
                'createdby' => 'privacy:metadata:local_wswizard_logs:createdby',
                'ip' => 'privacy:metadata:local_wswizard_logs:ip',
                'webservice_id' => 'privacy:metadata:local_wswizard_logs:webservice_id'
            ],
            'privacy:metadata:local_wswizard_logs'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists('local_wswizard_logs', ['createdby' => $userid])) {
            $sql = "SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel";
            $params = ['contextlevel' => CONTEXT_SYSTEM];
            $contextlist->add_from_sql($sql, $params);

        }
        return $contextlist;
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $userinsql = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $sql = "createdby {$userinsql}";
        $DB->delete_records('local_wswizard_logs', $sql);
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param userid $userid The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {

    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('local_wswizard_logs', ['createdby' => $userid]);
    }
}
