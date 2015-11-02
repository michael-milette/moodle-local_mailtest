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
 * @copyright  TNG Consulting Inc. - www.tngcosulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

/*
 * Form which prompts administrator for the recipient's email address.
 */
class mailtest_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        // Header.

        $mform->addElement('html', '<p>'.get_string('pluginname_help', 'local_mailtest').'</p>');

        // Recipient.

        $mform->addElement('text', 'recipient', get_string('email'));
        $mform->setType('recipient', PARAM_EMAIL);
        $mform->addRule('recipient', get_string('required'), 'required');

        // Buttons.

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'send', get_string('sendtest', 'local_mailtest'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['recipient'])) {
            $errors['recipient'] = get_string('recipientisrequired', 'local_mailtest');
        }

        return $errors;
    }
}