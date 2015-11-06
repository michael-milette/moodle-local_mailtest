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
 * @copyright  2015 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @dependancies None.
 *
 */

$pluginname = 'mailtest';

// Include config.php.
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/local/'.$pluginname.'/locallib.php');

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
require_once(dirname(__FILE__).'/class/'.$pluginname.'_form.php');

// Heading ==========================================================.

$title = get_string('pluginname', 'local_'.$pluginname);
$heading = get_string('heading', 'local_'.$pluginname);
$url = new moodle_url('/local/'.$pluginname.'/');
if ($CFG->version >= 2013051400) { // Moodle 2.5+.
    $context = context_system::instance();
} else {
    $context = get_system_context();
}

$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
admin_externalpage_setup('local_'.$pluginname); // Sets the navbar & expands navmenu.

// Setup the form.

$form = new mailtest_form();
if ($form->is_cancelled()) {
    redirect($homeurl);
}

echo $OUTPUT->header();

// Display or process the form. =====================================.

$data = $form->get_data();
if (!$data) { // Display the form.

    echo $OUTPUT->heading($heading);
    $form->display();

} else {      // Send test email.

    if (!empty($CFG->emailonlyfromnoreplyaddress) && !empty($CFG->noreplyaddress)) {
        // Use site name if Moodle Support Name is not available.
        $supportname = (trim($CFG->supportname) == '' ? $SITE->fullname : $CFG->supportname);
        $fromemail = local_mailtest_generate_email_user($CFG->noreplyaddress, format_string($supportname));
    } else {
        $fromemail = $USER;
    }

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
        $a->regstatus = get_string('registered', 'local_'.$pluginname, $USER->username);
    } else {
        $a->regstatus = get_string('notregistered', 'local_'.$pluginname);
    }
    $a->lang = current_language();
    $a->browser = $_SERVER['HTTP_USER_AGENT'];
    $a->referer = $_SERVER['HTTP_REFERER'];
    $a->ip = local_mailtest_getuserip();
    $messagehtml = get_string('message', 'local_'.$pluginname, $a);
    $messagetext = html_to_text($messagehtml);

    // Manage Moodle SMTP debugging display.
    $debugdisplay = $CFG->debugdisplay;
    $debugsmtp = $CFG->debugsmtp;
    $CFG->debugdisplay = true;
    $CFG->debugsmtp = true;
    ob_start();
    $success = email_to_user($toemail, $fromemail, $subject, $messagetext, $messagehtml, '', '', true);
    $smtplog = ob_get_contents();
    ob_end_clean();
    $CFG->debugdisplay = $debugdisplay;
    $CFG->debugsmtp = $debugsmtp;

    if ($success) { // Success.
        if ($debugdisplay && $debugsmtp) {
            // Display debugging info if settings were already on before the test.
            echo $smtplog;
        }
        $msg = get_string('sentmail', 'local_'.$pluginname);
        local_mailtest_msgbox($msg, get_string('success'), 2, 'infobox', $url);
    } else { // Email could not be delivered to the SMTP mail server.
        echo $smtplog; // Display SMTP dialogue.
        $msg = get_string('errorsend', 'local_'.$pluginname);
        local_mailtest_msgbox($msg, get_string('emailfail', 'error'), 2, 'errorbox', $url);
    }

}

// Footing  =========================================================.

echo $OUTPUT->footer();
