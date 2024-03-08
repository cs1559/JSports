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

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;

/**
 * JSports Batch Controller - provides the component with batch capabilities that can be execute via CRON.
 *
 * @since  1.5
 */
class BatchController extends BaseController
{

    public function updatestandings($cachable = false, $urlparams = array()) {

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
        
        //echo $input->get('validationid', '', 'string') . "<br/>";
        //echo $cron_key . "<br/>";
        
        if (($input->exists('validationid')) && ($input->get('validationid', '', 'string') == $cron_key)) {
            
            ob_start();
            echo $output->datetime . "\n";
            print "Update Standings:  START \n";
            $engine = new StandingsEngine();
            $engine->generateStandings(33);
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
}