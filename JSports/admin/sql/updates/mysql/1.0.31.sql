alter table `#__jsports_teams` add `tournament` tinyint default 0 after ownerid;
alter table `#__jsports_teams` add `showcontactinfo` tinyint default 0 after tournament;
alter table `#__jsports_teams` addd `openroster` tinyint  default 0 after show_contact_info;