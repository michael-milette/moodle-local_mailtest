<?php
// This file is part of MailTest for Moodle - http://moodle.org/
//
// MailTest is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// MailTest is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with MailTest.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'local_mailtest', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_mailtest
 * @copyright  TNG Consulting Inc. - www.tngcosulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Mail Test';
$string['pluginname_help'] = 'Mail Test will check the Moodle site\'s email configuration by sending an email message to a specified email address. For Site Administrators only.';
$string['credit'] = 'Michael Milette - <a href="http://www.instruxmedia.com/">TNG Consulting Inc.</a>';

$string['heading'] = 'Email configuration test';
$string['recipientisrequired'] = 'You must specify the recipient\'s email address.';
$string['errorsend'] = 'The test email message could not be delivered to the SMTP server. Check your <a href="../../admin/settings.php?section=messagesettingemail" target="blank">SMTP settings</a>.';
$string['sendtest'] = 'Send a test message';
$string['sentmail'] = 'The test message was successfully delivered to the SMTP server.';
$string['registered'] = 'Registered user ({$a}).';
$string['notregistered'] = 'Not registered or not logged in.';
$string['message'] = '<p>This is a test message. Please disregard.</p>
<hr><p><strong>Additional User Information</strong></p>
<ul>
<li><strong>Registration status :</strong> {$a->regstatus}</li>
<li><strong>Preferred language :</strong> {$a->lang}</li>
<li><strong>User\'s web browser :</strong> {$a->browser}</li>
<li><strong>Message submitted from :</strong> {$a->referer}</li>
<li><strong>User IP address :</strong> {$a->ip}</li>
</ul>';
