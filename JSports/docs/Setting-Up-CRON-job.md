# Setting up CRON Job to Update Standings

JSports has a batch component that is used to calculate the standings.  This is a process that can be setup to automatically be executed via CRON. The script used to update standings is called 'updatestandings.php'.  Here are the steps needed to setup this process:

1.  Copy the updatestandings.php script to a desired folder under docroot for your server.
2.  Edit the updatestandings.php file and change the value of the $site variable.
3.  Go to Joomla administrator side and edit the JSport component options and SAVE the change.
4.  Set a "CRON KEY" value.  NOTE: This can be any value you wish it to be.  This value is required to be passed in the PHP command cron executes.
5.  Go to your SPANEL or CPANEL and locate how to setup a CRON job.
6.  Enter the PHP command that needs to execute that includes "updatestandings.php salt={value}"

NOTE:  The 'salt value' is the CRON KEY that you set in the Joomla component configuration.

Here is an example CRON command with 1234 as the CRON key define in the component configuration where the script resides in the cronscripts folder.

**0,30 * * * *	/usr/bin/php81 /home/acme/public_html/cronscripts/updatestandings.php salt=1234**



