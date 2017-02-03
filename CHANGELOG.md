# Change Log
All notable changes to this project will be documented in this file.

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
- Moodle eMailTest has been successfully tested with for compatibility
  with Moodle 2.5 to 3.2.

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

### Changed
- Plugin's status changed from ALPHA to BETA.

### Fixed
- Corrected links from the plugin's page in the Moodle.org Plugins directory.
- Corrected documentation and added formatting to make it easier to read on GitHub.

### Added
- Added link to plugin's discussion forum in the plugin's Moodle.org Plugins directory.

## [0.1.0] - 2015-11-02
### Added
- Initial public release on Moodle.org and GitHub.
- Plugin officially compatible and tested with Moodle 2.5, 2.6 and 2.7.
