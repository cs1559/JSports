<?php

/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace FP4P\Component\JSports\Site\Objects\Standings;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

defined('_JEXEC') or die;

/**
 * Persists a calculated set of TeamRecord standings into
 * #__jsports_standings.
 *
 * Column mapping (TeamRecord -> table):
 *   id          -> teamid
 *   name        -> teamname
 *   coachName   -> headcoach
 *   position    -> position
 *   wins        -> wins
 *   losses      -> losses
 *   ties        -> ties
 *   gamesPlayed -> gamesplayed
 *   points      -> points
 *   gamesBack   -> gamesback
 *   winPct      -> winpct
 *   runsScored  -> runsscored
 *   runsAllowed -> runsallowed
 *
 * The table has no unique key on (programid, divisionid, teamid), so
 * this assumes the table is meant to be fully rebuilt per
 * program/division each time standings are recalculated: existing
 * rows for that programid/divisionid are deleted first, then the
 * fresh set is inserted. If you'd rather upsert individual team rows
 * instead, add a unique index on (programid, divisionid, teamid) and
 * let me know -- the insert logic would need to change to
 * ON DUPLICATE KEY UPDATE.
 */
final class TempStandingsWriter
{
    private DatabaseDriver $db;

    public function __construct(?DatabaseDriver $db = null)
    {
        $this->db = $db ?? Factory::getContainer()->get('DatabaseDriver');
    }

    /**
     * @param TeamRecord[] $standings
     */
    public function save(int $programId, int $divisionId, array $standings): void
    {
        $this->deleteExisting($programId, $divisionId);

        foreach ($standings as $team) {
            $this->insertOne($programId, $divisionId, $team);
        }
    }

    private function deleteExisting(int $programId, int $divisionId): void
    {
        $query = $this->db->getQuery(true)
            ->delete($this->db->quoteName('#__jsports_standings'))
            ->where($this->db->quoteName('programid') . ' = :programid')
            ->where($this->db->quoteName('divisionid') . ' = :divisionid')
            ->bind(':programid', $programId, ParameterType::INTEGER)
            ->bind(':divisionid', $divisionId, ParameterType::INTEGER);

        $this->db->setQuery($query)->execute();
    }

    private function insertOne(int $programId, int $divisionId, TeamRecord $team): void
    {
        $row = (object) [
            'programid'    => $programId,
            'divisionid'   => $divisionId,
            'position'     => $team->position,
            'teamid'       => (int) $team->id,
            'teamname'     => $team->name,
            'headcoach'    => $team->coachName,
            'wins'         => $team->wins,
            'losses'       => $team->losses,
            'ties'         => $team->ties,
            'gamesplayed'  => $team->gamesPlayed,
            'points'       => $team->points,
            'gamesback'    => $team->gamesBack,
            'winpct'       => $team->winPct,
            'runsscored'   => $team->runsScored,
            'runsallowed'  => $team->runsAllowed,
        ];

        $this->db->insertObject('#__jsports_standings', $row);
    }
}
