CREATE TABLE IF NOT EXISTS `#__jsports_leagues` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100),
    `abbr` VARCHAR(10),
    `description` MEDIUMTEXT,
    `published` TINYINT(3),
    `configuration` MEDIUMTEXT,
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4
    DEFAULT COLLATE=utf8mb4_unicode_ci;