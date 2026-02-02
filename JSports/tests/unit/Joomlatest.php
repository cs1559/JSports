<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Joomla\CMS\Factory;

final class JoomlaTest extends TestCase
{
    public function testJoomlaApplicationIsBootstrapped(): void
    {
        $app = Factory::getApplication();

        $this->assertNotNull($app);
        $this->assertSame('site', $app->getName());
    }
}
