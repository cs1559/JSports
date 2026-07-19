-- ============================================================
-- JSports: Team Badges Schema
-- ------------------------------------------------------------
-- Purpose: support awarding badges/trophies to TEAMS (e.g.
-- "League Tournament Champion", "Runner-Up", "Fair Play Award").
-- Written in MySQL/MariaDB syntax (Joomla's standard DB).
--
-- Assumes existing tables:
--   #__jsports_teams(id int(11) NOT NULL AUTO_INCREMENT PK, ...)
--   #__jsports_programs(id int(11) NOT NULL AUTO_INCREMENT PK, leagueid int(11) NOT NULL, ...)
--     -- "program" is the season-level instance (e.g. "2026 Fall
--     -- Boys Basketball"); it already carries a leagueid, so
--     -- league and season are not separately referenced here.
--     -- NOTE: both existing tables use plain signed int(11) for
--     -- their id columns (no UNSIGNED) — the FK columns below
--     -- (teamid, programid, badgetypeid) match that exactly.
--     -- A signed/unsigned mismatch here is what MySQL errno 150
--     -- ("Foreign key constraint is incorrectly formed") flags.
--
-- Badges are scoped to a PROGRAM (required) — a team's badge is
-- always earned within a specific program. The league that
-- program belongs to can be reached via #__jsports_programs.league_id.
--
-- Table names use this site's resolved Joomla table prefix,
-- "#__".
-- ============================================================
-- ------------------------------------------------------------
-- 1. #__jsports_badge_types
-- Catalog of the badges/trophies that CAN be awarded. Keeping
-- this as a lookup table means new badge types can be added
-- (e.g. "3-Peat Champion") without any schema changes, and the
-- app can drive badge lists/icons off this table.
-- ------------------------------------------------------------
CREATE TABLE #__jsports_badge_types (
    id              int(11) NOT NULL AUTO_INCREMENT,
    code            VARCHAR(50)  NOT NULL,             -- stable machine key, e.g. 'LEAGUE_CHAMPION'
    name            VARCHAR(100) NOT NULL,             -- display name, e.g. 'League Champion'
    description     TEXT,
    icon_url        VARCHAR(255),                      -- badge/trophy image, if displayed in UI
    sort_order      int(11) NOT NULL DEFAULT 0,   -- controls display order
    published       tinyint(4)   NOT NULL DEFAULT 1,
    createdate      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedate      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY idx_jsports_badge_types_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalog of badge/trophy types that can be awarded to a team.';
-- ------------------------------------------------------------
-- 2. #__jsports_team_badges
-- Records an actual badge awarded to a team within a program.
-- ------------------------------------------------------------
CREATE TABLE #__jsports_team_badges (
    id              int(11) NOT NULL AUTO_INCREMENT,
    teamid          int(11) NOT NULL,
    programid       int(11) NOT NULL,
    badgetypeid     int(11) NOT NULL,
    awarded_date    DATE,                              -- date the trophy/badge was actually won
    name            varchar(250),                               -- e.g. "Undefeated 14-0 season"
    alias			varchar(250),
    published		tinyint(4) 	 NOT NULL DEFAULT 1,                          -- e.g. "Undefeated 14-0 season"
    createdate      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedate      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- Prevents the same team from getting the same badge type
    -- twice within the same program.
    UNIQUE KEY uq_team_badge_context (teamid, programid, badgetypeid),
    KEY idx_jsports_team_badges_teamid (teamid),
    KEY idx_jsports_team_badges_programid (programid),
    KEY idx_jsports_team_badges_badgetypeid (badgetypeid),
    CONSTRAINT fk_jsports_team_badges_team
        FOREIGN KEY (teamid) REFERENCES #__jsports_teams(id) ON DELETE CASCADE,
    CONSTRAINT fk_jsports_team_badges_program
        FOREIGN KEY (programid) REFERENCES #__jsports_programs(id) ON DELETE CASCADE,
    CONSTRAINT fk_jsports_team_badges_badge_type
        FOREIGN KEY (badgetypeid) REFERENCES #__jsports_badge_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Badges/trophies awarded to a team within a program.';
-- ------------------------------------------------------------
-- Seed data: starter badge types
-- ------------------------------------------------------------
INSERT INTO #__jsports_badge_types (code, name, description, sort_order) VALUES
    ('TOURNAMENT_CHAMPION',     'Tournament Champion',      'Awarded to the team that wins the league tournament.',            1),
    ('LEAGUE_CHAMPION',         'League Champion',          'Awarded to the team that wins the regular season.',                2);
