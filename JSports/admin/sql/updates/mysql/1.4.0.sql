-- Sponsors Table -- THE WHO
CREATE TABLE IF NOT EXISTS `#__jsports_sponsors` (
	 `id` 			int(11) NOT NULL AUTO_INCREMENT,
	 `name` 		varchar(50) NOT NULL DEFAULT '',
 	 `alias` 		varchar(100) NOT NULL DEFAULT '',	
  	 `email` 		varchar(100) DEFAULT NULL,
	 `contactname` 	varchar(100) DEFAULT NULL,
 	 `contactemail` varchar(100) DEFAULT NULL,
  	 `contactphone` varchar(12) DEFAULT NULL,
 	 `website` 		varchar(100) DEFAULT NULL,
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
	 `id` 			int(11) 		NOT NULL AUTO_INCREMENT,
 	 `sponsorid` 	int(11)			NOT NULL,
   	 `planlevel` 	varchar(1) 		NOT NULL DEFAULT 'D',
   	 `plantype` 	varchar(1) 		NOT NULL DEFAULT 'L',
  	 `programid` 	int(11) 		NOT NULL DEFAULT 0,
	 `startdate` 	DATE 			DEFAULT NULL,
 	 `enddate`   	date 			DEFAULT NULL,
	 `published` 	tinyint 		default 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- SPONSOR ASSET Table
--  TYPE = Logo (L), Banner (B), 
--  PLAN TYPE = League (L), Program/Season (P), Division (D), Team (T)
--
CREATE TABLE IF NOT EXISTS `#__jsports_sponsor_assets` (
	 `id` 				tinyint 		NOT NULL AUTO_INCREMENT,
 	 `sponsorid` 		tinyint,
 	 `title`  			VARCHAR(30),
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
--  PLAN LEVEL = Default (D), Premium (P), Gold (G), Silver (S), Bronze (B)
--  PLAN TYPE = League (L), Program/Season (P), Division (D), Team (T)
--
CREATE TABLE `#__jsports_sponsor_campaign` (
	 `campaignid` 		tinyint 		NOT NULL AUTO_INCREMENT,
 	 `sponsorshipid` 	tinyint,
 	 `assetid` 			tinyint,
  	 `impressions` 		int default 0,
   	 `clicks`	 		int default 0,
	 `startdate` 		DATE 			NOT NULL,
 	 `enddate`   		date 			NOT NULL,
PRIMARY KEY (`campaignid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
