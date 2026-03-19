-- Add address fields for the sponsor
ALTER TABLE `#__jsports_sponsors` ADD `address1` 	VARCHAR(35) DEFAULT NULL AFTER `name`;
ALTER TABLE `#__jsports_sponsors` ADD `address2` 	VARCHAR(35) DEFAULT NULL AFTER `address1`;
ALTER TABLE `#__jsports_sponsors` ADD `city` 		VARCHAR(25) DEFAULT NULL AFTER `address2`;
ALTER TABLE `#__jsports_sponsors` ADD `state` 		VARCHAR(2) 	DEFAULT NULL AFTER `city`;
ALTER TABLE `#__jsports_sponsors` ADD `zipcode` 	VARCHAR(10) DEFAULT NULL AFTER `state`;
ALTER TABLE `#__jsports_sponsors` ADD `slogan` 		VARCHAR(50) DEFAULT NULL AFTER `logo`;
