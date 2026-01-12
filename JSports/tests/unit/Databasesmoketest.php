<?php
declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class DatabaseSmokeTest extends TestCase
{
    public function testCanConnectToJoomlaDatabase(): void
    {
        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // Simple query
        $db->setQuery('SELECT 1');
        $result = (int) $db->loadResult();

        $this->assertSame(1, $result);
    }
}
