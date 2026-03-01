<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     Tools.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Objects\Adapters\NSProAdapter;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\SponsorService;
// /use FP4P\Component\JSports\Site\Services\LogService;


class SponsorassetController extends FormController
{
            
    protected $default_view = 'sponsorasset';
           
    public function save($key=null, $urlVar = null) {
        
        $app   = Factory::getApplication();
        $input = $app->getInput();
        
        // Get entire form array
        $data = $input->get('jform', [], 'array');
        
        // Get specific field
        $sponsorid = $data['sponsorid'] ?? null;
        
        // Or safely:
//         $myValue = $input->get('jform', [], 'array')['fieldname'] ?? null;
        
        parent::save($key, $urlVar);

        $this->setRedirect('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . $sponsorid);
    }
    
    public function add() {
        
        
        $app = Factory::getApplication();
        
        $sponsorid = $app->input->getInt('sponsorid', 0);
        $return = $app->input->getString('return', 0);
        
        // Put it in user state so the new "add" form can default it
        $app->setUserState('com_jsports.edit.sponsorasset.sponsorid', $sponsorid);
        
        // Always use Joomla input filters
        
        $this->setRedirect('index.php?option=com_jsports&view=sponsorasset&layout=edit&sponsorid=' . $sponsorid . '&return=' . $return);
    }
    
    public function cancel($key = null) {
        $app    = \Joomla\CMS\Factory::getApplication();
        $return = $app->input->getBase64('return');
        
        // Let Joomla handle check-in, etc.
        parent::cancel($key);
        
        if (!empty($return)) {
            $this->setRedirect(\Joomla\CMS\Router\Route::_(base64_decode($return), false));
        }
        
        return true;
    }

    /**
     * Method to DELETE one or more records from the database
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     * @since   4.0.0
     */
    public function delete() {

                $app    = \Joomla\CMS\Factory::getApplication();
                $assetid = $app->input->getInt('id');
                $return = $app->input->getBase64('return');

                $rc = SponsorService::deleteAsset($assetid);
                
                if (!empty($return)) {
                    $this->setRedirect(\Joomla\CMS\Router\Route::_(base64_decode($return), false));
                }
                
                return true;

    }
    
    public function publish() {
        $model = $this->getModel('Sponsorasset');
        
        parent::publish();
        
        $app = Factory::getApplication();
        $sponsorid = $app->getUserState('com_jsports.edit.sponsorasset.sponsorid');
        $this->setRedirect('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . $sponsorid);
        
    }
    
}
