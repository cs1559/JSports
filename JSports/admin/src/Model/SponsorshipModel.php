<?php
/**
 * JSports - Joomla Sports Management Component
 */
namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\Database\ParameterType;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Services\SponsorService;

class SponsorshipModel extends AdminModel
{

    /** @var array<int, \stdClass> */
    protected $sponsorships;

    /**
     */
    public function getTable($name = 'Sponsorships', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_jsports.sponsorship', 'sponsorship', [
            'control' => 'jform',
            'load_data' => $loadData
        ]);

        return $form ?: false;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.sponsorship.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }
        $id = is_array($data) ? ($data['id'] ?? 0) : ($data->id ?? 0);

        $isNew = empty($id);

        if ($isNew) {
            $sponsorId = (int) $app->input->getInt('sponsorid', (int) $app->getUserState('com_jsports.edit.sponsorship.sponsorid', 0));

            // $data might be an object or array depending on your code path
            if (is_array($data)) {
                $data['sponsorid'] = $data['sponsorid'] ?? $sponsorId;
                // Have to treat this as a timestamp even thought we just need the date.
                $data['startdate'] = Factory::getDate(date('Y-01-01 12:00:00'))->format('Y-m-d H:i:s');
                $data['enddate'] = Factory::getDate(date('Y-12-31 12:00:00'))->format('Y-m-d H:i:s');
            } else {
                $data->sponsorid = $data->sponsorid ?? $sponsorId;
                $data->startdate = Factory::getDate(date('Y-01-01 12:00:00'))->format('Y-m-d H:i:s');
                $data->enddate = Factory::getDate(date('Y-12-31 12:00:00'))->format('Y-m-d H:i:s');
            }
            
        }

        $this->preprocessData('com_jsports.sponsorship', $data);

        return $data;
    }


    public function save($data)
    {

        $id       = (int) ($data['id'] ?? 0);
        $sponsorid = (int) ($data['sponsorid'] ?? 0);
        $plancode = (string) ($data['plancode'] ?? 0);

        
        echo '<pre>'; print_r($data); echo '</pre>'; exit;
        
        
        // Replace with your actual "season" key: seasonid, programid, year, etc.
        $programid = (int) ($data['programid'] ?? 0);
  
        if (!$id) {
            if (!SponsorService::canAddSponsorship($sponsorid, $programid, $plancode)) {
                $this->setError('This sponsor already has a primary sponsorship for the season. Add a bolt-on instead.');
                return false;
            }
        }
        
        // ADD ANY ADDITIONAL RULES TO DETERMINE IF THE SPONSORSHIP CAN BE ADDED.
        
        return parent::save($data);
    }
    
    
}
