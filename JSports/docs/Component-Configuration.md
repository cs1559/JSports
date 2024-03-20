# Component Configurations

This section covers the configuration options of the JSports component via the administrator Global Configuration.


### Options

**Logo Path** - This is the directory/folder where the team logos are stored.

**Team Logo Directory Prefix** - This value is used to define the subfolder where a teams individual logo is stored.  The two options are "Team-Id" and "Team".   This value is pre-pended to the team id (e.g. 1061) for form the name of the subfolder.  Ex.  Teamid-1061 where 1061 is the ID of the team profile.

**Front End Administration Enabled** - This indicator defines whether or not a user can manage their team profile from the front-end of the application.

**Remove IDs from URL** - TBD

**Show Standings Position** - TBD

**Item Id** - Not used.

**Cron Key** - This value is used to specify a salt value to ensure the client that calls a URL used in a cronscript is authorized to execute the specific script.

### Email

**Enable Event Emails** - This indicator will turn ON or OFF any/all email notifications that will are available by various event triggers.  Ex.  onAfterPostScore.

**From Email Address** - This is the **FROM** email value that is used in the event notifacations.  

**Name of the FROM person** - This is the name of the person that the email recipients will see in the email header.  This is NOT an email address.

**Copy League Admin** - This indicator specifies whether or not a LEAGUE Administrator will be cc'd on the email notifications.

**Admin Email(s)** - These are the league administrator emails that will be cc'd on event notificaitons if the 'Copy League Admin' indicator is enabled.
