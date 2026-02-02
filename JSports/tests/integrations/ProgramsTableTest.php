<?php

declare(strict_types=1);

namespace Tests\Integration;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;

final class ProgramsTableTest extends TestCase
{
    private DatabaseInterface $db;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }
    
    public function testProgramsTableExistsAndCanQuery(): void
    {
        // Quick existence check (works across Joomla DB drivers)
        $tables = $this->db->getTableList();
        $prefixedName = $this->db->replacePrefix('#__jsports_programs');
        
        $this->assertContains(
            $prefixedName,
            $tables,
            "Expected table not found: {$prefixedName}"
            );
        
        // Query a few rows
        $query = $this->db->getQuery(true)
        ->select($this->db->quoteName(['id', 'name']))
        ->from($this->db->quoteName('#__jsports_programs'))
        ->order($this->db->quoteName('id') . ' DESC');
        
        $this->db->setQuery($query, 0, 5);
        $rows = $this->db->loadAssocList();
        
        // If your table can be empty, assert it's an array; otherwise assertNotEmpty
        $this->assertIsArray($rows);
        
        // Optional: if you expect at least 1 row in your dev DB
        // $this->assertNotEmpty($rows);
        
        if (!empty($rows)) {
            $this->assertArrayHasKey('id', $rows[0]);
            $this->assertArrayHasKey('name', $rows[0]);
        }
    }
    
    public function testCanLoadSingleProgramByIdIfAnyExist(): void
    {
        // Grab a recent id (if table might be empty, handle gracefully)
        $query = $this->db->getQuery(true)
        ->select($this->db->quoteName('id'))
        ->from($this->db->quoteName('#__jsports_programs'))
        ->order($this->db->quoteName('id') . ' DESC');
        
        $this->db->setQuery($query, 0, 1);
        $id = $this->db->loadResult();
        
        if ($id === null) {
            $this->markTestSkipped('No rows exist in #__jsports_programs');
        }
        
        $idInt = (int) $id;
        
        $query = $this->db->getQuery(true)
        ->select('*')
        ->from($this->db->quoteName('#__jsports_programs'))
        ->where($this->db->quoteName('id') . ' = :id')
        ->bind(':id', $idInt, \Joomla\Database\ParameterType::INTEGER);
        
        $this->db->setQuery($query);
        $row = $this->db->loadAssoc();
        
        echo "# of rows = " . count($row);
        
        $this->assertIsArray($row);
        $this->assertSame((int) $id, (int) $row['id']);
    }
}
