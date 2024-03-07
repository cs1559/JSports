# JSports

## Overview
This component is focused on enabling an organization to manage the operation of their sports league.


###Leagues/Organizations###

current_season=28

email_from_addr=chris@swibl-baseball.org
email_from_name=SWIBL
copyright_notice=(c)2006-2030 Southwestern Illinois Baseball League

logo_folder=images/jleague/
max_logo_height=150
max_logo_width=150
logo_thumbnail_prefix=thumb
max_thumbnail_height=75
max_thumbnail_width=75
games_on_frontpage_scoreboard=8
games_on_league_scoreboard=24
x`submit_scores_enabled=1
edit_game_scores_enabled=1
schedules_enabled=1

events_enabled=0

show_position_in_standings=0
use_gmaps_for_venues=1
seo_enabled=0


###Programs
A "program" represents a "bound context" that includes a series of activities that has an overall beginning and an end.  This was previously known as a "season" (e.g. Spring 2020, Fall 2021, etc.).  Each program has a series of attributes that can be managed via the administrative backend section of the system.

There is a 1 to many relationship between the League/Organization and a programs.  Multiple programs can exist for a given league/organization with each program having their own beginning and ending date.

**Status** - There are three status values for the program. They are Active, Pending and Closed.  

- *Active* is meant to identify the currently active program for the organization.
- *Pending*
- *Closed* is the status once the program is closed.  All standings/rankings, etc. are locked in at that time and changes to teams, scores, etc. from the frontend are no longer allowed. 

**Setup Final** - This configuration setting allows the organization to publish "league standings" prior to being finalized in a "Looks Who's Coming" view.  This way teams who registered can be visible PRIOR to the actual division/teams are made public.

**Registration Open** - 

**Active** - 

**Registration Start** - This is the effective start date of the program.
**Registration End** - This is the effective end date of the program.
**Publish Standings**

