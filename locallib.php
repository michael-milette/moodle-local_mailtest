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
 * Library of functions for MailTest.
 *
 * @package    local_mailtest
 * @copyright  2015-2022 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generate a user info object based on provided parameters.
 *
 * @param      string  $email  plain text email address.
 * @param      string  $name   (optional) plain text real name.
 * @param      int     $id     (optional) user ID
 *
 * @return     object  user info.
 */
function local_mailtest_generate_email_user($email, $name = '', $id = -99) {
    $emailuser = new stdClass();
    $emailuser->email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailuser->email = '';
    }
    $name = format_text($name, FORMAT_HTML, array('trusted' => false, 'noclean' => false));
    $emailuser->firstname = trim(filter_var($name, FILTER_SANITIZE_STRING));
    $emailuser->lastname = '';
    $emailuser->maildisplay = true;
    $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML emails.
    $emailuser->id = $id;
    $emailuser->firstnamephonetic = '';
    $emailuser->lastnamephonetic = '';
    $emailuser->middlename = '';
    $emailuser->alternatename = '';
    return $emailuser;
}

/**
 * Outputs a message box.
 *
 * @param      string  $text     The text of the message.
 * @param      string  $heading  (optional) The text of the heading.
 * @param      int     $level    (optional) The level of importance of the
 *                               heading. Default: 2.
 * @param      string  $classes  (optional) A space-separated list of CSS
 *                               classes.
 * @param      string  $link     (optional) The link where you want the Continue
 *                               button to take the user. Only displays the
 *                               continue button if the link URL was specified.
 * @param      string  $id       (optional) An optional ID. Is applied to body
 *                               instead of heading if no heading.
 * @return     string  the HTML to output.
 */
function local_mailtest_msgbox($text, $heading = null, $level = 2, $classes = null, $link = null, $id = null) {
    global $OUTPUT;
    echo $OUTPUT->box_start(trim('box ' . $classes));
    if (!is_null($heading)) {
        echo $OUTPUT->heading($heading, $level, $id);
        echo "<p>$text</p>" . PHP_EOL;
    } else {
        echo "<p id=\"$id\">$text</p>" . PHP_EOL;
    }
    if (!is_null($link)) {
        echo $OUTPUT->continue_button($link);
    }
    echo $OUTPUT->box_end();
}

/**
 * Get the user's public or private IP address.
 *
 * @return     string  Public IP address or the private IP address if the public address cannot be identified.
 */
function local_mailtest_getuserip() {
    $fieldlist = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
            'REMOTE_ADDR', 'HTTP_CF_CONNECTING_IP', 'HTTP_X_CLUSTER_CLIENT_IP');

    // Public range first.
    $filterlist = array(
        FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );

    foreach ($filterlist as $filter) {
        foreach ($fieldlist as $field) {

            if (!array_key_exists($field, $_SERVER) || empty($_SERVER[$field])) {
                continue;
            }

            $iplist = explode(',', $_SERVER[$field]);
            foreach ($iplist as $ip) {

                // Strips off port number if it exists.
                if (substr_count($ip, ':') == 1) {
                    // IPv4 with a port.
                    list($ip) = explode(':', $ip);
                } else if ($start = (substr($ip, 0, 1) == '[') && $end = strpos($ip, ']:') !== false) {
                    // IPv6 with a port.
                    $ip = substr($ip, $start + 1, $end - 2);
                }
                // Sanitize so that we only get public addresses.
                $lastip = $ip; // But save other address just in case.
                $ip = filter_var(trim($ip), FILTER_VALIDATE_IP, $filter);
                if ($ip !== false) {
                    return($ip);
                }
            }
        }
    }
    // Private or restricted range.
    return $lastip;
}
