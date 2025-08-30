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
namespace FP4P\Component\JSports\Site\Objects\Validators;

use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Validators\ValidatorResponse;

class EmailValidator
{
   
    private $responseMsg = ""; 
        
//     function __construct($email) {
//         $this->email = $email;
//     }
    
   // function checkEmailBounce($email) {
    function validate($email) {
        $emailParts = explode('@', $email);
        $domain = $emailParts[1];
        
        echo "PROCESSING EMAIL:  " . $email . "</br>";
        
        // Get MX records for the domain
        $mxHosts = [];
        $mxWeight = [];
        if (!getmxrr($domain, $mxHosts, $mxWeight)) {
            return new ValidatorResponse(100,"Invalid domain or no MX record found.");
        }
        
        $mxHost = $mxHosts[0]; // Using the first MX server
        
        // Connect to the mail server
        $connection = @fsockopen($mxHost, 25, $errno, $errstr, 10);
        if (!$connection) {
            return new ValidatorReponse(101,"Failed to connect to the server.");
        }
        
        // Perform SMTP handshake
        $response = fgets($connection);
        // NOTE:  Client introduces itself to the the SMTP server - the domain value here can be any value.
        fputs($connection, "HELO swibl.org\r\n");
        $response = fgets($connection);
        
        // Set sender and recipient         
        @fputs($connection, "MAIL FROM:<info@swibl.org>\r\n");
        $response = fgets($connection);
        
        @fputs($connection, "RCPT TO:<$email>\r\n");
        $response = fgets($connection);
        
        // Check the server response
        if (strpos($response, '250') !== false) {
            $result = new ValidatorResponse(0,"Email is valid and deliverable.");
        } elseif (strpos($response, '550') !== false) {
            $result = new ValidatorResponse(102,"Email address is invalid or will bounce - " . $response);
        } else {
            $result = new ValidatorResponse(103,"Unable to determine:" . $response);
            //"Unable to determine. Response: $response";
        }
        
        // Clean up
        @fputs($connection, "QUIT\r\n");
        fclose($connection);
        

        return $result;
    }
    
    
}


