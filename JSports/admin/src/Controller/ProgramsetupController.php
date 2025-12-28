<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\Database\DatabaseInterface;

/**
 * This controller is used in the process of setting up each program by assigning a given registrant
 * to a specific division.  This supports "bulk" setting of division assignments.
 * 
 */
class ProgramsetupController extends AdminController
{
    protected $default_view = 'programsetup';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }

    /**
     * This function is control function for loop through all the items on the Program Setup view and call the updateMap function 
     * to set the division value.
     * 
     * @todo Eliminate the hardcoded and place the message in the language file.
     */
    public function assignDivisions() {
        
        $input = Factory::getApplication()->input;
        $requestData = $input->post->get('jform', [], 'array');
        
        $programid = $_REQUEST['programid'];
        
        $assignments = $_REQUEST["div-assignment"];
        
        foreach ($assignments as $key => $value) {
            $this->updateMap($key, $value);
        }
        
        Factory::getApplication()->enqueueMessage("Assignments have been saved", 'message');
        
        $this->setRedirect('index.php?option=com_jsports&view=programsetup&programid=' . $programid);
        
    }
    
    protected function programsList(){
        
        $this->setRedirect('index.php?option=com_jsports&view=programs');
        
    }

    /**
     * This function updates the internal map that associates a team to a given division wihtin a given program.  This map table
     * defines the relationship between program -> divisions -> teams.
     *  
     * @param unknown $id
     * @param unknown $divisionid
     */
    protected function updateMap($id, $divisionid) {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);
        
        $query->update($db->quoteName('#__jsports_map'))
            ->set($db->quoteName('divisionid') . ' = :divisionid')
            ->bind(':divisionid', $divisionid , ParameterType::INTEGER)
            ->where($db->quoteName('id'). ' = ' . $db->quote($id));
        $db->setQuery($query);
        $db->execute(); 
    }
    
}
