<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Services;

use Joomla\CMS\Factory;
use Joomla\CMS\Mail\Exception\MailDisabledException;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailerInterface;

class MailService
{
    
    protected $mailer;
    
    public function __construct(MailerInterface $mailer = null) {
        
        if (is_null($mailer)) {
//             $this->mailer = Factory::getMailer();
            $this->mailer = Factory::getContainer()
                ->get(MailerFactoryInterface::class)
                ->createMailer();
        } else {
            $this->mailer = $mailer;
        }
        
    }
    
    /**
     * Function to send an email
     *
     * @param   mixed    $recipient    Recipient email address(es)
     * @param   string   $subject      email subject
     * @param   string   $body         Message body
     * @param   boolean  $mode         false = plain text, true = HTML
     * @param   mixed    $cc           CC email address(es)
     * @param   mixed    $bcc          BCC email address(es)
     * @param   mixed    $attachment   Attachment file name(s)
     * @param   mixed    $replyTo      Reply to email address(es)
     * @param   mixed    $replyToName  Reply to name(s)
     *
     * @return  boolean  True on success, false on failure when exception throwing is disabled.
     *
     * @since   1.7.0
     *
     * @throws  MailDisabledException  if the mail function is disabled
     */
     public function sendMail(
        string|array $recipient,
        string $subject,
        $body,
        $mode = false,
        string|array $cc = null,
        string|array $bcc = null,
        $attachment = null,
        $replyTo = null,
        $replyToName = null
        ) 
     {
        
            $params = ComponentHelper::getParams( 'com_jsports' );
            $fromemail = $params->get('fromemail');
            $fromname = $params->get('fromname');
            
            if (strlen($fromemail) <= 1) {
                return false;
            }
            $this->mailer->setSender($fromemail,$fromname);
            
            // ADD RECIPIENT(S)
            if (is_string($recipient)) {
                $this->mailer->addRecipient($recipient,'');
            } else {
                if (is_array($recipient)) {
                    foreach ($recipient as $r) {
                        if (is_string($r)) {
                            $this->mailer->addRecipient($r,'');
                            continue;
                        }
                        if (is_array($r)) {
                            $email = (string) ($r['email'] ?? ($r[0] ?? ''));
                            $rname = (string) ($r['name']  ?? ($r[1] ?? ''));
                            if ($email === '') {
                                throw new \InvalidArgumentException('Recipient email is required.');
                            }
                            $this->mailer->addRecipient($email, $rname);
                            continue;
                        }
                        throw new \InvalidArgumentException('Invalid recipient format.');
                    }
                }
            }
                       
            $this->mailer->setSubject($subject);
                
            if (method_exists($this->mailer, 'isHtml')) {
                $this->mailer->isHtml(true);
            }
            
            $this->mailer->Encoding = 'base64';
            $this->mailer->setBody($body);
            
            // ADD CARBON COPIED RECIPIENT(S)
            if (is_string($cc)) {
                $this->mailer->addCC($cc,'');
            } else {
                if (is_array($cc)) {
                    foreach ($cc as $r) {
                        if (is_string($r)) {
                            $this->mailer->addCC($r,'');
                            continue;
                        }
                        if (is_array($r)) {
                            $email = (string) ($r['email'] ?? ($r[0] ?? ''));
                            $rname = (string) ($r['name']  ?? ($r[1] ?? ''));
                            if ($email === '') {
                                throw new \InvalidArgumentException('Recipient email is required.');
                            }
                            $this->mailer->addCC($email, $rname);
                            continue;
                        }
                        throw new \InvalidArgumentException('Invalid recipient format.');
                    }
                }
            }
            
//             $this->mailer->addCC($cc);
            try {
                $send = $this->mailer->Send();
                if ( $send !== true ) {
                    return false;
                } else {
                    return true;
                }
            } catch (\Exception $e) {
                $logger = Myapp::getLogger();
                $logger->error($e->getMessage());
                return true;
            }
    }
    
    public function getMailer() {
        return $this->mailer;
    }
}

