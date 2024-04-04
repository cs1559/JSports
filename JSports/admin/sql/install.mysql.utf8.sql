CREATE TABLE IF NOT EXISTS `#__jsports_groups` (
    `code` VARCHAR(4) NOT NULL,
    `name` VARCHAR(30) NOT NULL,
    `published` TINYINT(3) DEFAULT 0,
    PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsports_groups_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `groupcode` VARCHAR(4) NOT NULL,
    `code` VARCHAR(4) NOT NULL,
    `name` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsports_action_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `logdate` datetime NOT NULL,
    `userid` VARCHAR(30) NOT NULL,
    `username` VARCHAR(30) NOT NULL,
    `msg` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

