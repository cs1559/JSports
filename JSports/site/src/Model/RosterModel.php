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
namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;

use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RosterService;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Administrator\Table\RostersTable;

/**
 * Roster Model exposes functions to manage the Roster
 *
 */
class RosterModel extends FormModel
{
    
    protected $data;
    
    protected $programs;
    protected $recordhistory;
    
    protected $form = 'roster';
       
    protected function populateState()
    {
        parent::populateState();
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        $this->setState('roster.id', $input->getInt('id'));
        $this->setState('roster.teamid', $input->getInt('teamid'));
        $this->setState('roster.programid', $input->getInt('programid'));
    }
    
    public function getItem(?int $id = null): ?RostersTable {

        $id ??= (int) $this->getState('roster.id', 0);
        $item = (new RosterService())->getItem($id);
        
        if ($id === 0 && $item) {
            $input = Factory::getApplication()->input;
            $item->teamid    = $input->getInt('teamid', 0);
            $item->programid = $input->getInt('programid', 0);
        }
        
        return $item;
        
    }
        
    
    public function getForm($data = array(), $loadData = true)
    {
        
        $form = $this->loadForm(
            'com_jsports_form.roster.data', // just a unique name to identify the form
            'roster',				     // the filename of the XML form definition
            array(
                'control' => 'jform',	// the name of the array for the POST parameters
                'load_data' => $loadData	// will be TRUE
            )
            );
       
        // Removed previous code and throw runtime exception as the getErrors function is being removed.s      
        if (!$form) {
            throw new \RuntimeException('Unable to load roster form.');
        }

        return $form;
    }
    
    protected function loadFormData()
    {
        
        $app = Factory::getApplication();
        
        $data = $app->getUserState('com_jsports_form.roster.data', null);
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $this->preprocessData('jsports.roster', $data);
                
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     *
     * @param array $data
     * @return boolean
     */
    public function save($data) {
        
            $logger = Myapp::getLogger();
            $table  = RosterService::getRostersTable();
            
            try {
                if (!$table->save($data)) {
                    throw new \RuntimeException(implode("\n", $table->getErrors() ?? []));
                }
                
                $logger->info(
                    'Saving roster item id - ' . $table->id .
                    ' Name: ' . $table->firstname . ' ' . $table->lastname .
                    ' ADMIN=' . ($table->staffadmin ? 'Yes' : 'No') .
                    ' TYPE=' . $table->classification
                    );
                
                return true;
            } catch (\Throwable $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }
        
    }
    
}