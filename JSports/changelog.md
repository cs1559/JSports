#Change Log

#0.1.3
- Fixes to resolve posting score issues.
- Added new MailService in preparation for sending email notifications when game scores are
posted.

#0.1.2
- Made change to allow admins to see any roster.
- Fixed display of website url on team profile.

#0.1.1 
- Cleaned up code using SonarLint

#0.1.0
- Removed Hello folder from templates
- Added staff/player tranlsation on the manage rosters page.
- changed sort order of roster model on the SITE side.

#0.0.13
- Added support for a cron job to schedule standings updates.

#0.0.12
- Fixed issue with the missing function in TeamService::getTeamDivision.  Renamed it to getTeamDivisionId
- Fixed a bug in the helper routine that displayed game times.
- Added program name to the default page when "baseball" menu item is selected.
- Added button to manage roster, manage schedules to be more clear on how to navigate back to the profile.

#0.0.11
- Added confirmation popup before allowing the deletion of a game or roster entry.
- Added logic to prohibit the deletion of a COMPLETED game.
- Added ROSTR to Team Profile (with restrictions).

#0.0.10
- Fixed LIMIT issue on the Teams List.
- Added responsive table wrapper to the standings page, schedules page (edit) and roster page (edit).
- modifyed ROSTER entry page to prevent staff member attributes from being displayed for a player in EDIT mode.

#0.0.9
- Fixed a bug with the limit option on the Venues front end list.
- Improved responsiveness of the table on Team List and Team Profile and the dashboard game list.

#0.0.8
- Fix bug with presenting 24 hour time format into HH:MM AM/PM format.  Games at noon were presenting as "12:00 AM" instead of "12:00 PM".
- Added row highlighting to "Teams List".
- Changed sort order on Team List to sort by Team Name, City or Division
- Fixed security issue that was prohibiting alternate Admins for a profile from editing their page.

#0.0.7
- Fixed image upload issue.  
- On the team edit admin page (backend), update the "ownerid" list to include email address and proper sort order.
- Added ROLE and USERID to the roster entry page.
- Updated the Roster form.xml to align with the new fields on the roster page.
- Fixed redirect from the Rosters EDIT page when someone hits the cancel button.
- Added "Team Staff" section to the profile page.
- Added helper logic to properly format game times when the schedule is displayed.

#0.0.6
- Fixed a UserService not found error generated out of the SecurityService class.
- Changed Logoupload.xml forms file to all lowercase to eliminate problems when deployed on server.  
- Changed the TEAMS filter form to filter programid to list non-registration only programs.
- Added page header and adding/editing a Roster Member
a

#0.0.5
- Fixed the issue with the EDIT/DELETE buttons on the Manage schedule screen option.
- Resolved a jQuery issue with onChange to the team profile page when the eDOcman module is displayed at the same time.
- Moved "Location" under the game name on the manage schedule page to resolve formatting issues and make the location italicized.
- Fixed issue with canel button on the Rosters page.  It now properly sets the team id.
- Fixed type of "Scheule" on the team profile menu.  Changed to Schedule.

#0.0.4
- Added view/menu option to list all Venues on file from the frontend.
- Updated the router to support the Venues menu option.

#0.0.3
- Added the program name to the games/schedule on the Teams Profile Page
- Refreshed the default.php Dashboard page to show the upcoming games list.