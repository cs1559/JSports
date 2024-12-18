
# Query to get coaches/admins emails for updating newsletter list.
SELECT a.* FROM `jos2823_jsports_rosters` a, jos2823_jsports_programs b
WHERE a.programid = b.id and length(email) > 0 and programid = 35 and b.status = 'A';


# Registrations
select teamname, grouping, name as 'coach name', address, city, email, phone, cellphone, registeredby, skilllevel, if (playoffs=1,'Yes','No') as "Playoffs"
from jos2823_jsports_registrations
where published = 0 and programid= 35
order by grouping, name;

# Identify teams with no contact person

select t.* 
from jos2823_jsports_teams t 
where id IN (select teamid from jos2823_jsports_map where programid = 33)
and t.ownerid < 1
and id not in (select teamid from jos2823_jsports_rosters where userid > 0)



insert into xkrji_jsports_past_standings
	(id, programid, divisionid, position,teamid, teamname, headcoach,
	wins, losses, ties, gamesplayed, points, runsscored, runsallowed)
select 0, season, division_id, position, team_id, teamname, headcoach,
	wins, losses, ties, wins+losses+ties, points, runs_scored, runs_allowed
from joom_jleague_temp_standings


#Teams with NO roster
select t.id, t.name, t.contactname 
from jos2823_jsports_teams t, jos2823_jsports_map m 
where t.id= m.teamid
and m.programid = 33
and m.published = 1
and t.id not in (
select teamid
from jos2823_jsports_rosters
where programid = 33
and classification = 'P'
group by teamid
    );


#Teams with NO HOME GAME
select t.id, t.name, t.contactname 
from jos2823_jsports_teams t, jos2823_jsports_map m 
where t.id= m.teamid
and m.programid = 33
and m.published = 1
and t.id not in (
select hometeamid
from jos2823_jsports_games
where programid = 33
group by teamid
    );

    
#Average Run Differential by Division
select temp1.programid, temp1.divisionid, d.name, avg(rundiff)
from (select programid, divisionid, abs(hometeamscore - awayteamscore) as rundiff from jos2823_jsports_games
	where programid = 33 and leaguegame = 1 and gamestatus = 'C'
      ) as temp1, jos2823_jsports_divisions as d
where temp1.divisionid = d.id 
group by programid, divisionid;

#Duplicate Completed Games
select divisionid, d.name, t.name, gamedate, hometeamid, awayteamid, hometeamscore, awayteamscore ,count(*) total_game 
from jos2823_jsports_games g, jos2823_jsports_divisions d, jos2823_jsports_teams t
where g.programid = 33 and gamestatus = 'C' and divisionid = d.id and hometeamid = t.id
group by divisionid, gamedate, hometeamid, awayteamid, hometeamscore, awayteamscore
having total_game > 1;






SELECT id, name, gamedate, gamestatus, hometeamid, hometeamscore, awayteamid, awayteamscore, hometeampoints, awayteampoints, leaguegame 
FROM `jos2823_jsports_games` 
WHERE (hometeamid = 1033) 
and hometeamscore > awayteamscore
and hometeampoints = 0
and programid = 33
and gamestatus in ('C');


SELECT id, name, gamedate, gamestatus, hometeamid, hometeamscore, awayteamid, awayteamscore, hometeampoints, awayteampoints, leaguegame 
FROM `jos2823_jsports_games` 
WHERE (awayteamid = 1033) 
and awayteamscore > hometeamscore
and awayteampoints = 0
and programid = 33
and gamestatus in ('C');


#Idenitfy teams where the points don't match the wins + ties
SELECT * FROM `jos2823_jsports_standings` where points <> (wins*2 + ties * 1);



select divisionid, d.name as divname, teamname, headcoach, wins, losses, ties, points, gamesplayed, winpct, runsscored, runsallowed
from jos2823_jsports_standings s, jos2823_jsports_divisions d
where divisionid = d.id
order by d.ordering, divname, points desc, runsallowed asc, runsscored desc, gamesplayed desc





    