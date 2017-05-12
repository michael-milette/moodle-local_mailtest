Local eMailTest plugin for Moodle
=================================

Copyright
---------
Copyright © 2015-2017 TNG Consulting Inc. - http://www.tngconsulting.ca/

This file is part of MailTest/eMailTest for Moodle - http://moodle.org/

eMailTest is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

eMailTest is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with eMailTest.  If not, see <http://www.gnu.org/licenses/>.

Authors
-------
Michael Milette - Lead Developer

Description
-----------
The eMailTest module for Moodle allows administrators to test Moodle's email
system. A trace of the SMTP dialogue will be displayed if the email message
cannot be sent for any reason.

Once the test email has been sent, you will need to check your email in-box
to confirm that the email message was successfully delivered. This plugin
will not do that for you.

Requirements
------------
This plugin requires Moodle 2.5+ from http://moodle.org

Changes
-------
The first public BETA version was released on 2015-11-01. This plugin is now considered STABLE.

For more information on releases since then, see CHANGELOG.md.

Download eMail Test for Moodle
------------------------------

The most recent STABLE release of eMail Test for Moodle is available from:
https://moodle.org/plugins/local_mailtest

The most development release can be found at:
https://github.com/michael-milette/moodle-local_mailtest

Installation and Update
-----------------------
Install the plugin, like any other plugin, to the following folder:

    /local/mailtest

See http://docs.moodle.org/33/en/Installing_plugins for details on installing Moodle plugins.

There are no special considerations required for updating the plugin.

Uninstallation
--------------
Uninstalling the plugin by going into the following:

Home > Administration > Site Administration > Plugins > Manage plugins > eMail Test

...and click Uninstall. You may also need to manually delete the following folder:

    /local/mailtest

Usage & Settings
----------------
There are no configurable settings for this plugin at this time.

The local_mailtest plugin is designed allow administrators to test Moodle
to ensure the email message system is correctly configured.

Once installed, login as a Moodle administrator and then click:

    Home > Site Administration > Server > eMail Test

Enter the email address of the recipient where you want to send the message
and click the [Send a test message] button.

If the email message was successfully sent by Moodle, you will see a message
that says "Success - The test message was successfully delivered to the SMTP
server.". If it fails to send the email, a dialogue between the Moodle and
the SMTP server will be displayed.

Security considerations
-----------------------
This plugin allows administrators to submit an email through a web form which
is restricted to logged in Moodle administrators only.

Motivation for this plugin
--------------------------
The development of this plugin was motivated through our own experience in Moodle development and comments in the Moodle support forums and is supported by TNG Consulting Inc.

Limitations
-----------
There are no known limitations at this time.

Future Releases
---------------
Let us know if you have any suggestions.

Further information
-------------------
For further information regarding the local_mailtest plugin, support or to
report a bug, please visit the project page at:

http://github.com/michael-milette/moodle-local_mailtest

Language Support
----------------
This plugin includes support for the English language. Additional languages including French are supported if you've installed one or more additional Moodle language packs.

If you need a different language that is not yet supported, please feel free
to contribute using the Moodle AMOS Translation Toolkit for Moodle at
https://lang.moodle.org/

This plugin has not been tested for right-to-left (RTL) language support.
If you want to use this plugin with a RTL language and it doesn't work as-is,
feel free to prepare a pull request and submit it to the project page at:

http://github.com/michael-milette/moodle-local_mailtest

Frequently Asked Questions (FAQ)
--------------------------------
**Question: Why do I get a 500 server error when I use eMailTest?**

Answer: This plugin has been extensively tested and used on hundreds of sites.
If you are getting this error, it is likely that you have a permissions
issue on your server which needs to be resolved.

**Question: Why does it say that Moodle sent the test message successfully yet I did not receive the email?**

Answer: This could be due to a number of reasons including but not limited to:

* Incorrect PHP Mail settings in your php.ini file (if you have not configured Moodle's SMTP settings).
* You may have an anti-virus or firewall blocking email communications.
* The mail server receiving your email may be discarding emails received from
your site.
* The test email may be blocked by anti-spam filters. Check your junk mail folder.

**Question: How do I find out the correct email settings I should be using?**

Answer: Contact your network administrator or your Web Hosting provider. These
are typically the same settings as you would use for an email client but the
administrator may need to add special settings to allow emails to be sent from
your support and no-reply email addresses.

**Question: Why is the test emails not being sent successfully?**

Answer: Your SMTP email settings are incorrect or the mail server is refusing
emails coming from your Moodle site. Read the whole dialogue that is displayed
when you send a test message. It will often provide some hint of where the
problem might be.

**Question: What is the difference between PHP Mail and Moodle SMTP Mail?**

Answer: If you don't configure the mail settings in Moodle, Moodle will hand
off the delivery of email to PHP's built-in mail system. Its settings are
typically in your server's PHP.INI file. If you do configure the SMTP
settings in Moodle, it will attempt to deliver emails directly to the
SMTP server.

**Question: How can I use this tool to send emails to other email addresses?**

Answer: You can't at the moment. If you really must, you can temporarily test the email address of one of the following "From" addresses:

* Your user account
* Support
* No-reply
* Primary admin

**Question: My email used to work in Moodle previous to 3.2. After upgrading to Moodle 3.2 or later, why can't my site send emails?**

Answer: As of Moodle 3.2, use of the no-reply email address is no longer optional in many cases. You will need to make sure it is now configured correctly. Some mail server may even only permit connections if this is actually a valid email address.

**Question: Why do I see a message about cron not having run for at least 24 hours?**

Answer: IMPORTANT - See https://docs.moodle.org/33/en/Cron . If a link is included within the message, clicking it will cause Moodle to try sending queued messages immediately. However, future message will still not be sent automatically. Clicking the link instead of configuring cron will just hide the notice for 24 hours after which it will return until you fix this issue.

If for some reason you are unable to setup an automated cron job and don't see the link, you can enable the link and allow remote running of the cron job by going to Site administration >Security > Site Policies and unchecking **Cron execution via command line only**. For a little extra security, also set a **Cron password for remote access**.

**Question: Why do I see some debugging code in the communications log even though debugging is turned off?**

Answer: Moodle does some validation before sending an email and can display some useful information so we've enabled some minimal display of debugging information. Informative lines start with **email_to_user**. You can ignore the line number references that follow the information line.
