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
 * @copyright  2016 TNG Consulting Inc. - www.tngcosulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Mail Test';
$string['pluginname_help'] = 'Mail Test will verify the email settings for this Moodle site by sending a test email message to the address you specify. For Site Administrators only.';
$string['from'] = '{$a->type}: <strong>{$a->email}</strong> (<a href="{$a->url}">{$a->label}</a>)';
$string['credit'] = 'Michael Milette - <a href="http://www.tngconsulting.ca/">TNG Consulting Inc.</a>';

$string['heading'] = 'Email configuration test';
$string['recipientisrequired'] = 'You must specify the recipient\'s email address.';
$string['errorsend'] = 'The test email message could not be delivered to the mail server. Check your <a href="../../admin/settings.php?section=messagesettingemail" target="blank">Email settings</a>.';
$string['fromemail'] = 'From email address';
$string['toemail'] = 'To email address';
$string['youremail'] = 'Your email address';
$string['sendtest'] = 'Send a test message';
$string['sendmethod'] = 'Email send method';
$string['sentmail'] = 'Moodle succesfully delivered the test message to the SMTP mail server.';
$string['sentmailphp'] = 'The Moodle test message was successfully accepted by PHP Mail.';
$string['registered'] = 'Registered user ({$a}).';
$string['notregistered'] = 'Not registered or not logged in.';
$string['phpmethod'] = 'PHP default method';
$string['smtpmethod'] = 'SMTP hosts: {$a}';
$string['message'] = '<p>This is a test message. Please disregard.</p>
<p>If you received this email, it means that you have succesfully configured your Moodle site\'s email settings.</p>
<hr><p><strong>Additional User Information</strong></p>
<ul>
<li><strong>Registration status :</strong> {$a->regstatus}</li>
<li><strong>Preferred language :</strong> {$a->lang}</li>
<li><strong>User\'s web browser :</strong> {$a->browser}</li>
<li><strong>Message submitted from :</strong> {$a->referer}</li>
<li><strong>Moodle version :</strong> {$a->release}</li>
<li><strong>User\'s IP address :</strong> {$a->ip}</li>
</ul>';
