<?php
// This file is part of eMailTest plugin for Moodle - http://moodle.org/
//
// eMailTest is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// eMailTest is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with eMailTest.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds eMail Test link to the Site Administration > Server menu. There are no settings for this plugin.
 *
 * @package    local_mailtest
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    if ($CFG->branch >= 32) { // Moodle 3.2 and later.
        $section = 'email';
    } else { // Up to and including Moodle 3.1.x .
        $section = 'server';
    }
    $ADMIN->add($section, new admin_externalpage('local_mailtest',
            get_string('pluginname', 'local_mailtest'),
            new moodle_url('/local/mailtest/')
    ));
}
