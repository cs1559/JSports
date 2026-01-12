<?php

declare(strict_types=1);

namespace Tests\Integration;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\MailService;
use FP4P\Component\JSports\Site\Objects\Mock\Mailer;
use Joomla\CMS\Mail\MailerInterface;

final class MailServiceTest extends TestCase
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
    public function testMail(): void
    {

        $mailer = new Mailer();
        
        $subject = "SWIBL - MailService Test";
        $body = "This is the body of the email";
        $recipients = ['cs1559@localhost','cjstrieter@gmail.com'];
        $adminrecipients = ['cs1559@swibl.org'];
        $svc = new MailService($mailer);
        

        // to, subject, body, html mode, cc
        $rc = $svc->sendMail($recipients, $subject, $body, true,$adminrecipients );

        $mlr = $svc->getMailer();
        print_r($mailer->recipients); 
        $this->assertSame($rc, true);
        
    }

    
}
