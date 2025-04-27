<?php
// This file is part of MailTest for Moodle - https://moodle.org/
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
// along with MailTest.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Displays the form and processes the form submission.
 *
 * @package    local_mailtest
 * @copyright  2015-2025 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include config.php.
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Include our function library.
$pluginname = 'mailtest';
require_once($CFG->dirroot . '/local/' . $pluginname . '/locallib.php');

// Globals.
global $CFG, $OUTPUT, $USER, $SITE, $PAGE;

// Ensure only administrators have access.
$homeurl = new moodle_url('/');
require_login();
if (!is_siteadmin()) {
    redirect($homeurl, "This feature is only available for site administrators.", 5);
}

// URL Parameters.
// There are none.

// Include form.
require_once(dirname(__FILE__) . '/classes/' . $pluginname . '_form.php');

// Heading ==========================================================.

$title = get_string('pluginname', 'local_' . $pluginname);
$heading = get_string('heading', 'local_' . $pluginname);
$url = new moodle_url('/local/' . $pluginname . '/');
if ($CFG->branch >= 25) { // Moodle 2.5+.
    $context = context_system::instance();
} else {
    $context = get_system_context();
}

$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
admin_externalpage_setup('local_' . $pluginname); // Sets the navbar & expands navmenu.

// Setup the form.

$CFG->noreplyaddress = empty($CFG->noreplyaddress) ? 'noreply@' . get_host_from_url($CFG->wwwroot) : $CFG->noreplyaddress;

if (!empty($CFG->emailonlyfromnoreplyaddress) || $CFG->branch >= 32) { // Always send from no reply address.
    // Use primary administrator's name if support name has not been configured.
    $primaryadmin = get_admin();
    $CFG->supportname = empty($CFG->supportname) ? fullname($primaryadmin, true) : $CFG->supportname;
    // Use noreply address.
    $fromemail = local_mailtest_generate_email_user($CFG->noreplyaddress, format_string($CFG->supportname));
    $fromdefault = $CFG->noreplyaddress;
} else { // Otherwise defaults to send from primary admin user.
    $fromemail = get_admin();
    $fromdefault = $fromemail->email;
}

$form = new mailtest_form(null, ['fromdefault' => $fromdefault]);
if ($form->is_cancelled()) {
    redirect($homeurl);
}

echo $OUTPUT->header();

// Display or process the form. =====================================.

