SELECT g.divisionid, g.teamid, g.name, m.divisionid
FROM `jos2823_jsports_games` g, jos2823_jsports_map m
WHERE g.programid = 33
and g.programid = m.programid
and g.teamid = m.teamid;
and g.divisionid <> m.divisionid


SELECT g.id, g.gameid, g.divisionid as 'game-div', d1.name, g.opponentid, g.name, m.divisionid as 'map-div', d2.name
FROM `jos2823_jsports_games` g, jos2823_jsports_map m, jos2823_jsports_divisions d1, jos2823_jsports_divisions d2
WHERE g.programid = 33
and g.divisionid = d1.id
and m.divisionid = d2.id
and g.programid = m.programid
and g.opponentid = m.teamid
and g.divisionid <> m.divisionid;



#Query to find teams with Rosters
select teamid, count(*) from jos2823_jsports_rosters where classification='P' and programid = 33 group by teamid; 


#Query to find teams with no home games
SELECT t.id, t.name, t.contactname, t.contactemail
from jos2823_jsports_map m, jos2823_jsports_teams t
where m.teamid = t.id
and m.programid = 33
and t.published = 1
and m.published = 1
and t.id not in (
	select teamid from jos2823_jsports_games
	where programid = 33
	);

#Query to find teams with no roster
SELECT t.id, t.name, t.contactname, t.contactemail
from jos2823_jsports_map m, jos2823_jsports_teams t
where m.teamid = t.id
and m.programid = 33
and t.published = 1
and m.published = 1
and t.id not in (
	select teamid from jos2823_jsports_rosters
	where programid = 33
	and classification = 'P'
	);






#Additional Extensions#
- Mailing Tool - https://demo.mavrosxristoforos.com/





#Statistics#
SELECT 
    count(id) totalgames,
    count(case when gamestatus = 'S' then 1 end) gamesscheduled,
    count(case when gamestatus = 'C' then 1 end) gamescompleted,
    count(case when gamestatus not in ('S', 'C') then 1 end) gamesother
FROM `xkrji_jsports_games` 
WHERE programid=33;


##Total Games Per Season/Program##
SELECT * FROM `xkrji_jsports_games` WHERE programid = 33;

##Total TEAMS for season##
SELECT * FROM `xkrji_jsports_map` WHERE programid = 33 and published = 1;

##Total GAMES scheduled##
SELECT * FROM `xkrji_jsports_games` WHERE programid = 33 and gamestatus = 'S';

##Total GAMES completed##
SELECT * FROM `xkrji_jsports_games` WHERE programid = 33 and gamestatus = 'S';


#Most Recent Games#
SELECT *, d.name 
FROM `xkrji_jsports_games` g, xkrji_jsports_divisions d 
where g.divisionid = d.id 
and gamestatus = 'C'
and gamedate <= now()
and g.programid = 33
order by gamedate desc
limit 15


#Team Profile Record History Query#
select s.programid, p.name as programname, d.name as divisionname , teamid, teamname, wins, losses, ties, points, runsscored, runsallowed
from xkrji_jsports_standings s, xkrji_jsports_programs p, xkrji_jsports_divisions d
where teamid = 1036
and s.programid = p.id
and s.divisionid = d.id
UNION
select programid, programname, divisionname, teamid, teamname, wins, losses, ties, points, runsscored, runsallowed
from xkrji_jsports_recordhistory
where teamid = 1036;


#Standings Engine Queries#

select m.programid, m.divisionid, d.name, teamid , regid 
from xkrji_jsports_map m, xkrji_jsports_divisions d
where m.programid =33
and m.divisionid = d.id
and m.published = 1;


REGID:  2870, 2901


