-- Sponsors Table -- THE WHO
CREATE TABLE IF NOT EXISTS `#__jsports_sponsors` (
	 `id` 			int(11) NOT NULL AUTO_INCREMENT,
	 `name` 		varchar(50) NOT NULL DEFAULT '',
 	 `alias` 		varchar(100) NOT NULL DEFAULT '',	
  	 `email` 		varchar(100) DEFAULT NULL,
	 `contactname` 	varchar(100) DEFAULT NULL,
 	 `contactemail` varchar(100) DEFAULT NULL,
  	 `contactphone` varchar(12) DEFAULT NULL,
 	 `website` 		varchar(150) DEFAULT NULL,
 	 `description` 	text DEFAULT NULL,
 	 `logo` 		varchar(100) DEFAULT NULL,
	 `notes` 		text DEFAULT NULL,
 	 `ordering`		tinyint default 0,
	 `published` 	tinyint default 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sponsorships Table -- THE WHAT THEY PAID FOR
--  PLAN LEVEL = Default (D), Premium (P), Gold (G), Silver (S), Bronze (B)
--  PLAN TYPE = League (L), Program/Season (P), Division (D), Team (T)
--
CREATE TABLE IF NOT EXISTS `#__jsports_sponsorships` (
	 `id` 				int(11) 		NOT NULL AUTO_INCREMENT,
 	 `sponsorid` 		int(11)			NOT NULL,
   	 `planlevel` 		varchar(1) 		NOT NULL DEFAULT 'D',
   	 `plantype` 		varchar(1) 		NOT NULL DEFAULT 'L',
  	 `programid` 		int(11) 		NOT NULL DEFAULT 0,
	 `startdate` 		DATE 			DEFAULT NULL,
 	 `enddate`   		date 			DEFAULT NULL,
 	 `campaign_limit`	int				DEFAULT 0,
	 `published` 		tinyint 		default 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- SPONSOR ASSET Table
--  TYPE = Logo (L), Banner (B), 
--  PLAN TYPE = League (L), Program/Season (P), Division (D), Team (T)
--
CREATE TABLE IF NOT EXISTS `#__jsports_sponsor_assets` (
	 `id` 				tinyint 		NOT NULL AUTO_INCREMENT,
 	 `sponsorid` 		tinyint default 0,
 	 `title`  			VARCHAR(50),
 	 `description` 		VARCHAR(255),	
 	 `assettype` 		VARCHAR(1),
 	 `filename`  		VARCHAR(255),
 	 `filesize`			int,
  	 `mimetype`  		VARCHAR(100),
	 `height` 			int,
 	 `width`   			int,
 	 `createdate` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	 `updatedate` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 	 `published` tinyint(4) NOT NULL DEFAULT 0,
PRIMARY KEY (`id`),
CONSTRAINT UC_filename UNIQUE (sponsorid,filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- CAMPAIGN Table
--  Campaign is what actually generates the ad/banner on the site.  It ties the sponsor and particular asset together and will be used to render the asset/content
--  in a specific position on the website.
--
--  CAMPAIGN TYPE - could be a banner, ad, link to asset, external link, etc.
--
CREATE TABLE `#__jsports_campaigns` (
	 `id` 		tinyint 		NOT NULL AUTO_INCREMENT,
 	 `campaigntype` 	VARCHAR(1),
 	 `name`				VARCHAR(30),	
 	 `sponsorid` 		tinyint default 0,
 	 `sponsorshipid`	tinyint default 0,
 	 `assetid` 			tinyint default 0,
  	 `positions` 		VARCHAR(150),
   	 `url` 				VARCHAR(150),		
 	 `content` 			text,		
  	 `impressions` 		int default 0,
   	 `clicks`	 		int default 0,
	 `startdate` 		DATE 			NOT NULL,
 	 `enddate`   		date 			NOT NULL,
 	 `classname`		varchar(100)  default null,
 	 `customcss`		varchar(500)  default '';
 	 `published`		tinyint default 0,
PRIMARY KEY (`campaignid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `#__jsports_campaign_positions` (
	`id` 			tinyint 		NOT NULL AUTO_INCREMENT,
	`title` 		VARCHAR(50),
 	`position` 		VARCHAR(25),	
 	`maxwidth` 		INT NULL,
	`maxheight`		INT NULL,
	`css_class` 	VARCHAR(50) NULL,
	`rotation` 		VARCHAR(50) NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


insert into #__jsports_campaign_positions values(null,'Joomla Module','module',0,0,'','');
insert into #__jsports_campaign_positions values(null,'Standings (TOP)','standings-top',0,0,'','');
insert into #__jsports_campaign_positions values(null,'Profile pages','profile-',0,0,'','');