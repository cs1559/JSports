-- Version 1.2.2 - introduce archived team attribute for FUTURE USE
ALTER TABLE `#__jsports_teams` 
    ADD COLUMN IF NOT EXISTS `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `properties`;