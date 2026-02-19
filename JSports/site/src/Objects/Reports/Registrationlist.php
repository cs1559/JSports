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
namespace FP4P\Component\JSports\Site\Objects\Reports;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class Registrationlist implements Report
{
    private $name       = "Registration List Report";
    private $layout     = "reports.registrationlist"; 
    private $data;
    private $programid;
    private $format;
    private $filters;
        
    public function getName() {
        return $this->name;
    }
    
    public function getData() {
        
        /*
         * select teamname, grouping, name as 'coach name', address, city, email, phone, cellphone, registeredby, skilllevel, if (playoffs=1,'Yes','No') as "Playoffs"
from jos2823_jsports_registrations
where published = 0 and programid= 35
order by grouping, name;

         */        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        

        
        $query = $db->getQuery(true)
        ->select([
            $db->quoteName('teamname'),
            $db->quoteName('grouping'),
            $db->quoteName('name') . ' AS ' . $db->quoteName('coach_name'),
            $db->quoteName('address'),
            $db->quoteName('city'),
            $db->quoteName('email'),
            $db->quoteName('phone'),
            $db->quoteName('cellphone'),
            $db->quoteName('registeredby'),
            $db->quoteName('skilllevel'),
            "IF(" . $db->quoteName('playoffs') . " = 1, 'Yes', 'No') AS " . $db->quoteName('playoffs')
        ])
        ->from($db->quoteName('#__jsports_registrations'))
        ->where($db->quoteName('programid') . ' = :programid')
        ->order([
            $db->quoteName('grouping'),
            $db->quoteName('name')
        ]);
        
        $query->bind(':programid', $this->programid, ParameterType::INTEGER);
        
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    public function toHtml() {
        
        $rows = $this->getData();
               
        // Render from administrator component layouts folder
        return LayoutHelper::render($this->layout, $rows, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        
    }
 
    
    public function render()
    {
        
        switch ($this->format) {
            case 'html':
                return $this->toHtml();
                break;
                
            default:
                return $this->toHtml();
        }
        
    }
    public function setContext(array $context)
    {

        $this->filters      = $context['filters'];
        $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;

        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

