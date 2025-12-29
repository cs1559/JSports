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

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\Exception\MailDisabledException;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

class MailService
{
    
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
        $recipient,
        $subject,
        $body,
        $mode = false,
        $cc = null,
        $bcc = null,
        $attachment = null,
        $replyTo = null,
        $replyToName = null
        ) 
     {
        
            $params = ComponentHelper::getParams( 'com_jsports' );
            $fromemail = $params->get('fromemail');
            $fromname = $params->get('fromname');
            
//             $fromemail = 'info@swibl.org';
//             $fromname = 'SWIBL';
            if (strlen($fromemail) <= 1) {
                return false;
            }
         
            $mailer = Factory::getMailer();
            
            // from, from name
            $sender = array($fromemail,$fromname);
            $mailer->setSender($sender);
            
            //$recipient = array( 'cs1559@sbcglobal.net' );
            $mailer->addRecipient($recipient);
                       
            $mailer->setSubject($subject);
                
            $mailer->isHtml(true);
            $mailer->Encoding = 'base64';
            $mailer->setBody($body);
            $mailer->addCC($cc);
            try {
                $send = $mailer->Send();
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
    
}