### Sum a team's data for their HOME GAME
select  m.divisionid, hometeamid id, score.programid, team.name teamname, 
		sum(if(hometeamscore > awayteamscore,1,0)) wins, sum(if(hometeamscore < awayteamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(hometeampoints) points, sum( awayteamscore ) runs_allowed, sum( hometeamscore ) runs_scored,
	'homegame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.hometeamid = team.id and score.hometeamid = m.teamid  and m.published = 1 and score.programid = 33 and gamestatus = 'C' and leaguegame = 'Y' 	and m.divisionid = 340
group by divisionid, id, team.name, programid 

### Sum a team's data for their AWAY GAME
select  m.divisionid, awayteamid id, score.programid, team.name teamname, 
		sum(if(awayteamscore > hometeamscore,1,0)) wins, sum(if(awayteamscore < hometeamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(awayteampoints) points, sum( hometeamscore ) runs_allowed, sum( awayteamscore ) runs_scored,
	'awaygame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.awayteamid = team.id 
		and score.awayteamid = m.teamid  
		and m.published = 1 
		and score.programid in (select id from xkrji_jsports_programs where status <> 'C')
		and gamestatus = 'C' 
		and leaguegame = 'Y' 	
		and m.divisionid = 340
group by divisionid, id, team.name, programid 


###Union query###

select  m.divisionid, hometeamid id, score.programid, team.name teamname, 
		sum(if(hometeamscore > awayteamscore,1,0)) wins, sum(if(hometeamscore < awayteamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(hometeampoints) points, sum( awayteamscore ) runs_allowed, sum( hometeamscore ) runs_scored,
	'homegame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.hometeamid = team.id and score.hometeamid = m.teamid  and m.published = 1 and score.programid = 33 and gamestatus = 'C' and leaguegame = 'Y' 	and m.divisionid = 340
group by divisionid, id, team.name, programid 
UNION
select  m.divisionid, awayteamid id, score.programid, team.name teamname, 
		sum(if(awayteamscore > hometeamscore,1,0)) wins, sum(if(awayteamscore < hometeamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(awayteampoints) points, sum( hometeamscore ) runs_allowed, sum( awayteamscore ) runs_scored,
	'awaygame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.awayteamid = team.id and score.awayteamid = m.teamid  and m.published = 1 and score.programid = 33 and gamestatus = 'C' and leaguegame = 'Y' 	and m.divisionid = 340
group by divisionid, id, team.name, programid 


### FINAL ###

select programid, id as teamid, teamname, sum(wins), sum(losses), sum(ties), sum(points), sum(runs_allowed), sum(runs_scored)
from 
(
select  m.divisionid, hometeamid id, score.programid, team.name teamname, 
		sum(if(hometeamscore > awayteamscore,1,0)) wins, sum(if(hometeamscore < awayteamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(hometeampoints) points, sum( awayteamscore ) runs_allowed, sum( hometeamscore ) runs_scored,
	'homegame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.hometeamid = team.id and score.hometeamid = m.teamid  and m.published = 1 and score.programid = 33 and gamestatus = 'C' and leaguegame = 'Y' 	and m.divisionid = 340
group by divisionid, id, team.name, programid 
UNION
select  m.divisionid, awayteamid id, score.programid, team.name teamname, 
		sum(if(awayteamscore > hometeamscore,1,0)) wins, sum(if(awayteamscore < hometeamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(awayteampoints) points, sum( hometeamscore ) runs_allowed, sum( awayteamscore ) runs_scored,
	'awaygame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.awayteamid = team.id and score.awayteamid = m.teamid  and m.published = 1 and score.programid = 33 and gamestatus = 'C' and leaguegame = 'Y' 	and m.divisionid = 340
group by divisionid, id, team.name, programid 
) temp
group by programid, teamid



### GET DATA QUERY ###

select tempa.programid, tempa.divisionid, tempa.divname, tempa.teamid, tempa.teamname, wins, losses, ties, points, runsallowed, runsscored from (
select m.programid, m.divisionid, d.name as divname, teamid , t.name as teamname, regid 
from xkrji_jsports_map m, xkrji_jsports_divisions d, xkrji_jsports_teams t
where m.programid in (select id from xkrji_jsports_programs where status <> 'C')
and m.divisionid = d.id
and m.teamid = t.id
and m.published = 1
) tempa
LEFT JOIN
(
select programid, id as teamid, teamname, sum(wins) wins, sum(losses) losses, sum(ties) ties, sum(points) points , sum(runs_allowed) runsallowed, sum(runs_scored) runsscored
from 
(
select  m.divisionid, hometeamid id, score.programid, team.name teamname, 
		sum(if(hometeamscore > awayteamscore,1,0)) wins, sum(if(hometeamscore < awayteamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(hometeampoints) points, sum( awayteamscore ) runs_allowed, sum( hometeamscore ) runs_scored,
	'homegame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.hometeamid = team.id 
		and score.hometeamid = m.teamid  and m.published = 1 
		and score.programid in (select id from xkrji_jsports_programs where status <> 'C')
		and gamestatus = 'C' 
		and leaguegame = 'Y' 	
group by programid, divisionid, id, team.name
UNION
select  m.divisionid, awayteamid id, score.programid, team.name teamname, 
		sum(if(awayteamscore > hometeamscore,1,0)) wins, sum(if(awayteamscore < hometeamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(awayteampoints) points, sum( hometeamscore ) runs_allowed, sum( awayteamscore ) runs_scored,
	'awaygame' game
from xkrji_jsports_games score, xkrji_jsports_teams team, xkrji_jsports_map m  
where score.awayteamid = team.id 
		and score.awayteamid = m.teamid  
		and m.published = 1 
		and score.programid  in (select id from xkrji_jsports_programs where status <> 'C')
		and gamestatus = 'C' 
		and leaguegame = 'Y' 	
group by programid, divisionid, id, team.name 
) temp
group by programid, teamid
) tempb
on tempa.teamid = tempb.teamid and tempa.programid = tempb.programid





<hr>




insert into xkrji_jsports_registrations 
	(id, divisionid, programid, teamid, name, address, city, state, email, phone, teamname, agegroup, published, skilllevel)
select 0, 0, 33, 0, concat(firstname," ",lastname), "", city, "IL", email, phone, teamname, SUBSTRING(agegroup,1,CHAR_LENGTH(agegroup) - 1), 0, skilllevel
	from se_registration;


2851


 		<submenu>
			<menu link="option=com_jsports&amp;view=leagues" view="leagues"  img="" alt="JSports Leagues">Leagues</menu>
			<menu link="option=com_jsports&amp;view=programs" view="programs"  img="" alt="JSports Programs">Programs</menu>			
			<menu link="option=com_jsports&amp;view=divisions" view="divisions"  img="" alt="JSports Divisions">Divisions</menu>
			<menu link="option=com_jsports&amp;view=teams" view="teams"  img="" alt="JSports Teams">Teams</menu>
			<menu link="option=com_jsports&amp;view=registrations" view="registrations"  img="" alt="JSports Registratino">Registrations</menu>
			<menu link="option=com_jsports&amp;view=venues" view="venues"  img="" alt="JSports Venues">Venues</menu>
		</submenu> 	



**SEF URL**

/baseurl/baseball/program/teams/
/baseurl/baseball/program/standings
/baseurl/baseball/team/id/view|edit



create or replace view xkrji_jsports_view_mapinfo as
select dm.id, dm.programid, p.name as programname, dm.teamid, t.name as teamname, dm.divisionid, d.name as divisionname, d.agegroup
from xkrji_jsports_map dm,
	xkrji_jsports_teams t,
    xkrji_jsports_programs p,
    xkrji_jsports_divisions d
where dm.teamid = t.id
and dm.programid = p.id
and dm.divisionid = d.id;




create or replace view xkrji_jsports_view_lastplayed as
select teamid, year(lastgame) as lastplayed, lastprogram as lastprogramid, p.name as lastprogramname from 
(
select hometeamid as teamid, max(gamedate) as lastgame, max(programid) as lastprogram from xkrji_jsports_games
group by hometeamid
UNION
select awayteamid as teamid, max(gamedate) as lastgame, max(programid) as lastprogram from xkrji_jsports_games
group by awayteamid
) as temp, xkrji_jsports_programs as p
where temp.lastprogram = p.id
group by teamid;



select a.*, b.agegroup, b.name
FROM
(
select hometeamid as teamid, max(divisionid) as divid, max(gamedate) as lastgame from xkrji_jsports_games
group by hometeamid
union
select awayteamid as teamid, max(divisionid) as divid, max(gamedate) as lastgame from xkrji_jsports_games
group by awayteamid
    ) as a, xkrji_jsports_divisions as B
where a.divid = b.id;






create or replace view xkrji_jsports_view_yearplayed as
select teamid, lastgame as lastplayed from 
(
select hometeamid as teamid, year(gamedate) as lastgame from xkrji_jsports_games
group by hometeamid, lastgame
UNION
select awayteamid as teamid, year(gamedate) as lastgame from xkrji_jsports_games
group by awayteamid
) as temp
group by teamid, lastgame






select dm.id, dm.programid, p.name, year(p.dm.teamid, t.name, dm.divisionid, d.name, d.agegroup
from xkrji_jsports_map dm,
	xkrji_jsports_teams t,
    xkrji_jsports_programs p,
    xkrji_jsports_divisions d
where dm.teamid = t.id
and dm.programid = p.id
and dm.divisionid = d.id;
)

#Scoring#

##reset test data##
update xkrji_jsports_games set gamestatus='S', hometeampoints = 0, awayteampoints = 0
where programid = 33


##Home Team Points##

update xkrji_jsports_games set hometeampoints = 2, awayteampoints = 0
where programid = 33
and hometeamscore > awayteamscore;

##Away Team Points##
update xkrji_jsports_games set awayteampoints = 2, hometeampoints = 0
    where programid = 33
    and awayteamscore > hometeamscore;
    
##Tie##    
update xkrji_jsports_games set awayteampoints = 1, hometeampoints = 1
    where programid = 33
    and awayteamscore = hometeamscore;
    
    
    
#SPORTS ENGINE - GAME CONVERSION#
1. Use MySQL Workbench to create a table based on the CSV##
2. Rename table
3. Created new colums 
	- programid 
	- divisionid
	- hometeamid
	- awayteamid
	- teamid
	- opponentid
	- homekey
	- awaykey
	
4. Rename several of the columns like "Division Name" to "divisionname"
- rename 1 to "entrystatus"
- Game Id to "gameid"

5. Set the program id
update `2024-sports-engine-games-list` set programid = 33
6. Update the 10U W North divisin name

<code>
update sportsenginegames set divisionname = "10U - WHITE - NORTH"
where divisionname = "10U - WHITE  - NORTH"
</code>

7.  Update DIVISION ID

<code>
UPDATE sportsenginegames
	SET sportsenginegames.divisionid = (
	    SELECT xkrji_jsports_divisions.id 
	    FROM xkrji_jsports_divisions
	    WHERE 
	    	lower(xkrji_jsports_divisions.name) = lower(sportsenginegames.divisionname)
	    	and programid = 33
        );	
        
</code>


7. Rename "Home Team" and "Away Team" to hometeam and awayteam

8. Update new key fields

<code>
update `sportsenginegames` set homekey = replace(lower(trim(concat(divisionid,'-',hometeam))),' ','');

update `sportsenginegames` set awaykey = replace(lower(trim(concat(divisionid,'-',awayteam))),' ','');
</code>

9. Update hometeam and awayteam for the Gators SLaton TEam due to unrecognizable characters

<code>
update `sportsenginegames` set hometeam = 'Illinois Gators - Slaton'
where hometeam like '%slaton%';

update `sportsenginegames` set awayteam = 'Illinois Gators - Slaton'
where awayteam like '%slaton%';
</code>

10. Update the new hometeamid and awayteamid

11.  Set the TEAM ID to the HOMETEAMID and the OPPONENT ID to the AWAYTEAMID

<code>
update `sportsenginegames` set teamid = hometeamid, opponentid = awayteamid
</code>

12.  Calculate the new date.

<code>
update `sportsenginegames`
	set newdate = concat(split_str(date,'/',3),'-',split_str(date,'/',1),'-',split_str(date,'/',2))
</code>







    
##Update SportsEngine DivisionID ##
UPDATE sportsenginegames
	SET sportsenginegames.divisionid = (
	    SELECT xkrji_jsports_divisions.id 
	    FROM xkrji_jsports_divisions
	    WHERE 
	    	lower(xkrji_jsports_divisions.name) = lower(sportsenginegames.divisionname)
	    	and programid = 33
        );	
        
        
##Identify AWAY Teams that can't find Match (WITH REPLACE)##        
select distinct(awaykey) 
from sportsenginegames
where awaykey not in (
	select replace(lower(concat(d.divisionid,'-',t.name)),' ','')
	from jos2823_jsports_map d, jos2823_jsports_teams t
	where d.teamid = t.id
	and d.published = 1
	and d.programid = 33
)
and length(awaykey)>4

##Identify HOME Teams that can't find Match (WITH REPLACE)##        
select distinct(homekey) 
from sportsenginegames
where homekey not in (
	select replace(lower(concat(d.divisionid,'-',t.name)),' ','')
	from jos2823_jsports_map d, jos2823_jsports_teams t
	where d.teamid = t.id
	and d.published = 1
	and d.programid = 33
)
and length(homekey)>4
and hometeamid <> 0

select t.id, t.name, replace(lower(concat(d.divisionid,'-',t.name)),' ','')
	from jos2823_jsports_map d, jos2823_jsports_teams t
	where d.teamid = t.id
	and d.published = 1
	and d.programid = 33

##Update the Sports Engine data with Teams ID##
UPDATE sportsenginegames 
SET sportsenginegames.hometeamid = 
	( SELECT distinct(t.id ) 
		from jos2823_jsports_map d, jos2823_jsports_teams t 
		WHERE replace(lower(concat(d.divisionid,'-',t.name)),' ','') = sportsenginegames.homekey and d.teamid = t.id );


		

UPDATE sportsenginegames 
SET sportsenginegames.awayteamid = 
	( SELECT distinct(t.id ) 
		from jos2823_jsports_map d, jos2823_jsports_teams t 
		WHERE replace(lower(concat(d.divisionid,'-',t.name)),' ','') = sportsenginegames.awaykey and d.teamid = t.id );
		
		
		


##INSERT INTO JSPORTS GAME TABLE ##
insert into jos2823_jsports_games (programid, divisionid, teamid, opponentid, hometeamid,awayteamid, hometeamname, awayteamname, location,gamestatus, newdate, newtime, gameid, name, leaguegame, forfeit, published, enteredby, homeindicator)
	SELECT programid, divisionid, teamid, opponentid, hometeamid, awayteamid, hometeam, awayteam, location, 'S', newdate, time, gameid,
		concat(awayteam,' @ ', hometeam), 1, 'N', 1, 'SYSTEM IMPORT', 1
	FROM `sportsenginegames` 
	WHERE programid = 33
		and hometeamid <> 0
		and awayteamid <> 0
		and gameid not in (select gameid from jos2823_jsports_games);

		and hometeamid = 1011
		and gameid = '6596dfc23587e9000125452e'	


##Find Games Not in JSPORTS GAMES TABLE##
SELECT * FROM `sportsenginegames` WHERE gameid not in (select gameid from jos2823_jsports_games)

##Update the GAMEDATE ##
update `jos2823_jsports_games` set gamedate = newdate 
where enteredby = 'SYSTEM IMPORT'
and programid = 33;

##Update the GAMETIME##
update `jos2823_jsports_games` set gametime = newtime 
where enteredby = 'SYSTEM IMPORT'
and programid = 33;






SELECT * FROM `sportsenginegames` 
where entrystatus <> 1
and programid = 33
and hometeamid <> 0
and awayteamid <> 0
and hometeamid = 1011
and gameid = '6596dfc23587e9000125452e'
and gameid not in (select gameid from jos2823_jsports_games);






#GAMES TO REVISIT#

##away teams##
348-edwardsvillehogs
352-edwardsvillewarriors
345-gatewaystallions
352-gatewaystallions-randolph
350-extremegates12u 


348-roxanashells-pitchford --- ID 1161  *DONE*
345-gatewaystallions -- 1129 *DONE*
341-thunder-seger -- 1121   *DONE*
346-bombers-newgent -- 1038  *DONE*
346-oã¢â‚¬â„¢fallonathletics-klier  -- 1095  *DONE*
352-jerseyvillepanthers -- ID 1030  *DONE*
348-carlinville-borgini -- ID 1202   *DONE*
339-wildcatsbaseball-weh -- ID 1062   *DONE*
352-edwardsvillewarriors -- ID  1130  *DONE*
352-gatewaystallions-randolph --  ID 1109 *DONE*
352-glencarboncoyotes13u   -- ID 1134  *DONE*
340-carlinvillecavies-dowland --- ID 1084   *DONE*
348-edwardsvillehogs  -- ID 1154   *DONE*
350-extremegates12u -- ID 1155   *DONE*
352-southernillinoisroughnecks -- ID 1157  *DONE*
337-teammascoutah-lauderdale -- 1059  *DONE*
348-riverbendbulldogs -- 1118   *DONE*
350-stauntonstorm-armbruster -- ID 1102   *DONE*
350-jerseypanthers-schannot   -- ID 1206   *DONE*
352-carlinvillecavaliers-suits13u  -- ID 1207   *DONE*

##home teams ##
348-carlinville-borgini -- ID 1202
352-glencarboncoyotes13u  -- ID 1134
348-roxanashells-pitchford  --  1161

update `sportsenginegames` set hometeamid = 1202
where homekey = '348-carlinville-borgini'


	update `sportsenginegames` set awayteamid = 1093
	where awaykey = '350-highlandheat-hammer'








