-- ------------------------------------------------------------------------------------
--  LOG table - JSports uses this table to log certain action events by users.
-- ------------------------------------------------------------------------------------
CREATE TABLE `#__jsports_action_logs` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `logdate` datetime NOT NULL,
	 `userid` varchar(30) NOT NULL,
	 `username` varchar(30) NOT NULL,
	 `msg` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
--  Divisions Table - Divisions all exist within the context of a program.
--  NOTE: There is a 1:n relationship between a program and divisions. 
-- ------------------------------------------------------------------------------------
CREATE TABLE `#__jsports_divisions` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `name` varchar(50) NOT NULL DEFAULT '',
	 `alias` varchar(60) DEFAULT NULL,
	 `programid` int(11) NOT NULL DEFAULT 0,
	 `ordering` decimal(11,2) NOT NULL DEFAULT 0.00,
	 `crossdivisional` tinyint(2) NOT NULL DEFAULT 0,
	 `published` tinyint(4) NOT NULL DEFAULT 0,
	 `agegroup` int(11) DEFAULT NULL,
	 `notes` text DEFAULT NULL,
 PRIMARY KEY (`id`),
	 KEY `division_season_idx` (`programid`),
	 KEY `program_idx` (`programid`,`ordering`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
--  Games Table - this is where all game information is stored.
-- ------------------------------------------------------------------------------------
CREATE TABLE `#__jsports_games` (
	 `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Game ID',
	 `name` varchar(100) DEFAULT NULL,
	 `divisionid` int(11) NOT NULL DEFAULT 0 COMMENT 'Division Id',
	 `programid` int(11) NOT NULL DEFAULT 0 COMMENT 'Season the game was played in',
	 `teamid` int(11) NOT NULL,
	 `opponentid` int(11) DEFAULT NULL,
	 `homeindicator` tinyint(4) NOT NULL DEFAULT 0,
	 `gamedate` date DEFAULT NULL COMMENT 'Date of game',
	 `hometeamid` int(11) NOT NULL DEFAULT 0 COMMENT 'ID of the Home Team.  Only if team was in league',
	 `awayteamid` int(11) NOT NULL DEFAULT 0 COMMENT 'Away Team Id.  Only if team was in league',
	 `hometeamscore` int(11) NOT NULL DEFAULT 0 COMMENT 'Home team score',
	 `awayteamscore` int(11) NOT NULL DEFAULT 0 COMMENT 'Away team score',
	 `hometeampoints` int(11) NOT NULL DEFAULT 0 COMMENT 'Not Used',
	 `awayteampoints` int(11) NOT NULL DEFAULT 0 COMMENT 'Not Used',
	 `forfeit` char(1) DEFAULT NULL COMMENT 'Forfeit indicator',
	 `leaguegame` tinyint(2) DEFAULT NULL COMMENT 'Indicates if it is a league game',
	 `hometeamname` varchar(50) DEFAULT NULL COMMENT 'Home team name',
	 `awayteamname` varchar(50) DEFAULT NULL COMMENT 'Away team name',
	 `location` varchar(100) DEFAULT NULL COMMENT 'Location of the game',
	 `gamestatus` char(1) DEFAULT NULL COMMENT 'Status of the game',
	 `gametime` varchar(15) DEFAULT NULL COMMENT 'game time',
	 `enteredby` varchar(30) DEFAULT NULL COMMENT 'User who entered the score',
	 `updatedby` varchar(30) DEFAULT NULL COMMENT 'User who last updated the score',
	 `dateupdated` datetime DEFAULT NULL COMMENT 'date record was last updated',
	 `published` tinyint(4) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
	 KEY `division_id` (`divisionid`),
	 KEY `season` (`programid`),
	 KEY `scores_idx1` (`programid`,`leaguegame`,`gamestatus`),
	 KEY `xconfseason` (`leaguegame`,`programid`),
	 KEY `scores_idx4` (`programid`,`leaguegame`),
	 KEY `xstatusxseason` (`gamestatus`,`programid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------
CREATE TABLE `#__jsports_groups` (
	 `code` varchar(4) NOT NULL,
	 `name` varchar(30) NOT NULL,
	 `published` tinyint(3) DEFAULT 0,
 PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_groups_items` (
	 `groupid` int(11) NOT NULL AUTO_INCREMENT,
	 `groupcode` varchar(4) NOT NULL,
	 `code` varchar(4) NOT NULL,
	 `name` varchar(30) NOT NULL,
 PRIMARY KEY (`groupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
-- League table.  This table is the highest "node" in the main data hierarchy.
-- NOTE:  There is a 1:n relationship between a LEAGUE and a PROGRAM
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_leagues` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `name` varchar(100) NOT NULL DEFAULT '',
	 `abbr` varchar(10) DEFAULT NULL,
	 `description` text NOT NULL,
	 `published` tinyint(4) NOT NULL DEFAULT 0,
	 `configuration` text DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
-- Map Table - This table ties everything (program,division, teams, etc.) together
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_map` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `programid` int(11) NOT NULL DEFAULT 0,
	 `teamid` int(11) NOT NULL DEFAULT 0,
	 `divisionid` int(11) NOT NULL DEFAULT 0,
	 `regid` int(11) NOT NULL,
	 `published` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
	 KEY `divmap_season_idx` (`programid`,`divisionid`),
	 KEY `divmap_season_idx2` (`programid`),
	 KEY `divmap_season_idx3` (`teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
-- Past Standings - holds past standings information.  when a program is closed, the
-- standings are placed in this table for future reference.
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_past_standings` (
	 `id` int(9) NOT NULL,
	 `programid` int(9) NOT NULL,
	 `divisionid` int(9) NOT NULL,
	 `position` int(9) NOT NULL,
	 `teamid` int(9) NOT NULL,
	 `teamname` varchar(50) NOT NULL,
	 `headcoach` varchar(50) NOT NULL,
	 `wins` int(5) NOT NULL,
	 `losses` int(5) NOT NULL,
	 `ties` int(5) NOT NULL,
	 `gamesplayed` int(11) NOT NULL DEFAULT 0,
	 `points` int(5) NOT NULL,
	 `gamesback` decimal(5,2) NOT NULL,
	 `winpct` float(7,5) NOT NULL DEFAULT 0.00000,
	 `runsscored` int(11) NOT NULL,
	 `runsallowed` int(11) NOT NULL,
	 KEY `standings_idx1` (`programid`,`divisionid`) USING BTREE,
	 KEY `primary_idx` (`programid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
-- Programs - this table stores all data related to a program/season.
-- NOTE:  There is a 1:n relationship between League and a Programs.
-- NOTE:  THere is a 1:n relationship between a Program and Divisions.
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_programs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `leagueid` int(11) NOT NULL,
 `name` varchar(100) NOT NULL DEFAULT '',
 `description` mediumtext DEFAULT NULL,
 `cost` int(11) NOT NULL DEFAULT 0,
 `alias` varchar(50) NOT NULL,
 `sportcode` varchar(1) NOT NULL,
 `groupingscode` varchar(4) NOT NULL,
 `programstart` datetime DEFAULT NULL,
 `programend` datetime DEFAULT NULL,
 `active` tinyint(1) DEFAULT 0,
 `registrationopen` tinyint(4) DEFAULT NULL,
 `agreementurl` varchar(100) DEFAULT NULL,
 `status` varchar(1) NOT NULL,
 `registrationonly` tinyint(4) DEFAULT NULL,
 `registrationtemplate` varchar(40) DEFAULT NULL,
 `registrationstart` datetime DEFAULT NULL,
 `registrationend` datetime DEFAULT NULL,
 `registrationnotes` mediumtext DEFAULT NULL,
 `publishstandings` tinyint(4) NOT NULL,
 `setupfinal` tinyint(4) NOT NULL,
 `published` tinyint(4) NOT NULL,
 `properties` mediumtext DEFAULT NULL,
 `rostersenabled` tinyint(4) DEFAULT NULL,
 `rosterslocked` tinyint(4) NOT NULL,
 `limitroster` tinyint(4) NOT NULL DEFAULT 0,
 `rostersize` int(11) NOT NULL,
 `includesubstitutes` int(11) NOT NULL DEFAULT 0,
 `standingspolicy` varchar(6) NOT NULL,
 `registrationoptions` text NOT NULL,
 PRIMARY KEY (`id`),
 KEY `season_idx2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
-- Record History - stores an individual teams record per program
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_recordhistory` (
 `teamid` int(11) NOT NULL DEFAULT 0,
 `programid` int(11) NOT NULL DEFAULT 0,
 `programname` varchar(100) NOT NULL DEFAULT '',
 `divisionid` int(11) NOT NULL DEFAULT 0,
 `divisionname` varchar(50) NOT NULL DEFAULT '',
 `teamname` varchar(50) NOT NULL DEFAULT '',
 `runsscored` decimal(54,0) DEFAULT NULL,
 `runsallowed` decimal(54,0) DEFAULT NULL,
 `wins` decimal(45,0) DEFAULT NULL,
 `losses` decimal(45,0) DEFAULT NULL,
 `ties` decimal(45,0) DEFAULT NULL,
 `points` decimal(48,0) DEFAULT NULL,
 PRIMARY KEY (`teamid`,`programid`,`divisionid`),
 KEY `record_history_idx` (`programid`,`teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------------
--  Registrations - This table stores all of the regisration data.
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_registrations` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `divisionid` int(11) NOT NULL DEFAULT 0,
	 `programid` int(11) NOT NULL DEFAULT 0,
	 `teamid` int(11) NOT NULL DEFAULT 0,
	 `name` varchar(50) NOT NULL,
	 `address` varchar(50) NOT NULL,
	 `city` varchar(25) NOT NULL,
	 `state` varchar(2) NOT NULL,
	 `email` varchar(100) NOT NULL,
	 `phone` varchar(15) NOT NULL,
	 `cellphone` varchar(15) DEFAULT NULL,
	 `teamname` varchar(100) NOT NULL,
	 `agegroup` int(11) DEFAULT NULL,
	 `grouping` varchar(10) DEFAULT NULL,
	 `existingteam` tinyint(10) DEFAULT NULL,
	 `published` tinyint(4) NOT NULL DEFAULT 0,
	 `paid` tinyint(4) DEFAULT NULL,
	 `confnum` varchar(20) DEFAULT NULL,
	 `confirmed` tinyint(4) NOT NULL DEFAULT 0,
	 `playoffs` int(11) NOT NULL DEFAULT 0,
	 `allstarevent` int(11) NOT NULL DEFAULT 0,
	 `regdate` datetime DEFAULT current_timestamp(),
	 `registeredby` varchar(40) DEFAULT NULL,
	 `skilllevel` varchar(1) DEFAULT NULL,
	 `requestedclass` varchar(8) DEFAULT NULL,
	 `tosack` int(11) NOT NULL DEFAULT 0,
	 `ipaddr` varchar(20) DEFAULT NULL,
 PRIMARY KEY (`id`),
	 KEY `divmap_season_idx` (`programid`,`divisionid`),
	 KEY `divmap_season_idx2` (`programid`,`paid`),
	 KEY `divmap_season_idx3` (`teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
--  Rosters - This table holds all roster information for a specific team/program.
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_rosters` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `programid` int(11) NOT NULL,
	 `teamid` int(11) NOT NULL,
	 `firstname` varchar(25) NOT NULL,
	 `lastname` varchar(35) NOT NULL,
	 `playernumber` varchar(2) DEFAULT NULL,
	 `substitute` int(11) NOT NULL DEFAULT 0,
	 `classification` varchar(1) NOT NULL,
	 `role` varchar(20) DEFAULT NULL,
	 `userid` int(11) NOT NULL DEFAULT 0,
	 `staffadmin` tinyint(4) NOT NULL DEFAULT 0,
	 `email` varchar(150) DEFAULT NULL,
	 `published` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 	KEY `simple_roster_idx1` (`teamid`,`programid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
-- Tames - This table stores information for each team within the league.
-- NOTE:  There is a 1:n relationship between DIVISIONS and TEAMS
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_teams` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `name` varchar(50) NOT NULL DEFAULT '',
	 `alias` varchar(50) DEFAULT NULL,
	 `websiteurl` varchar(100) DEFAULT NULL,
	 `logo` varchar(75) DEFAULT NULL,
	 `thumbnail` varchar(75) DEFAULT NULL,
	 `active` char(1) DEFAULT NULL,
	 `city` varchar(30) DEFAULT NULL,
	 `state` varchar(3) DEFAULT NULL,
	 `contactname` varchar(50) DEFAULT NULL,
	 `contactemail` varchar(100) DEFAULT NULL,
	 `contactphone` varchar(12) DEFAULT NULL,
	 `ownerid` int(11) DEFAULT 0,
	 `hits` int(11) DEFAULT 0,
	 `dateupdated` timestamp NULL DEFAULT NULL,
	 `updatedby` varchar(30) DEFAULT NULL,
	 `properties` text DEFAULT NULL,
	 `published` tinyint(4) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `owner_teamid` (`ownerid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
--  Vanues - this table holds common locaations where games are played.
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_venues` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `name` varchar(50) NOT NULL,
	 `alias` varchar(50) NOT NULL,
	 `latitude` varchar(30) NOT NULL,
	 `longitude` varchar(30) NOT NULL,
	 `notes` text NOT NULL,
	 `published` tinyint(1) DEFAULT NULL,
	 `properties` text DEFAULT NULL,
	 `address1` varchar(30) DEFAULT NULL,
	 `address2` varchar(30) DEFAULT NULL,
	 `city` varchar(20) DEFAULT NULL,
	 `state` varchar(10) DEFAULT NULL,
	 `country` varchar(30) DEFAULT NULL,
	 `phone` varchar(20) DEFAULT NULL,
	 `zipcode` varchar(20) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------------------------------
-- Record History - stores an individual teams record per program
-- ------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__jsports_teamprofile_audit` (
 `teamid` int(11) NOT NULL DEFAULT 0,
 `programid` int(11) NOT NULL DEFAULT 0,
 `auditdate` datetime DEFAULT NULL,
 `status` int(11) NOT NULL DEFAULT 0,
 `messages` text DEFAULT NULL,
PRIMARY KEY (`teamid`, 'programid')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- version 1.0.31 
alter table `#__jsports_teams` add `tournament` tinyint default 0 after ownerid;
alter table `#__jsports_teams` add `showcontactinfo` tinyint default 0 after tournament;
alter table `#__jsports_teams` addd `openroster` tinyint  default 0 after show_contact_info;
-- version 1.1.0
alter table `#__jsports_divisions` add `leaguemanaged` tinyint default 0 after agegroup;

-- Version 1.1.1
CREATE TABLE IF NOT EXISTS `#__jsports_bulletins` (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	 `ownerid` int(11) NOT NULL DEFAULT 0,
	 `teamid` int(11) DEFAULT 0,
	 `category` varchar(1) DEFAULT 'B',
	 `title` varchar(50) DEFAULT 'B',	
	 `approved` tinyint DEFAULT 0,
	 `content` text DEFAULT null,
	 `startdate` date DEFAULT NULL COMMENT 'Start Date',
	 `enddate` date DEFAULT NULL COMMENT 'End Date',
	 `location` varchar(100) DEFAULT null,
	 `externalurl` varchar(100) DEFAULT null,
	 `attachment` varchar(100) DEFAULT null,
	 `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	 `updatedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	 `updatedby` varchar(25) DEFAULT null,
	 `published` tinyint(4) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
    KEY `bulletins_ownerid_idx` (`ownerid`, `createdate`),
    KEY `bulletins_teamid_idx` (`teamid`, `createdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- BULLETIN TYPES
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS #__jsports_bulletin_categories (
	 category varchar(1) NOT NULL,
	 catdesc varchar(30) not null,
	 publicaccess tinyint default 0, 
 PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into #__jsports_bulletin_categories values('G','General',0);
insert into #__jsports_bulletin_categories values('T','Tournament',0);
insert into #__jsports_bulletin_categories values('Y','Tryout',1);
insert into #__jsports_bulletin_categories values('F','Fundraising',0);
insert into #__jsports_bulletin_categories values('S','Sponsors',0);

-- Version 1.2.2 - introduce archived team attribute for FUTURE USE
ALTER TABLE `#__jsports_teams` 
    ADD COLUMN IF NOT EXISTS `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `properties`;