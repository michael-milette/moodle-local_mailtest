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
 * Displays the form and processes the form submission.
 *
 * @package    local_mailtest
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include config.php.
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Include our function library.
$pluginname = 'mailtest';
require_once($CFG->dirroot.'/local/' . $pluginname . '/locallib.php');

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

$form = new mailtest_form(null, array('fromdefault' => $fromdefault));
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
        $expectedfrequency = $CFG->expectedcronfrequency ?? MINSECS;
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
                    $cronurl = new moodle_url('/admin/cron.php', array('password' => $CFG->cronremotepassword));
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
            $msg .= html_writer::link($link, $icon, array('class' => 'helplink', 'target' => '_blank', 'rel' => 'external'));
        }
        $msg .= '</p>';
        $msg .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . get_string('closebuttontitle') .
                '"><span aria-hidden="true">&times;</span></button>';
        local_mailtest_msgbox($msg, null, 3, 'alert alert-danger alert-block alert-dismissible fade show');
    }

    // Display the form. ============================================.
    $form->display();

} else {      // Send test email.
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
        local_mailtest_msgbox(get_string('invalidemail'), get_string('error'), 2, 'errorbox', $url);
    }
    $toemail = local_mailtest_generate_email_user($toemail, '');

    $subject = format_string($SITE->fullname);

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
    }
    $CFG->debug = $debuglevel;
    $CFG->debugdisplay = $debugdisplay;
    $CFG->debugsmtp = $debugsmtp;

    if ($success) { // Success.
        if ($showlog) {
            // Display debugging info if settings were already on before the test or user wants to force display.
            echo $smtplog;
        }
        if (empty($CFG->smtphosts)) {
            $msg = get_string('sentmailphp', 'local_' . $pluginname);
        } else {
            $msg = get_string('sentmail', 'local_' . $pluginname);
        }
        if (email_should_be_diverted($toemail->email)) {
            $toemail->email = $toemail->email . ' <strong>(' .
                    get_string('divertedto', 'local_' . $pluginname, $CFG->divertallemailsto) . ')</strong>';
        }
        $msg .= '<br><br>' . get_string('from') . ' : ' . $fromemail->email . '<br>' . get_string('to') . ' : ' . $toemail->email;

        local_mailtest_msgbox($msg, get_string('success'), 2, 'infobox', $url);

    } else { // Failed to deliver message to the SMTP mail server.

        if (trim($smtplog) == false) { // No communication between Moodle and the SMTP server.
            $errstring = 'errorcommunications';
        } else { // SMTP mail server refused the email.
            $errstring = 'errorsend';
            // Display the results of the dialogue between Moodle and the SMTP server.
            echo $smtplog;
        }

        if ($CFG->branch >= 32) {
            $msg = get_string($errstring, 'local_' . $pluginname, '../../admin/settings.php?section=outgoingmailconfig');
        } else {
            $msg = get_string($errstring, 'local_' . $pluginname, '../../admin/settings.php?section=messagesettingemail');
        }
        $msg .= '<br><br>' . get_string('from') . ' : ' . $fromemail->email . '<br>' . get_string('to') . ' : ' . $toemail->email;

        local_mailtest_msgbox($msg, get_string('emailfail', 'error'), 2, 'errorbox', $url);

    }
}

// Footing  =========================================================.

echo $OUTPUT->footer();
