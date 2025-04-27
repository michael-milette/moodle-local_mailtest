<?php
// This file is part of eMailTest plugin for Moodle - https://moodle.org/
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
// along with eMailTest.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'local_mailtest', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_mailtest
 * @copyright  2015-2025 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['additionalinfo'] = 'Additional information:';
$string['alwaysshowlog'] = 'Always show log of communications with mail server, even if there are no errors.';
$string['bimidmarcfailure'] = 'BIMI failure due to one or more DMARC dependency failures.';
$string['bimiinvalidlogo'] = 'Missing BIMI logo: {$a}.';
$string['biminorecordfound'] = 'BIMI record is missing.';
$string['bimipctinvalid'] = 'DMARC PCT value must be set to 100 for BIMI.';
$string['bimirecordfound'] = 'BIMI record found.';
$string['bimitagsfound'] = 'BIMI tags valid.';
$string['checkingdomain'] = 'DNS security check for {$a}:';
$string['commoncommissues'] = '
<li>Your Moodle site may be blocked by a firewall, preventing communication with your mail server.</li>
<li>You may need to add the IP address of your website to a list of allowed IP addresses in your mail server\'s configuration.</li>
<li>Ensure that your SMTP server is up and running.</li>
';
$string['commondeliveryissues'] = 'If the email is not delivered within about 15-30 minutes, check the following:</p>
<ul>
<li>Ensure that the TO email address is correct.</li>
<li>Check the recipients spam/junk mail.</li>
<li>Check the recipients mailbox to make sure it is not full.</li>
<li>Check the mailbox of your no-reply email address to see if the message bounced (was not delivered) for any reason.</a></li>
<li>Check that your email content is not being flagged as spam/junk by any filter on your mail server or on the recipient\'s mail server.</a></li>
<li>Make sure that the IP address of your mail server is not blacklisted by any mail providers.</a></li>
{$a}
</ul>';
$string['connectionlog'] = 'Communications log with mail server';
$string['credit'] = 'Michael Milette - <a href="https://www.tngconsulting.ca/">TNG Consulting Inc.</a>';
$string['divertallemails'] = 'Divert all emails';
$string['divertedto'] = 'Diverted to {$a}';
$string['dkiminvalidrecord'] = 'DKIM record must contains valid v, k and p tags.';
$string['dkimmissingselector'] = 'DKIM selector setting has not been configured.';
$string['dkimnorecordfound'] = 'DKIM record is missing.';
$string['dkimrecordfound'] = 'DKIM record found.';
$string['dkimselectorconfigured'] = 'DKIM selector setting is configured.';
$string['dkimspffailed'] = 'DMARC requires that SPF or DKIM records be configured.';
$string['dkimvalidrecord'] = 'DKIM record format is valid.';
$string['dmarcnorecordfound'] = 'DMARC record is missing.';
$string['dmarcpctinvalid'] = 'DMARC pct value is not within range.';
$string['dmarcrecordfound'] = 'DMARC record found.';
$string['dmarcruainvalid'] = 'DMARC rua value is not formatted correctly.';
$string['dmarcrufinvalid'] = 'DMARC ruf value is not formatted correctly.';
$string['dmarctagsfound'] = 'DMARC required tags found.';
$string['emailfail'] = 'Failed to send email message';
$string['errorcommunications'] = '<p>Moodle could not communicate with your mail server.</p><p><strong>Recommendations:</strong></p><p>Start by checking your Moodle <a href="{$a}" target="_blank">SMTP mail settings</a>.</p><p>If they look correct, check your SMTP Server and/or firewall settings to ensure that they are configured to accept SMTP connections from your Moodle web server and your no-reply email address. For more help, see the <a href="https://github.com/michael-milette/moodle-local_mailtest/blob/master/README.md#faq" target="_blank">FAQ section</a> in the documentation.</p>';
$string['errorsend'] = '<p>The test email message could not be delivered to the mail server.</p><p><strong>Recommendations:</strong></p><p>Check your Moodle <a href="{$a}" target="_blank">Email settings</a>. For more help, see the <a href="https://github.com/michael-milette/moodle-local_mailtest/blob/master/README.md#faq" target="_blank">FAQ section</a> in the documentation. You may also wish to reference the list of SMTP codes in <a href="https://www.rfc-editor.org/rfc/rfc5321.html#section-4.2.3" target="_blank">RFC 5321</a> and <a href="https://datatracker.ietf.org/doc/html/rfc4954#section-6" target="_blank">RFC 4954</a>.</p>';
$string['errorunknown'] = '<p>An undiagnosed error occured.</p><p><strong>Recommendations:</strong></p><p>Refer to the communications log below. You may find it useful to reference the list of SMTP codes in <a href="https://www.rfc-editor.org/rfc/rfc5321.html#section-4.2.3" target="_blank">RFC 5321</a> and <a href="https://datatracker.ietf.org/doc/html/rfc4954#section-6" target="_blank">RFC 4954</a>.</p>';
$string['failaccessdenied'] = '<li>Connect to the mail server but it closed then closed connection.</li>';
$string['failclosedport'] = 'Server port {$a} is closed. Did you specify the correct :port number?';
$string['failcredentials'] = '<li>Authentication credentials may be invalid or missing. Make sure that you have entered the correct login information.</li>';
$string['faildnslookup'] = 'DNS lookup failed. Ensure that \'<strong>{$a}</strong>\' resolves to the address of a mail server.';
$string['failmissingport'] = 'You may need to specify a :port number for "{$a}" type connections.';
$string['failmissingprotocol'] = 'You may need to specify a secure protocol type (SSL/TLS) with port \'{$a}\'.';
$string['failoutboundsmtpblocked'] = 'Something is blocking connectivity of outbound SMTP connections. Is there a firewall blocking your connection to the mail server?';
$string['failphpmailer'] = 'There may be issues with your installation of Moodle LMS. One possible reason is incorrect owner/group permissions of the application files.';
$string['failphpmailerconfig'] = 'The mail service on the your Moodle web server may not be running or may be incorrectly configured.';
$string['failprotocolmismatch'] = 'You may have a mismatch between the protocol \'{$a->protocol}\' and the port \'{$a->port}\'.';
$string['failunknownmailbox'] = '<li>The FROM mailbox was not found, is not accessible, or was rejected for policy reasons. Make sure that both the TO and FROM email addresses are valid and exist and that the \'no-reply\' address is a real mailbox that exists on your mail server.</li>';
$string['from'] = '{$a->type}: <strong>{$a->email}</strong> (<a href="{$a->url}">{$a->label}</a>)';
$string['fromemail'] = 'From email address';
$string['heading'] = 'Email configuration test';
$string['iconlabel'] = 'Security check for {$a}';
$string['message'] = '<p>This is a test message. Please disregard.</p>
<p>If you received this email, it means that you have successfully configured your Moodle site\'s email settings.</p>
<hr><p><strong>Additional User Information</strong></p>
<ul>
<li><strong>Registration status :</strong> {$a->regstatus}</li>
<li><strong>Preferred language :</strong> {$a->lang}</li>
<li><strong>User\'s web browser :</strong> {$a->browser}</li>
<li><strong>Message submitted from :</strong> {$a->referer}</li>
<li><strong>Moodle version :</strong> {$a->release}</li>
<li><strong>User\'s IP address :</strong> {$a->ip}</li>
</ul>';
$string['noemailever'] = '<p>Email on this site has been disabled by way of <strong>$CFG-&gt;noemailever = true;</strong>.</p>';
$string['nologavailable'] = '<p>Logging is not available when using PHP mail() function. However, may find logs on your server. Most common locations on Linux include:</p>
<ul>
<li>/var/log/maillog</li>
<li>/var/log/mail.log</li>
<li>/var/adm/maillog</li>
<li>/var/adm/syslog/mail.log</li>
</ul>
<p>Alernatively, a custom location may be specified using the mail.log setting in php.ini.</p>';
$string['notregistered'] = 'Not registered or not logged in.';
$string['phpmethod'] = 'PHP default method';
$string['pluginname'] = 'eMail Test';
$string['pluginname_help'] = 'eMail Test will verify the email settings for this Moodle site by sending a test email message to the address you specify. For Site Administrators only.';
$string['primaryadminemail'] = 'Primary admin email';
$string['privacy:metadata'] = 'The eMail Test plugin does not store any personal data about any user.';
$string['registered'] = 'Registered user ({$a}).';
$string['sendmethod'] = 'Email send method';
$string['sendtest'] = 'Send a test message';
$string['sentmail'] = 'Moodle successfully delivered the test message to the SMTP mail server.';
$string['sentmailphp'] = 'The Moodle test message was successfully accepted by PHP Mail.';
$string['server'] = 'Server: {$a}';
$string['smtpmethod'] = 'SMTP hosts: {$a}';
$string['spfinvalidrecord'] = 'SPF record must contains at least one mechanism.';
$string['spfnorecordfound'] = 'SPF record is missing.';
$string['spfrecordfound'] = 'SPF record found.';
$string['spfvalidrecord'] = 'SPF record format is valid.';
$string['testing'] = 'Testing email configuration';
$string['toemail'] = 'To email address';
$string['winsyslog'] = 'Event Log on Windows';
$string['youremail'] = 'Your email address';
