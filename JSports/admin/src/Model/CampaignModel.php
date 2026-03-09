<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use FP4P\Component\JSports\Site\Services\CampaignService;

class CampaignModel extends AdminModel
{
    
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @since   3.0
     * @throws  \Exception
     */
    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'campaigns';
        $prefix = 'Table';
        
        if ($table = $this->_createTable($name, $prefix, $options))
        {
            return $table;
        }
        
        throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
    }
    
    protected function prepareTable($table)
    {
        // Convert empty string to NULL (best for optional FK)
        if ($table->assetid === '' || $table->assetid === null) {
            $table->assetid = null;
        }
        
        parent::prepareTable($table);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|boolean  A Form object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jsports.campaign', 'campaign', array('control' => 'jform', 'load_data' => $loadData));
        
        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.campaign.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
            
        }
        
        // Convert CSV string to array
        if (!empty($data->positions) && is_string($data->positions)) {
            $data->positions = explode(',', $data->positions);
        }
        
        $this->preprocessData('com_jsports.campaign', $data);
        
        return $data;
    }
    
    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->input;
        
        // Posted form data
        $requestData = $input->post->get('jform', [], 'array');
        // $bulletinTitle = $requestData['title'] ?? '';
        $type = $data['campaigntype'];
        if (!empty($data['campaigntype']) && $data['campaigntype'] === 'T') {
            $data['assetid'] = null;
        }
        $data['positions'] = implode(',', $requestData['positions']);
        $data['assetid'] = $requestData['assetid'];
        $data['sponsorshipid'] = $requestData['sponsorshipid'];
        
        if ($data['assetid'] > 0) {
            $asset = CampaignService::getAsset($data['sponsorid'], $data['assetid']);
            $data['url'] = CampaignService::getAssetURL($data['sponsorid'], $asset->filename);
        }

        
        return parent::save($data);

    }
    
}

