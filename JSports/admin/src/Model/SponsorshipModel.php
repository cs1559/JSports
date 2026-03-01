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
            } else {
                $data->sponsorid = $data->sponsorid ?? $sponsorId;
            }
        }

        $this->preprocessData('com_jsports.sponsorship', $data);

        return $data;
    }


}
