# Change Log
All notable changes to this project will be documented in this file.

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
