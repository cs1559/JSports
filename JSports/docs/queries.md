# Identify teams with no contact person

select t.* 
from jos2823_jsports_teams t 
where id IN (select teamid from jos2823_jsports_map where programid = 33)
and t.ownerid < 1
and id not in (select teamid from jos2823_jsports_rosters where userid > 0)

