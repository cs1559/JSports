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

