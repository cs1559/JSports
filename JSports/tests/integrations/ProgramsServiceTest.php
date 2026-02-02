<?php

declare(strict_types=1);

namespace Tests\Integration;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use FP4P\Component\JSports\Site\Services\ProgramsService;

final class ProgramsServiceTest extends TestCase
{
    private DatabaseInterface $db;
    private $debug = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }
    
    /**
     * This test ensures that the ProgramService is still able to retrieve a program record.
     */
    public function testGetItem(): void
    {

        $item = ProgramsService::getItem(35);
        if ($this->debug) {
            fwrite(STDERR, "Name = " . $item->name . PHP_EOL);
        }
//         // If your table can be empty, assert it's an array; otherwise assertNotEmpty
//         $this->assertIsArray($rows);
        
//         // Optional: if you expect at least 1 row in your dev DB
        $this->assertNotEmpty($item);
        
//         if (!empty($rows)) {
//             $this->assertArrayHasKey('id', $rows[0]);
//             $this->assertArrayHasKey('name', $rows[0]);
//         }
    }

    /**
     * this test should ensure that only PUBLISHED (1) and ACTIVE (status = A) programs are returned.
     * NOTE:  If the argument passed isn't true, then it would include both active and pending.
     */
    public function testGetNonCompletedProgramsActiveOnly(): void {
        
        $published = 1;
        $status = 'A';
        
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__jsports_programs'))
            ->where($this->db->quoteName('published') . ' = :published')
            ->where($this->db->quoteName('status') . ' = :status')
            ->bind(':published', $published, \Joomla\Database\ParameterType::INTEGER)
            ->bind(':status',$status,\Joomla\Database\ParameterType::STRING);
    
        $this->db->setQuery($query);
        $rows = $this->db->loadAssocList();
        
        
        if ($this->debug) {
            fwrite(STDERR, $query->__toString() . PHP_EOL);
        }
        if ($this->debug) {
            fwrite(STDERR, "Number of ACTIVE rows: " . count($rows) . PHP_EOL);
        }
        
        $programs = ProgramsService::getNonCompletedPrograms(true);
        if ($this->debug) {
            fwrite(STDERR, "Number returned from service: " . count($programs) . PHP_EOL);
        }
        $this->assertEquals((int) count($rows), (int) count($programs), "ProgramsSerivce::getNonCompletedPrograms(true) FAILED... counts are not equal");
    }

    public function testGetDefaultProgram() : void {
        $this->markTestIncomplete('testGetDefaultProgram - Test not implemented yet');
    }

    public function testGetProgramList() : void {
        $this->markTestIncomplete('testGetProgramList - Test not implemented yet');
    }
    
    public function testGetMostRecentProgram() : void {
        $this->markTestIncomplete('testGetMostRecentProgram - Test not implemented yet');
    }
    
    public function testGetProgramGroups() : void {
        $this->markTestIncomplete('testGetProgramGroups - Test not implemented yet');
    }
    
    public function testCloseProgram() : void {
        $this->markTestIncomplete('testCloseProgram - Test not implemented yet');
    }

    public function testRollbackProgram() : void {
        $this->markTestIncomplete('testRollbackProgram - Test not implemented yet');
    }
    
}