$data = $form->get_data();
if (!$data) { // Display the form.
    echo $OUTPUT->heading($heading);

    // Display a warning if Cron hasn't run in a while. =============.

    $cronwarning = '';
    if ($CFG->branch >= 37) {
        defined('MINSECS') || define('MINSECS', 200); // For pre-Moodle 3.9 compatibility.
        $lastcron = get_config('tool_task', 'lastcronstart');
        $cronoverdue = ($lastcron < time() - 3600 * 24);
        $check = $PAGE->get_renderer('core', 'admin');
        if ($cronoverdue) {
            $cronwarning .= $check->cron_overdue_warning($cronoverdue);
        }

        $lastcroninterval = get_config('tool_task', 'lastcroninterval');
        $expectedfrequency = isset($CFG->expectedcronfrequency) ? $CFG->expectedcronfrequency : MINSECS;
        $croninfrequent = !$cronoverdue && ($lastcroninterval > ($expectedfrequency + MINSECS)
                || $lastcron < time() - $expectedfrequency);
        if ($croninfrequent) {
            $cronwarning .= $check->cron_infrequent_warning($croninfrequent);
        }
    } else { // Up to and including Moodle 3.6.
        if ($CFG->branch >= 27) { // Moodle 2.7+.
            $sql = 'SELECT MAX(lastruntime) FROM {task_scheduled}';
        } else {
            $sql = 'SELECT MAX(lastcron) FROM {modules}';
        }
        $lastcron = $DB->get_field_sql($sql);
        $cronoverdue = ($lastcron < time() - 3600 * 24);
        if ($cronoverdue) { // Cron is overdue.
            if (empty($CFG->cronclionly)) {
                // Determine build link to run cron.
                $cronurl = new moodle_url('/admin/cron.php');
                if (!empty($CFG->cronremotepassword)) {
                    $cronurl = new moodle_url('/admin/cron.php', ['password' => $CFG->cronremotepassword]);
                }
                $cronwarning .= get_string('cronwarning', 'admin', $cronurl->out());
            } else {
                $cronwarning .= get_string('cronwarningcli', 'admin');
            }
        }
    }

    if ($cronoverdue) { // Cron is overdue.
        $msg = '';
        $msg .= '<h3 class="alert-heading">' . get_string('warning') . '</h3>';
        $msg .= $cronwarning;
        $msg .= '<p>' . get_string('cron_help', 'admin');
        if (!empty($CFG->branch)) {
            $icon = $OUTPUT->pix_icon('help', get_string('moreinfo'));
            $link = $CFG->docroot . '/' . $CFG->branch . '/' . substr(current_language(), 0, 2) . '/Cron';
            $msg .= html_writer::link($link, $icon, ['class' => 'helplink', 'target' => '_blank', 'rel' => 'external']);
        }
        $msg .= '</p>';
        $msg .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . get_string('closebuttontitle') . '">'
                . '<span aria-hidden="true">&times;</span></button>';
        local_mailtest_msgbox($msg, null, 3, 'alert alert-danger alert-block alert-dismissible fade show');
    }

    // Display the form. ============================================.

    $form->display();
} else {
    // Send test email.
    if (!isset($data->sender)) {
        $data->sender = $CFG->noreplyaddress;
    }
    $fromemail = local_mailtest_generate_email_user($data->sender);

    if ($CFG->branch >= 26) {
        $toemail = core_text::strtolower($data->recipient);
    } else {
        $toemail = textlib::strtolower($data->recipient);
    }
    if ($toemail !== clean_param($toemail, PARAM_EMAIL)) {
        local_mailtest_msgbox(get_string('invalidemail'), get_string('error'), 2, 'errorbox', $url->out());
    }
    $toemail = local_mailtest_generate_email_user($toemail, '');
    if (email_should_be_diverted($toemail->email)) {
        $toemail->email = $toemail->email . ' <strong>(' .
                get_string('divertedto', 'local_' . $pluginname, $CFG->divertallemailsto) . ')</strong>';
    }

    $subject = format_string($SITE->fullname, true, ['escape' => false]);

    // Add some system information.
    $a = new stdClass();
    if (isloggedin()) {
        $a->regstatus = get_string('registered', 'local_' . $pluginname, $USER->username);
    } else {
        $a->regstatus = get_string('notregistered', 'local_' . $pluginname);
    }
    $a->lang = current_language();
    $a->browser = $_SERVER['HTTP_USER_AGENT'];
    $a->referer = $_SERVER['HTTP_REFERER'];
    $a->release = $CFG->release;
    $a->ip = local_mailtest_getuserip();
    $messagehtml = get_string('message', 'local_' . $pluginname, $a);
    $messagetext = html_to_text($messagehtml);

    if ($CFG->branch >= 404) {
        $fromsender = get_string('fromsender');
        $torecipient = get_string('torecipient');
    } else {
        $fromsender = get_string('from');
        $torecipient = get_string('to');
    }

    ob_end_flush();
    ob_implicit_flush(true);
    echo '<h2 class="alert-heading">' . get_string('testing', 'local_' . $pluginname) . '</h2>';
    echo '<p>' . $fromsender . ' : ' . $fromemail->email . '<br>
        &#129095; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &#129095; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &#129095;<br>
        ' . get_string('server', 'local_' . $pluginname, (empty($CFG->smtphosts) ? 'PHPMailer' : $CFG->smtphosts)) . '<br>
        &#129095; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &#129095; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &#129095;<br>
        ' . $torecipient . ' : ' . $toemail->email . '</p>';
    ob_implicit_flush(false);

    // Manage Moodle SMTP debugging display.
    $debuglevel = $CFG->debug;
    $debugdisplay = $CFG->debugdisplay;
    $debugsmtp = isset($CFG->debugsmtp) && $CFG->debugsmtp;
    $showlog = !empty($data->alwaysshowlog) || ($debugdisplay && $debugsmtp);
    // Set debug level to a minimum of NORMAL: Show errors, warnings and notices.
    if ($CFG->debug < 15) {
        $CFG->debug = 15;
    }
    $CFG->debugdisplay = true;
    $CFG->debugsmtp = true;
    if (empty($CFG->smtphosts)) {
        $success = email_to_user($toemail, $fromemail, $subject, $messagetext, $messagehtml, '', '', true, $fromemail->email);
        $smtplog = get_string('nologavailable', 'local_' . $pluginname);
        if (!empty($phplog = ini_get('mail.log'))) {
            if ($phplog == 'syslog' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $smtplog .= '<pre>mail.log : ' . get_string('winsyslog', 'local_' . $pluginname) . '</pre>';
            } else {
                $smtplog .= '<pre>mail.log : ' . $phplog . '</pre>';
            }
        }
    } else {
        ob_start();
        $success = email_to_user($toemail, $fromemail, $subject, $messagetext, $messagehtml, '', '', true, $fromemail->email);
        $smtplog = ob_get_contents();
        ob_end_clean();
        $smtplog = '<figure class="border border-dark p-2">' . $smtplog . '</figure>';
    }

    $CFG->debug = $debuglevel;
    $CFG->debugdisplay = $debugdisplay;
    $CFG->debugsmtp = $debugsmtp;

    if ($success) { // Success.
        $title = get_string('success');
        $msg = '<p>';
        if (empty($CFG->smtphosts)) {
            $msg .= get_string('sentmailphp', 'local_' . $pluginname);
        } else {
            $msg .= get_string('sentmail', 'local_' . $pluginname);
        }

        // Display a list of common reasons the email may not have been delivered.
        if (empty($CFG->smtphosts)) {
            $extrainfo = '<li>' . get_string('failphpmailerconfig', 'local_' . $pluginname) . '</li>';
        } else {
            $extrainfo = '';
        }
        $msg .= ' ' . get_string('commondeliveryissues', 'local_' . $pluginname, $extrainfo);

        local_mailtest_msgbox($msg, $title, 2, 'infobox', $url->out());
        if ($showlog) {
            // Display debugging info if settings were already on before the test or user wants to force display.
            echo $smtplog;
        }
    } else if (!empty($CFG->smtphosts)) {
        // Failed to deliver message using SMTP.
        $errtype = 'errorunknown';

        // Diagnose failed SMTP connection issues.

        $issues = '';
        if (strpos($smtplog, '220') === false) { // Missing 220 code, connection failed.
            $errtype = 'errorcommunications';

            // Check for domain, security protocol and port issues for each specified mail server.
            $hosts = explode(';', $CFG->smtphosts);
            foreach ($hosts as $host) {
                if (empty($host)) {
                    continue; // Skip if blank.
                }

                // Split the host and the port.
                $host = explode(':', $host . ':25'); // Set default port to 25 in case none was specified.
                $host = $host[0];
                $port = $host[1];
                $port = (int)$port;

                // Check for DNS record lookup failure. Skip if host is an IP address.
                if (
                    filter_var($host, FILTER_VALIDATE_IP) === false // Not an IP address.
                    && empty(@dns_get_record($host)) // The address does not have a DNS record.
                    && gethostbyname($host) == $host
                ) { // Address does not resolve to an IP address (e.g. localhost).
                        $issues .= '<li>' . get_string('faildnslookup', 'local_' . $pluginname, $host) . '</li>';
                        break;
                }

                // If using SSL or TLS, port is not usually 25.
                if ($port == 25 && !empty($CFG->smtpsecure)) {
                    // No port or port 25 was specified for a SSL/TLS connection.
                    $issues .= '<li>' . get_string('failmissingport', 'local_' . $pluginname, $CFG->smtpsecure) . '</li>';
                }

                // Port is not 25 but a secure protocol was not specified.
                if ($port != 25 && empty($CFG->smtpsecure)) {
                    // Non default port specified for a non SSL/TLS connection.
                    $issues .= '<li>';
                    $issues .= get_string('failmissingprotocol', 'local_' . $pluginname, $port);
                    $issues .= '</li>';
                }

                // The port and protocol don't match. Although it can, the SSL port is rarely 587, and TLS is rarely 465.
                if ($port == 587 && $CFG->smtpsecure == 'ssl' || $port == 465 && $CFG->smtpsecure == 'tls') {
                    $issues .= '<li>' . get_string(
                        'failprotocolmismatch',
                        'local_' . $pluginname,
                        ['port' => $port, 'protocol' => $CFG->smtpsecure]
                    ) . '</li>';
                }

                // Connection timeout issues.
                $fp = @fsockopen($host, $port, $errno, $errstr, 10);
                if (!$fp) {
                    if (stripos($errstr, 'Connection timed out') !== false) {
                        // Connection timed out due to possible issues such as ISP blocking outbound SMTP connections.
                        $issues .= '<li>' . get_string('failoutboundsmtpblocked', 'local_' . $pluginname) . '</li>';
                    } else {
                        // Server's port was closed.
                        $issues .= '<li>' . get_string('failclosedport', 'local_' . $pluginname, $port) . '</li>';
                    }
                    // Add a list common issues.
                    $issues .= get_string('commoncommissues', 'local_' . $pluginname);
                    break;
                } else {
                    fclose($fp);
                }
            }
        } else if (strpos($smtplog, '250') === false) { // No 250 code.
            // No or very limited communication between Moodle and the SMTP server.
            $errtype = 'errorcommunications';
            $issues .= get_string('failaccessdenied', 'local_' . $pluginname);
        } else {
            // SMTP mail server refused the email.
            $errtype = 'errorsend';

            // Diagnose possible authentication issues.

            // Invalid credentials - username and/or password are incorrect.
            if (strpos($smtplog, '530') !== false || strpos($smtplog, '535') !== false || strpos($smtplog, '235') === false) {
                $issues .= get_string('failcredentials', 'local_' . $pluginname);
            }

            // No-reply address is probably fake or contains a typo.
            // Your mail server requires a real email address with a real mailbox.
            if (strpos($smtplog, '550') !== false) {
                $issues .= get_string('failunknownmailbox', 'local_' . $pluginname);
            }
        }
        $smtplog = '<h4>' . get_string('connectionlog', 'local_' . $pluginname) . '</h4>' . $smtplog;

        $continuelink = ($CFG->branch >= 32) ? 'outgoingmailconfig' : 'messagesettingemail';
        $msg = get_string($errtype, 'local_' . $pluginname, '../../admin/settings.php?section=' . $continuelink);

        // Display diagnostic information, if available.
        if (!empty($issues)) {
            $title = get_string('additionalinfo', 'local_' . $pluginname);
            $msg .= '<p>' . $title . '</p><ul>' . $issues . '</ul>' . $smtplog;
        }

        // Also display the results of the dialogue between Moodle and the SMTP server.
        local_mailtest_msgbox($msg, get_string('emailfail', 'local_' . $pluginname), 3, 'errorbox', $url->out());
    } else {
        // Failed to send message using PHPMailer.
        $title = get_string('emailfail', 'local_' . $pluginname);
        $msg = get_string('failphpmailer', 'local_' . $pluginname);
        local_mailtest_msgbox($msg, $title, 3, 'errorbox', $url->out());
    }
}

// Footing  =========================================================.

echo $OUTPUT->footer();
