<?php
namespace FP4P\Component\JSports\Site\Objects\Mock;

use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailerInterface;
use Joomla\Registry\Registry;

class MailFactory implements MailerFactoryInterface

{

    public function createMailer(Registry $configuration = null): MailerInterface
    {
        return new Mailer($configuration);
    }

}

