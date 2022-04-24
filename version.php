<?php
// This file is part of the eMailTest plugin for Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information for eMailTest (also called MailTest).
 *
 * @package    local_mailtest
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_mailtest';  // To check on upgrade, that module sits in correct place.
$plugin->version   = 2022042400;        // The current module version (Date: YYYYMMDDXX).
$plugin->requires  = 2013040500;        // Requires Moodle version 2.5.
$plugin->release   = '2.0.0';
$plugin->maturity  = MATURITY_STABLE;
$plugin->cron      = 0;
