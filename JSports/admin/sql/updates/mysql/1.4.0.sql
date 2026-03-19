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
   	 `plancode` 		varchar(1) 		NOT NULL DEFAULT 'D',
   	 `plantype` 		varchar(1) 		NOT NULL DEFAULT 'L',
  	 `programid` 		int(11) 		NOT NULL DEFAULT 0,
	 `startdate` 		DATE 			DEFAULT NULL,
 	 `enddate`   		date 			DEFAULT NULL,
	 `impressions` 		int default 0,
   	 `clicks`	 		int default 0,
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
CREATE TABLE IF NOT EXISTS `#__jsports_campaigns` (
	 `id` 		tinyint 		NOT NULL AUTO_INCREMENT,
 	 `campaigntype` 	VARCHAR(2),
 	 `title`			VARCHAR(50),	
 	 `sponsorid` 		tinyint default 0,
 	 `sponsorshipid`	tinyint default 0,
 	 `assetid` 			tinyint default 0,
  	 `positions` 		VARCHAR(150),
   	 `url` 				VARCHAR(200),		
 	 `content` 			text,		
  	 `impressions` 		int default 0,
   	 `clicks`	 		int default 0,
	 `startdate` 		DATE 			NOT NULL,
 	 `enddate`   		date 			NOT NULL,
 	 `classname`		varchar(100)  default null,
 	 `customcss`		varchar(500)  default '',
 	 `published`		tinyint default 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__jsports_sponsorship_plans` (
 `id` 		tinyint 		NOT NULL AUTO_INCREMENT,	
	`plancode` 		VARCHAR(1) 		NOT NULL,
	`name` 			VARCHAR(20),
	`bolton`		tinyint default 0,
	`ordering`		tinyint default 0,
 	`entitlements`	TEXT,	
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'P',
    'Platinum',0,5,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);
INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'G',
    'Gold',0,4,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);
INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'S',
    'Silver',0,3,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);
INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'B',
    'Bronze',0,2,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);
INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'C',
    'Compliment',0,1,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);
INSERT INTO `#__jsports_sponsorship_plans` (plancode, name, bolton, ordering, entitlements)
VALUES (
    'X',
    'Bold-on 5',1,0,
    '{"max_campaigns":5,"positions":["standings-top","standings-bottom","venues-top"],"bulletins":true}'
);