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
 * Main form for MailTest.
 *
 * @package    local_mailtest
 * @copyright  2016 TNG Consulting Inc. - www.tngcosulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

/**
 * Form to prompt administrator for the recipient's email address.
 *
 */
class mailtest_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER, $CFG;
        $mform = $this->_form;

        // Header.

        $mform->addElement('html', '<p>'.get_string('pluginname_help', 'local_mailtest').'</p>');

        // Send method.

        if (empty($CFG->smtphosts)) {
            $sendmethod = get_string('phpmethod', 'local_mailtest');
        } else {
            $sendmethod = get_string('smtpmethod', 'local_mailtest', $CFG->smtphosts);
        }
        $sendmethod .= ' (<a href="../../admin/settings.php?section=messagesettingemail">'.get_string('change', 'admin').'</a>)';
        $mform->addElement('static', 'sendmethod',  get_string('sendmethod', 'local_mailtest'), $sendmethod);

        // Sender.
        $senderarray = array();
        $a = new stdClass();
        $a->label = get_string('change', 'admin');
        $a->email = $CFG->noreplyaddress;
        $a->url = "../../admin/settings.php?section=messagesettingemail#noreplyaddress";
        $a->type = get_string('noreplyaddress', 'message_email');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        $a->email = $USER->email;
        $a->url = "../../user/editadvanced.php?course=1#fitem_id_email";
        $a->type = get_string('youremail', 'local_mailtest');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        $a->email = $CFG->supportemail;
        $a->url = "../../admin/settings.php?section=supportcontact";
        $a->type = get_string('supportemail', 'admin');
        $senderarray[] = $mform->createElement('radio', 'sender', '', get_string('from', 'local_mailtest', $a), $a->email);
        $mform->addGroup($senderarray, 'senderar', get_string('fromemail', 'local_mailtest'), array('<br />'), false);
        $mform->setDefault('sender', $this->_customdata['fromdefault']);

        // Recipient.

        $mform->addElement('text', 'recipient', get_string('toemail', 'local_mailtest'), 'maxlength="100" size="25" ');
        $mform->setType('recipient', PARAM_EMAIL);
        $mform->addRule('recipient', get_string('required'), 'required');

        // Buttons.

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'send', get_string('sendtest', 'local_mailtest'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['recipient'])) {
            $errors['recipient'] = get_string('err_email', 'form');
        }

        return $errors;
    }
}
