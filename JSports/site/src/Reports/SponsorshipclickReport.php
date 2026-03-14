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

class SponsorshipclickReport extends AbstractReport
{
    private $programid;

    public function __construct() {
        $this->setName("Sponsorship Click Report");
        $this->setLayout("reports.sponsorshipclick");
    }
    
    
    public function getData() : array {
         
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);

/*

SELECT s.name, p2.name, p.plancode,  p.startdate, p.enddate, impressions, clicks 
FROM `jos2823_jsports_sponsors` as s, jos2823_jsports_sponsorships as p, jos2823_jsports_sponsorship_plans as p2
WHERE s.id = p.sponsorid
and p.plancode = p2.plancode
and p2.bolton <> 1
order by s.name;

*/
        $query
        ->select([
            $db->quoteName('s.name', 'sponsor_name'),
            $db->quoteName('p2.name', 'plan_name'),
            $db->quoteName('p.plancode'),
            $db->quoteName('p.startdate'),
            $db->quoteName('p.enddate'),
            $db->quoteName('p.impressions'),
            $db->quoteName('p.clicks')
        ])
        ->from($db->quoteName('#__jsports_sponsors', 's'))
        ->join(
            'INNER',
            $db->quoteName('#__jsports_sponsorships', 'p')
            . ' ON ' . $db->quoteName('s.id') . ' = ' . $db->quoteName('p.sponsorid')
            )
            ->join(
                'INNER',
                $db->quoteName('#__jsports_sponsorship_plans', 'p2')
                . ' ON ' . $db->quoteName('p.plancode') . ' = ' . $db->quoteName('p2.plancode')
                )
                ->where($db->quoteName('p2.bolton') . ' <> 1')
                ->order($db->quoteName('s.name'));
        
        $db->setQuery($query);
                
        return $db->loadObjectList();
    }
    
    public function setContext(array $context) : void
    {

        $this->filters      = $context['filters'];
        $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;
        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

