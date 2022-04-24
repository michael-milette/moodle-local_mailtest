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
 * Main form for eMailTest.
 *
 * @package    local_mailtest
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngcosulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form to prompt administrator for the recipient's email address.
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngcosulting.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mailtest_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $USER, $CFG;
        $mform = $this->_form;

        // Header.

        $mform->addElement('html', '<p>' . get_string('pluginname_help', 'local_mailtest') . '</p>');

        // Send method.

        if (empty($CFG->smtphosts)) {
            $sendmethod = get_string('phpmethod', 'local_mailtest');
        } else {
            $sendmethod = get_string('smtpmethod', 'local_mailtest', $CFG->smtphosts);
        }
        if ($CFG->branch >= 32) {
            $sendmethod .= ' (<a href="../../admin/settings.php?section=outgoingmailconfig#admin-smtphosts">' .
                    get_string('change', 'admin') . '</a>)';
        } else {
            $sendmethod .= ' (<a href="../../admin/settings.php?section=messagesettingemail">' .
                    get_string('change', 'admin') . '</a>)';
        }
        $mform->addElement('static', 'sendmethod',  get_string('sendmethod', 'local_mailtest'), $sendmethod);

        // Sender.

        $senderarray = array();
        $a = new stdClass();
        $a->label = get_string('change', 'admin');

        // Current user's email address.
        $a->email = $USER->email;
        if ($CFG->branch >= 32) {
            $a->url = '../../user/editadvanced.php?course=1#id_email';
        } else {
            $a->url = '../../user/editadvanced.php?course=1#fitem_id_email';
        }
        $a->type = get_string('youremail', 'local_mailtest');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        if (!validate_email($a->email)) {
            $senderarray[] = $mform->CreateElement('static', 'error', '',
                    html_writer::span(get_string('invalidemail'), 'statuswarning'));
        }

        // Support email address.
        $primaryadmin = get_admin();
        $a->email = empty($CFG->supportemail) ? $primaryadmin->email : $CFG->supportemail;
        $a->url = '../../admin/settings.php?section=supportcontact';
        $a->type = get_string('supportemail', 'admin');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        if (!validate_email($a->email)) {
            $senderarray[] = $mform->CreateElement('static', 'error', '',
                    html_writer::span(get_string('invalidemail'), 'statuswarning'));
        }

        // No Reply address.
        $a->email = empty($CFG->noreplyaddress) ? 'noreply@' . get_host_from_url($CFG->wwwroot) : $CFG->noreplyaddress;
        if ($CFG->branch >= 32) {
            $a->url = '../../admin/settings.php?section=outgoingmailconfig#admin-noreplyaddress';
            $a->type = get_string('noreplyaddress', 'admin');
        } else {
            $a->url = '../../admin/settings.php?section=messagesettingemail#noreplyaddress';
            $a->type = get_string('noreplyaddress', 'message_email');
        }
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        if (!validate_email($a->email)) {
            $senderarray[] = $mform->CreateElement('static', 'error', '',
                    html_writer::span(get_string('invalidemail'), 'statuswarning'));
        }

        // Primary admin email address.
        $primaryadmin = get_admin();
        $a->email = $primaryadmin->email;
        $a->url = '../../user/editadvanced.php?id=' . $primaryadmin->id;
        $a->type = get_string('primaryadminemail', 'local_mailtest');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        if (!validate_email($a->email)) {
            $senderarray[] = $mform->CreateElement('static', 'error', '',
                    html_writer::span(get_string('invalidemail'), 'statuswarning'));
        }

        // Add group of sender radio buttons to form.
        $mform->setDefault('sender', $this->_customdata['fromdefault']);
        $mform->addGroup($senderarray, 'senderar', get_string('fromemail', 'local_mailtest'), array('<br />'), false);
        $mform->setType('sender', PARAM_EMAIL);
        $mform->addRule('senderar', get_string('required'), 'required');

        // Recipient.

        $mform->addElement('text', 'recipient', get_string('toemail', 'local_mailtest'), 'maxlength="100" size="25" ');
        $mform->setType('recipient', PARAM_EMAIL);
        $mform->addRule('recipient', get_string('required'), 'required');

        // Divert all emails.

        if ($CFG->branch >= 32) {
            if (empty($CFG->divertallemailsto)) {
                $divertstatus = get_string('functiondisabled');
            } else {
                $divertstatus = get_string('divertedto', 'local_mailtest', $CFG->divertallemailsto);
            }
            $divertstatus .= ' (<a href="../../admin/settings.php?section=outgoingmailconfig#admin-divertallemailsto">' .
                    get_string('change', 'admin') . '</a>)';
            $mform->addElement('static', 'divertemails',  get_string('divertallemails', 'admin'), $divertstatus);
        }

        // Always show communications log - even on success.
        $mform->addElement('checkbox', 'alwaysshowlog', '', get_string('alwaysshowlog', 'local_mailtest'));
        $mform->setDefault('alwaysshowlog', ($CFG->debugdisplay && isset($CFG->debugsmtp) && $CFG->debugsmtp));

        // Buttons.

        if ($disabled = !empty($CFG->noemailever)) {
            $mform->addElement('static', 'noemailever',
                    html_writer::div(get_string('messagingdisable', 'error'), 'alert alert-danger'),
                    html_writer::div(get_string('noemaileverset', 'message_airnotifier'), 'alert alert-danger'));
        }
        $buttonarray = array();
        if (!$disabled) {
            $buttonarray[] = $mform->createElement('submit', 'send', get_string('sendtest', 'local_mailtest'));
        }
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Validate submitted form data, recipient in this case, and returns list of errors if it fails.
     *
     * @param      array  $data   The data fields submitted from the form.
     * @param      array  $files  Files submitted from the form (not used)
     *
     * @return     array  List of errors to be displayed on the form if validation fails.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['recipient'])) {
            $errors['recipient'] = get_string('err_email', 'form');
        }

        return $errors;
    }
}
