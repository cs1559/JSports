<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Reports;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class CampaignsclickReport extends AbstractReport
{
    private $programid;

    public function __construct() {
        $this->setName("Campaigns Click Report");
        $this->setLayout("reports.campaignsclick");
    }
    
    
    public function getData() : array {
         
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);

/*

SELECT c.*, s.name as sponsorname 
FROM `jos2823_jsports_campaigns` as c, jos2823_jsports_sponsors as s
where c.sponsorid = s.id

*/
        
        $query
        ->select([
            $db->quoteName('c') . '.*',
            $db->quoteName('s.name', 'sponsorname')
        ])
        ->from($db->quoteName('#__jsports_campaigns', 'c'))
        ->join(
            'INNER',
            $db->quoteName('#__jsports_sponsors', 's')
            . ' ON ' . $db->quoteName('c.sponsorid') . ' = ' . $db->quoteName('s.id')
            );
        
        $query->order($db->quoteName('s.name'));
        
        $db->setQuery($query);
                
        return $db->loadObjectList();
    }
    
    public function setContext(array $context) : void
    {

        $this->filters      = $context['filters'];
//         $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;
        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

