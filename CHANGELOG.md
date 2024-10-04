# Change Log
All notable changes to this project will be documented in this file.

## [3.1.6] - 2024-10-03
### Updates
- No version change as there was no change to the code.
- Tested compatible with Moodle 4.5.

## [3.1.6] - 2024-07-22
### Updated
- Fixed deprecated strings in Moodle 4.4.

## [3.1.5] - 2024-06-28
### Update
- Fixed some compatibility issues with PHP 5.6 and 7.0.
- DNS test exclamation triangles icons now showing up in older versions of Moodle LMS.
- Improved SPF, DMIK and DMARC detection.

## [3.1.2] - 2024-04-28
### Updated
- Fixed incorrect error message for invalid DMARC ruf.
- Cleaned-up language file.

## [3.1.1] - 2024-04-23
### Added
- Github CI Workflows.
- Github security reporting
### Updated
- Bug reporting form.
- Tested compatible with PHP 5.6 to 8.3.
- Compatible with Moodle up to v4.4.

## [3.1.0] - 2024-02-22
### Added
- Now tests domain for SPF configuration.
- Now tests Moodle and domain for DKIM configuration.
- Now tests domain for DMARC configuration.
- Now tests domain for BIMI configuration.
- Updated copyright for 2024.

## [3.0.1] - 2023-10-23
### Updated
- Now only run the tests that make sense in order to reduce unlikely recommendations.
- Now displays missing recommendations.
- Tested compatible with PHP 5.6 to 8.2.

## [3.0.0] - 2023-10-20
### Added
- New diagnostic feature provides issue-specific information and some common solutions.
- New detection of SMTP credential issue.
- New detection of SMTP server connection failure/timeout.
- New detection of SMTP server rejection.
- New detection of connection timeout issues.
- New detection of DNS resolution failure.
- New detection of missing port or SSL/TLS protocol/port mismatch.
- New detection of closed port.
- New in-app information to help you troubleshoot if you don't receive the test email.
### Updated
- Documentation. Links to http:// have been replaced with https://.
- Compatible with Moodle up to v4.3.

## [2.0.2] - 2022-05-06
### Updated
- Fix-31: Corrected deprecated FILTER_SANITIZE_STRING deprecation notice in PHP 8.1.
- Fix-30: Corrected undefined $CFG->branch error when installing at the same time as initial Moodle install.
- Fix-20: Now correctly handles site names with special characters such as ampersands.
- Compatible with PHP 5.6 to 8.1.
- Compatible with Moodle up to v4.2.
- Copyright notice for 2023.

## [2.0.1] - 2022-12-11
### Added
- Added documentation for sending email via Gmail.
### Updated
- Added missing string for Moodle 2.4 to 3.9.
- Compatible with PHP 5.6 to 8.0.
- Compatible with Moodle up to v4.1.

## [2.0.0] - 2022-04-24
### Added
- Detects and reports the log location if mail.log is set in php.ini.
- Detects if email diverting is enabled.
- Detects if email/messaging is disabled by way of $CFG->noemailever.
### Updated
- Removed db directory as there are no tables used by this plugin.
- .gitignore
- Compatible with PHP 5.6 to 7.4.
- Compatible with Moodle up to v4.0.
- Copyright notice for 2022.

## [1.4.0] - 2020-09-20
### Added
- composer.lock
### Updated
- Fixed display issue with Moodle 3.9.
- Compatibility with PHP 5.6 to 7.3.
- Copyright notice.
- Fixed composer installation issues with mediamaisteri/moodle-installer. See https://github.com/juho-jaakkola/moodle-project
- Compatible with Moodle up to v3.11.

## [1.3.0] - 2019-11-17
### Added
- If PHP mail() function method is used instead of SMTP, it is not possible to capture the dialogue between Moodle and an SMPT server. Information will now be displayed showing potential locations for a server log.
- Documentation suggesting adding a port number to the SMTP server address.
### Updated
- Fixed compatibility with Moodle 3.8 due to $CFG->debugsmtp now being optional.
- Copyright notice.

## [1.2.1] - 2018-05-21
### Added
- Support for Privacy API.
- More answers in FAQ section of README.md.
- From and To addresses in status message.
- Support for Reply-to address.
### Updated
- Fixed a PHP notice that rarely but occasionally occurred when reloading the results page without going through the form again.
- Documentation.
- phpDocs error.

## [1.1.3] - 2018-04-30
No code changes.
### Updated
- Moodle eMailTest has been successfully tested for compatibility with Moodle 2.5 to 3.5.
- More answers in FAQ section of README.md.
- Documentation.
- Copyright for 2018.

## [1.1.2] - 2017-10-29
No code changes.
### Updated
- Moodle eMailTest has been successfully tested for compatibility with Moodle 2.5 to 3.3.

## [1.1.1] - 2016-05-12
No code changes.
### Added
- CONTRIBUTE.md.
### Updated
- Removed Limitations notice in the README.md file. This plugin is confirmed to work with PHP 7.1 (thanks davidpesce)
- Moodle eMailTest has been successfully tested for compatibility with Moodle 2.5 to 3.3.
- Reorganized README.md (New: logo, status badges, table of contents, contributing, etc).

## [1.1.0] - 2016-02-03
### Added
- Option to always show SMTP connection log, even if there is no error.
- Now detects whether Moodle should be always sending from no-reply address.
- Now notifies you if an email address is invalid.
- New option to send from primary admin user's email address.
- Now displays Moodle informative debug messages from email_to_user().
- Added more answers to FAQ section of documentation relating to cron and Moodle 3.2+.

## [1.0.1] - 2016-01-02
### Updated
- Made source code comments clearer and phpdoc valid.
- Corrected and updated copyright notice to include 2017.
- Corrected missing closing </p> tag in English language file. (Thanks lucaboesch!)

## [1.0.0] - 2016-11-27
### Added
- A notification will now be displayed if cron hasn't run in the last 24 hours.
- FAQ, in README.md, indicating what to do if you see the new cron notification.
- Provides better recommendations depending on whether the SMTP server
  refused communications from Moodle or it refused delivery of the message.
### Changed
- Changed the visible name of the plugin to eMail Test to help people find it.
- Since there have been no issues reported, the plugin is now considered STABLE.
- The link to eMail Test will now appear in the Email section of the Server tab
  in Moodle 3.2's new Site Adminstration page.
- Moodle eMailTest has been successfully tested for compatibility with
  Moodle 2.5 to 3.2.

## [0.3.0] - 2016-05-21
### Added
- This CHANGELOG.md file.
- French Translation in AMOS - Update your French language packs!
- Link to plugin's discussion forum in the plugin's Moodle.org Plugins directory.
- README.md now contains answers to common questions.
- Displays whether using PHP mail() function or talking directly to the SMTP server.
- Option to choose whether test email will be sent from your email address,
  the noreply user email address, or the support email address.
- Moodle MailTest is now compatible with Moodle 2.5 to 3.1 LTS.

## [0.2.0] - 2015-11-05
### Added
- Moodle MailTest is Now also compatible with Moodle 2.8, 2.9 and 3.0.
- Added link to plugin's discussion forum in the plugin's Moodle.org Plugins directory.
### Updated
- Plugin's status changed from ALPHA to BETA.
- Corrected links from the plugin's page in the Moodle.org Plugins directory.
- Corrected documentation and added formatting to make it easier to read on GitHub.

## [0.1.0] - 2015-11-02
### Added
- Initial public release on Moodle.org and GitHub.
- Plugin officially compatible and tested with Moodle 2.5, 2.6 and 2.7.
