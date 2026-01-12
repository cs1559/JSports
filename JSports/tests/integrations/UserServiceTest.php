<?php

declare(strict_types=1);

namespace Tests\Integration;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Objects\Mock\GuestUser;
use FP4P\Component\JSports\Site\Objects\Mock\AdminUser;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Objects\Mock\CoachUser;

final class UserServiceTest extends TestCase
{
    private DatabaseInterface $db;
    private $debug = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }
    
//     /**
//      * This test ensures that the ProgramService is still able to retrieve a program record.
//      */
//     public function testGetUserTeams(): void
//     {

//         $item = ProgramsService::getItem(35);
//         if ($this->debug) {
//             fwrite(STDERR, "Name = " . $item->name . PHP_EOL);
//         }
// //         // If your table can be empty, assert it's an array; otherwise assertNotEmpty
// //         $this->assertIsArray($rows);
        
// //         // Optional: if you expect at least 1 row in your dev DB
//         $this->assertNotEmpty($item);
        
// //         if (!empty($rows)) {
// //             $this->assertArrayHasKey('id', $rows[0]);
// //             $this->assertArrayHasKey('name', $rows[0]);
// //         }
//     }

//     /**
//      * this test should ensure that only PUBLISHED (1) and ACTIVE (status = A) programs are returned.
//      * NOTE:  If the argument passed isn't true, then it would include both active and pending.
//      */
    public function testGetUserTeamIds(): void {
        
        $user = new CoachUser();
     
        $teams = UserService::getUserTeams($user);
        if ($this->debug) {
            fwrite(STDERR, "Number returned from service: " . count($teams) . PHP_EOL);
        }
        $this->assertEquals((int) count($teams), 4, "UserService::getUserTeams() FAILED... counts are not equal");
    }

    public function testIsGuest() : void {
        // Test for Admin User
        $user = new AdminUser();
        $rc = UserService::isGuest($user);
        if ($this->debug) {
            fwrite(STDERR, "Return = " . $rc . PHP_EOL);
        }
        $this->assertSame(0,$rc, 'Admin user is evaluating as a guest');
        
        // Test for guest
        $user = new GuestUser();
        $rc = UserService::isGuest($user);
        if ($this->debug) {
            fwrite(STDERR, "Return = " . $rc . PHP_EOL);
        }
        $this->assertSame(1,$rc, 'Guest user not showing as a guest');

// //         $user = new GuestUser();
        $rc = UserService::isGuest();
        $this->assertSame(1,$rc, 'No argument passed - Guest user not showing as a guest');
    }

//     public function testGetAssignedAgeGroups() : void {
//         $this->markTestIncomplete('testGetAssignedAgeGroups - Test not implemented yet');
//     }
    
    public function testIsTeamAdmin() : void {
        
        // Coach User id is 640
        // Team Ids = 1115 or 1032
        // Program ID = 33
        
        $teamid = 1115;
        $programid = 33;
        
        $user = new CoachUser();
        $rc = UserService::isTeamAdmin($teamid, $programid, $user);
        if ($this->debug) {
            fwrite(STDERR, "Return = " . $rc . PHP_EOL);
        }
        $this->assertTrue($rc > 0, 'user is a team admin');
//         $this->markTestIncomplete('testIsTeamAdmin - Test not implemented yet');
    }
    
//     public function testGetAssignedAgeGroups() : void {
//         $this->markTestIncomplete('testGetAssignedAgeGroups - Test not implemented yet');
//     }
        
}
