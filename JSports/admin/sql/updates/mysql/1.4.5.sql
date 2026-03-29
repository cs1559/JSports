-- Add a LAYOUTS column in favor of campaign type
ALTER TABLE `#__jsports_campaigns` ADD `layout` 	VARCHAR(25) DEFAULT NULL AFTER `sponsorid`;
ALTER TABLE `#__jsports_campaigns` ADD `link` 		VARCHAR(1) 	DEFAULT 'S' AFTER `layout`;
ALTER TABLE `#__jsports_campaigns` ADD `imageid` 	TINYINT 	DEFAULT 0 AFTER `link`;