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

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '../../../config.php');
require_admin();
/**
 * Exntended navigation for the plugin.
 * @param global_navigation $navigation
 *
 * @return void
 */
function local_wswizard_extend_navigation(global_navigation $navigation) {
    global $USER;

    $context = context_system::instance();
    if (is_siteadmin($USER)) {
        $node = $navigation->find('local_wswizard', navigation_node::TYPE_CUSTOM);
        if (!$node) {
            $node = $navigation->add(
                get_string('pluginname', 'local_wswizard'),
                new moodle_url('/local/wswizard/dashboard.php'),
                navigation_node::TYPE_CUSTOM,
                get_string('pluginname', 'local_wswizard'),
                'local_wswizard',
                new pix_icon('i/grades', '')
            );
        }
    }
}
