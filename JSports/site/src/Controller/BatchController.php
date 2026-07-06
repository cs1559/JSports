<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     Batch.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Controller;

/**
 * REVISION HISTORY:
 * 2025-01-16  Cleaned up code and added inline comments.
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\MailService;


/**
 * JSports Batch Controller - provides the component with batch capabilities that can be execute via CRON.
 *
 * @since  1.5
 */
class BatchController extends BaseController
{

    /**
     * This function will perform the batch operation to update the league standings.  It is expected
     * that this function is performed via a CRON JOB.
     *
     * Authenticates the request against a CRON key (derived from the component's
     * configured secret) before regenerating standings for every non-completed,
     * active program. Output is buffered and flushed as plain text, ending the
     * request via $app->close() either way.
     *
     * @param   boolean  $cachable   Unused; present only to satisfy the base
     *                               controller's display() signature convention.
     * @param   array    $urlparams  Unused; see above.
     *
     * @return  void  Never returns normally — always terminates via $app->close().
     *
     * @since   1.5
     */
    public function updatestandings($cachable = false, $urlparams = array()) : void {

        $params = ComponentHelper::getParams('com_jsports');
        $salt = $params->get('cronkey');
        
        $app = Factory::getApplication();
        
        $output = new \stdclass();
        $output->datetime = date('l jS \of F Y h:i:s A');
        $output->content = "";
        
        $input = Factory::getApplication()->input;
        $site = 'https://swibl.org';
        $content = '';
        
        $cron_key = md5('JSports Key For: ' . $site . $salt);
                
        if (($input->exists('validationid')) && ($input->get('validationid', '', 'string') == $cron_key)) {
            
            ob_start();
            echo $output->datetime . "\n";
            print "Update Standings:  START \n";
            
            $engine = new StandingsEngine();
            
            // 2024-03-11 Made changes to support issue#8 - remove hardcoded value.
            // Retrieve the non completed programs.  'true' will filter only ACTIVE programs.
            $programs = ProgramsService::getNonCompletedPrograms(true);
            foreach ($programs as $program) {
                print "- Processing Program " . $program->id . "\n";
                $engine->generateStandings($program->id);
            }
            print "Update Standings:  END \n";
            $msize = ob_get_length();
            header("Content-Length: $msize");
            header('Connection: close');
            $content = ob_get_contents();
            ob_end_flush();
            ob_flush();
            flush();
            
            $app->close();
        } else {
            $content = "Invalid Key";
        }
        
        print $content;
        $app->close();
    }
    
    
    /**
     * Debug helper that sends a hardcoded test email to a fixed recipient list
     * and echoes whether it succeeded.
     *
     * NOTE: recipients and content are hardcoded — this is intended for manual
     * developer testing only and should not be exposed on a production route
     * without further access control.
     *
     * @return  void
     *
     * @since   1.5
     */
    public function testEmail() : void {
        $recipients = array();
        $recipients[]='cs1559@sbcglobal.net';
        $recipients[]='cjstrieter@gmail.com';
        $subject = 'This is a test';
        $body = '<strong>Hello World</strong><br/>This is a test email to multiple recipients';
        $svc = new MailService();
        $rc = $svc->sendMail($recipients, $subject, $body);
        if ($rc) {
            echo "email sent";
        } else {
            echo "email failed";
        }
        
        
        
    }
}
