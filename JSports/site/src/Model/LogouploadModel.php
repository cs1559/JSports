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

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\MVC\Model\FormModel;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;


/**
 * Methods supporting a list of mywalks records.
 *
 * @since  1.6
 */
class LogouploadModel extends FormModel
{
    
    /**
     * @var     object  The user profile data.
     * @since   1.6
     */
    protected $data;
    
    protected $programs;
    protected $recordhistory;
    protected $actionmenu;
    protected $teamid;    
    protected $form = 'team';
    
    
    
    public function getItem(){
        
        $input = Factory::getApplication()->input;

        $this->teamid = (int) $this->getState('logoupload.id')
            ?: Factory::getApplication()->input->getInt('teamid');
        
        $svc = new TeamService();
        $item = $svc->getItem($this->teamid);

        return $item;
    }
    
    
    
    public function getForm($data = array(), $loadData = true)
    {
        
//         $form = $this->loadForm('com_jsports.logoupload', 'logoupload', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.logoupload.data', // just a unique name to identify the form
            'logoupload',				     // the filename of the XML form definition
            // Joomla will look in the models/forms folder for this file
            array(
                'control' => 'jform',	// the name of the array for the POST parameters
                'load_data' => $loadData	// will be TRUE
            )
        );
                
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState(
            'com_jsports_form.logoupload.data',
            []
            );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        

        $this->preprocessData('jsports.logoupload', $data);
        
        
        return $data;
    }
    
    
    protected function populateState() {
        
        parent::populateState();
        
        /** @var SiteApplication $app */
        $app = Factory::getContainer()->get(SiteApplication::class);
        $this->setState('logoupload.id', $app->getInput()->getInt('teamid'));
    }
    
}

