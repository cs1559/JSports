<?php
namespace FP4P\Component\JSports\Site\Objects\Mock;

use Joomla\CMS\Mail\MailerFactoryAwareTrait;
use Joomla\CMS\Mail\MailerInterface;

class Mailer implements MailerInterface
{
    
    use MailerFactoryAwareTrait;
    
    public $recipients;
    public $bcc;
    public $cc;
    public $sender;
    public $subject;
    public $body;
    
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::addAttachment()
     */
    public function addAttachment(string $data, string $name = '', string $encoding = 'base64', string $type = 'application/octet-stream')
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::addBcc()
     */
    public function addBcc(string $bccEmail, string $name = '')
    {
        $this->bcc[] = [$bccEmail, $name];
        
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::addCc()
     */
    public function addCc(string $ccEmail, string $name = '')
    {
        $this->cc[] = [$ccEmail, $name];

    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::addRecipient()
     */
    public function addRecipient(string $recipientEmail, string $name = '')
    {
      
        $this->recipients[] = [$recipientEmail, $name];
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::addReplyTo()
     */
    public function addReplyTo(string $replyToEmail, string $name = '')
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::send()
     */
    public function send() : bool
    {
        return true;    
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::setBody()
     */
    public function setBody(string $content)
    {
        $this->body = $content;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::setSender()
     */
    public function setSender(string $fromEmail, string $name = ''): void
    {
      $this->sender = $fromEmail;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Mail\MailerInterface::setSubject()
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * This are helper functions for this mock object to support unit testing.
     * @return void
     */
    public function getRecipients() {
        return $this->recipients;
    }
    public function getBcc() {
        return $this->bcc;
    }
    public function getCc() {
        return $this->cc;
    }
    // Implement the respective functions
}