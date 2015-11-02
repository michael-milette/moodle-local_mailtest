Local MailTest plugin for Moodle
================================

Copyright
---------
Copyright © 2014-2015 TNG Consulting Inc. - http://www.tngconsulting.ca

This file is part of MailTest for Moodle - http://moodle.org/

MailTest is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

MailTest is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with MailTest.  If not, see <http://www.gnu.org/licenses/>.

Authors
-------
Michael Milette - Lead Developer

Description
-----------
The Moodle MailTest module allows administrators to test Moodle's email
system. A trace of the SMTP dialogue will be displayed if the email message
cannot be sent for any reason.

Once the test email has been sent, you will need to check your email inbox
to confirm that the email message was successfully delivered. This plugin
will not do that for you.

Requirements
------------
This plugin requires Moodle 2.5+ from http://moodle.org

Changes
-------
2015-11-01 - Initial version.

Installation
------------
Install the plugin, like any other plugin, to the following folder:
/local/cms

See http://docs.moodle.org/27/en/Installing_plugins for details
on installing Moodle plugins.

Unininstallation
----------------
Uninstalling the plugin by going into the following:

Home > Administration > Site Administration > Plugins > Manage plugins

...and click Uninstall. You may also need to delete the following folder:

    /local/mailtest

Usage & Settings
----------------
The local_mailtest plugin is designed allow administrators to test Moodle
to ensure the email message system is correctly configured.

There are no settings for this plugin.

Once installed, login as an administrator and then click:

    Home > Site Administration > Server > Mail Test

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
The development of this plugin was motivated by comments in the Moodle support
forums and is supported by TNG Consulting Inc.

Further information
-------------------
For further information regarding the local_mailtest plugin, support or to
report a bug, please visit the project page at:

    http://github.com/michael-milette/moodle-local-mailtest

Right-to-left support
---------------------
This plugin has not been tested with Moodle's support for right-to-left (RTL)
languages.

If you want to use this plugin with a RTL language and it doesn't work as-is,
feel free to prepare a pull request and submit it to the project page at:

    http://github.com/michael-milette/moodle-local-mailtest

Future
------
TODO: Add option to send message either from Noreply, Support or other email address.
