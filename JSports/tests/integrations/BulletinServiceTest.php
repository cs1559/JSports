<?php

declare(strict_types=1);

namespace Tests\Integration;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\BulletinService;

final class BulletinServiceTest extends TestCase
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
    public function testGetBulletinFilepath(): void
    {

        $path = BulletinService::getBulletinFilePath(88);
        if ($this->debug) {
            fwrite(STDERR, "Path = " . $path . PHP_EOL);
        }
        $this->assertEquals($path, 'C\xampp\htdocs\j4/media/com_jsports/attachments/Bulletin-88/');
        

    }

    
}
