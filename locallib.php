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
 * Library of functions for MailTest.
 *
 * @package    local_mailtest
 * @copyright  2015-2025 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    $name = format_text($name, FORMAT_HTML, ['trusted' => false, 'noclean' => false]);
    $emailuser->firstname = trim(htmlspecialchars($name, ENT_COMPAT));
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
 */
function local_mailtest_msgbox($text, $heading = null, $level = 2, $classes = null, $link = null, $id = null) {
    global $OUTPUT;
    echo $OUTPUT->box_start(trim('box ' . $classes));
    if (!is_null($heading)) {
        echo $OUTPUT->heading($heading, $level, $id);
        echo "<div>$text</div>" . PHP_EOL;
    } else {
        echo "<div id=\"$id\">$text</div>" . PHP_EOL;
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
    $fieldlist = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_CLUSTER_CLIENT_IP',
    ];

    // Public range first.
    $filterlist = [
        FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
    ];

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
                    $ip = explode(':', $ip)[0];
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

/**
 * Check DNS records for a given domain.
 *
 * This function checks the DKIM, SPF, DMARC, and BIMI records for a given domain.
 * It returns an array with a success flag and a message string.
 *
 * @param string $domain The domain to check DNS records for.
 * @return string Message string.
 */
function local_mailtest_checkdns($domain) {
    global $CFG;

    $message = '';
    $success = true;

    $xmark = '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i> ';
    $checkmark = '<i class="fa fa-check-circle text-success" aria-hidden="true"></i> ';
    $exclamation = '<i class="fa fa-exclamation-triangle text-warning" aria-hidden="true"></i> ';

    // Check SPF records.

    $regex = '/^v=spf1( +([-+?~]?(all|include:(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\})|a(:(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}))?((\/(\d|1\d|2\d|3[0-2]))?(\/\/'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8]))?)?|mx(:(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}))?((\/(\d|1\d|2\d|3[0-2]))?'
        . '(\/\/([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8]))?)?|ptr(:(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}))?|ip4:([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]'
        . '|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]'
        . '|25[0-5])(\/([0-9]|1[0-9]|2[0-9]|3[0-2]))?|ip6:(::|([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4}|'
        . '([0-9A-Fa-f]{1,4}:){1,8}:|([0-9A-Fa-f]{1,4}:){7}:[0-9A-Fa-f]{1,4}|([0-9A-Fa-f]{1,4}:){6}(:'
        . '[0-9A-Fa-f]{1,4}){1,2}|([0-9A-Fa-f]{1,4}:){5}(:[0-9A-Fa-f]{1,4}){1,3}|([0-9A-Fa-f]{1,4}:){4}'
        . '(:[0-9A-Fa-f]{1,4}){1,4}|([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){1,5}|([0-9A-Fa-f]{1,4}:){2}'
        . '(:[0-9A-Fa-f]{1,4}){1,6}|[0-9A-Fa-f]{1,4}:(:[0-9A-Fa-f]{1,4}){1,7}|:(:[0-9A-Fa-f]{1,4}){1,8}|'
        . '([0-9A-Fa-f]{1,4}:){6}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|'
        . '[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]'
        . '|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){6}:'
        . '([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]'
        . '|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?'
        . '([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]'
        . '|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}([0-9]|[1-9][0-9]'
        . '|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])'
        . '\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}([0-9]|[1-9][0-9]'
        . '|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])'
        . '\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])|([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}([0-9]|[1-9][0-9]'
        . '|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])'
        . '\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])|[0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}([0-9]|[1-9][0-9]'
        . '|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])'
        . '\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}'
        . '|2[0-4][0-9]|25[0-5])|::([0-9A-Fa-f]{1,4}:){0,6}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]'
        . '|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]'
        . '|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))'
        . '(\/(\d{1,2}|10[0-9]|11[0-9]|12[0-8]))?|exists:(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}))|redirect=(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\})|exp=(%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|[!-$&-~])*(\.([A-Za-z]'
        . '|[A-Za-z]([-0-9A-Za-z]?)*[0-9A-Za-z])|%\{[CDHILOPR-Tcdhilopr-t]'
        . '([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\})|[A-Za-z][-.0-9A-Z_a-z]*='
        . '(%\{[CDHILOPR-Tcdhilopr-t]([1-9][0-9]?|10[0-9]|11[0-9]|12[0-8])?r?[+-\/=_]*\}|%%|%_|%-|'
        . '[!-$&-~])*))* *$/';

    // Perform DNS query for SPF TXT records.
    $spf = false;
    $txtrecords = @dns_get_record($domain, DNS_TXT);
    // If a TXT records is found, check if it contains SPF record.
    if (!empty($txtrecords)) {
        // Check if any have the required tags.
        foreach ($txtrecords as $record) {
            if (strpos($record['txt'], 'v=spf1') !== false) {
                // SPF record found.
                $message .= $checkmark . get_string('spfrecordfound', 'local_mailtest')  . '<br>';

                // Extract found SPF record data.
                $spfdata = $record['txt'];

                // Check if the SPF record contains at least one mechanism (mandatory).
                if (preg_match($regex, $spfdata)) {
                    // SPF record contains at least one mechanism, it's valid.
                    $message .= $checkmark . get_string('spfvalidrecord', 'local_mailtest')  . '<br>';
                    $spf = true;
                    break;
                }
                if (!$spf) {
                    $message .= $xmark . get_string('spfinvalidrecord', 'local_mailtest')  . '<br>';
                }
            }
        }
    }
    // No SPF record was found.
    if (!$spf && empty($message)) {
        $message .= $exclamation . get_string('spfnorecordfound', 'local_mailtest')  . '<br>';
    }

    // Check DKIM record.

    $dkim = false;
    if (empty($emaildkimselector = $CFG->emaildkimselector)) {
        $message .= $exclamation . get_string('dkimmissingselector', 'local_mailtest')  . '<br>';
    } else {
        $txtrecords = @dns_get_record($emaildkimselector . '._domainkey.' . $domain, DNS_TXT);

        // If TXT records are found named *_domainkey, check if it contains DKIM record.
        if (!empty($txtrecords)) {
            // DKIM records found.
            $message .= $checkmark . get_string('dkimrecordfound', 'local_mailtest')  . '<br>';

            // Check if it has the required tags.
            foreach ($txtrecords as $record) {
                // Extract DKIM record data.
                $dkimdata = $record['txt'];

                // Check if the DKIM record contains all mandatory tags.
                if (
                    strpos($dkimdata, 'v=DKIM1') !== false &&
                    strpos($dkimdata, 'k=') !== false &&
                    strpos($dkimdata, 'p=') !== false
                ) {
                    // DKIM record contains all mandatory tags, it's valid.
                    $message .= $checkmark . get_string('dkimvalidrecord', 'local_mailtest')  . '<br>';
                    $dkim = true;
                    break;
                }
            }
            if (!$dkim) {
                $message .= $xmark . get_string('dkiminvalidrecord', 'local_mailtest')  . '<br>';
            } else {
                if (empty($CFG->emaildkimselector)) {
                    $message .= $exclamation . get_string('dkimmissingselector', 'local_mailtest')  . '<br>';
                } else {
                    $message .= $checkmark . get_string('dkimselectorconfigured', 'local_mailtest')  . '<br>';
                }
            }
        } else {
            // Check to see if there might be a CNAME record instead.
            $records = @dns_get_record($emaildkimselector . '._domainkey.' . $domain, DNS_CNAME);
            $dkim = !empty($records);
            if ($dkim) {
                // DKIM CNAME type records can points to a DKIM key stored on another server.
                $message .= $checkmark . get_string('dkimrecordfound', 'local_mailtest')  . '<br>';
            }
        }

        if (!$dkim) {
            // No DKIM record was found.
            $message .= $exclamation . get_string('dkimnorecordfound', 'local_mailtest')  . '<br>';
        }
    }

    // Check DMARC records.

    $txtrecords = @dns_get_record('_dmarc.' . $domain, DNS_TXT);
    if (empty($txtrecords)) {
        // No DMARC records found.
        $message .= $xmark . get_string('dmarcnorecordfound', 'local_mailtest')  . '<br>';
        $success = false;
    } else {
        // DMARC records found.
        $message .= $checkmark . get_string('dmarcrecordfound', 'local_mailtest')  . '<br>';

        // Check if it has the required tags.
        foreach ($txtrecords as $record) {
            if (
                preg_match('/v=DMARC1;/', $record['txt'])
                && preg_match('/p=(none|quarantine|reject);/', $record['txt'])
            ) {
                // Required DMARC tags are present and valid.
                $message .= $checkmark . get_string('dmarctagsfound', 'local_mailtest')  . '<br>';

                // Check rua tag if present.
                $ruavalue = false;
                if (preg_match('/rua=([^;]+)/', $record['txt'], $matches)) {
                    $ruavalue = $matches[1];
                    // Validate rua value format (should be a valid URI).
                    if (!filter_var($ruavalue, FILTER_VALIDATE_URL) && !filter_var("mailto:" . $ruavalue, FILTER_VALIDATE_EMAIL)) {
                        // The rua value is not formatted correctly.
                        $message .= $xmark . get_string('dmarcruainvalid', 'local_mailtest')  . '<br>';
                        $success = false;
                    }
                }

                // Check ruf tag if present.
                $rufvalue = false;
                if (preg_match('/ruf=([^;]+)/', $record['txt'], $matches)) {
                    $rufvalue = $matches[1];
                    // Validate ruf value format (should be a valid URI).
                    if (!filter_var($rufvalue, FILTER_VALIDATE_URL) && !filter_var("mailto:" . $rufvalue, FILTER_VALIDATE_EMAIL)) {
                        // The ruf value is not formatted correctly.
                        $message .= $xmark . get_string('dmarcrufinvalid', 'local_mailtest')  . '<br>';
                        $success = false;
                    }
                }

                // Check pct tag if present.
                $pctvalue = false;
                if (preg_match('/pct=([0-9]+)/', $record['txt'], $matches)) {
                    $pctvalue = intval($matches[1]);
                    // Validate pct value range (should be between 0 and 100).
                    if ($pctvalue < 0 || $pctvalue > 100) {
                        // The pct value is not within the valid range.
                        $message .= $xmark . get_string('dmarcpctinvalid', 'local_mailtest')  . '<br>';
                        $success = false;
                    }
                }
                break;
            } else {
                // Required tags not found in any of the DMARC records.
                $message .= $checkmark . get_string('dmarctagsfound', 'local_mailtest')  . '<br>';
                $success = false;
            }
        }
    }

    // Check to ensure that either DKIM or SPF is configured.
    if (!$dkim && !$spf) {
        $message .= $xmark . get_string('dkimspffailed', 'local_mailtest')  . '<br>';
        $success = false;
    }

    // Check BIMI record.

    // Perform DNS query for BIMI TXT records.
    $txtrecords = @dns_get_record('_bimi.' . $domain, DNS_TXT);
    if (empty($txtrecords)) {
        // Required tags not found in any of the DMARC records.
        $message .= $xmark . get_string('biminorecordfound', 'local_mailtest')  . '<br>';
        $success = false;
    } else {
        // Records found. Check if it has the required tags.
        $message .= $checkmark . get_string('bimirecordfound', 'local_mailtest')  . '<br>';

        // Loop through each BIMI record.
        foreach ($txtrecords as $record) {
            // Extract BIMI record data.
            $bimidata = $record['txt'];

            // Check if BIMI record contains both v and l tags.
            if (strpos($bimidata, 'v=BIMI1') !== false && strpos($bimidata, 'l=') !== false) {
                // Required DMARC tags are present and valid.
                $message .= $checkmark . get_string('bimitagsfound', 'local_mailtest')  . '<br>';

                // Extract logo URL from the BIMI record.
                preg_match('/l=([^;]+)/', $bimidata, $matches);
                $logourl = $matches[1];

                // Validate existence of logo URL.
                $headers = @get_headers($logourl);
                if ($headers && strpos($headers[0], '200')) {
                    // Logo URL exists, BIMI record is valid.
                    $message .= $checkmark . get_string('bimiinvalidlogo', 'local_mailtest', $logourl)  . '<br>';
                    break;
                }
            }
        }
        if (!$success) {
            // Required tags not found in any of the DMARC records.
            $message .= $xmark . get_string('bimidmarcfailure', 'local_mailtest')  . '<br>';
            $success = false;
        }
        if ($pctvalue != 100) {
            $message .= $xmark . get_string('bimipctinvalid', 'local_mailtest')  . '<br>';
            $success = false;
        }
    }

    $icon = $success ? 'fa-info-circle text-info' : 'fa-exclamation-triangle text-warning';
    $title = get_string('iconlabel', 'local_mailtest', $domain);
    $bs = ($CFG->branch >= 500 ? 'bs-' : '');
    $popupicon = '<a class="btn btn-link p-0" role="button" data-' . $bs . 'container="body" data-' . $bs . 'toggle="popover"'
        . ' data-' . $bs . 'placement="right" data-' . $bs . 'content="<div class=&quot;no-overflow&quot;><p>{message}</p></div>"'
        . ' data-' . $bs . 'html="true" tabindex="0" data-' . $bs . 'trigger="focus">'
        . '<i class="icon fa ' . $icon . ' fa-fw " title="' . $title . '" aria-label="' . $title . '"></i></a>';
    $message = '<p class="alert alert-warning">' . get_string('checkingdomain', 'local_mailtest', $domain) . '</p>' . $message;
    $message = str_replace('{message}', str_replace('"', '&quot;', $message), $popupicon);

    return $message;
}
