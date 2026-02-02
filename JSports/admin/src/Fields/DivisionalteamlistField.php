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
namespace FP4P\Component\JSports\Administrator\Fields;

defined('JPATH_PLATFORM') or die;
    
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Site\Services\TeamService;

class DivisionalteamlistField extends ListField
{
    protected $type = 'divisionalteamlist';
    protected $teamid = 0;
    protected $programid = 0;
    
    protected function getOptions()
    {
        
        $options[] = HTMLHelper::_('select.option',"0","-- Select Team --");
        
        // XML attributes (the two filtering attributes)
        $programFieldName = (string) $this->element['programField'];
        $divisionFieldName  = (string) $this->element['divisionField'];
        
        // Pull values from the current form
        $programid = (int) $this->form->getValue('filter.'.$programFieldName);
        $divisionid  = (int) $this->form->getValue('filter.'.$divisionFieldName);
        
        // If either is missing, return default options only (or return empty)
        if ($programid === 0 || $divisionid === 0)
        {
            return array_merge(parent::getOptions(), $options);
        }
        
        $teams = TeamService::getTeamsByDivision($programid, $divisionid);

        foreach ($teams as $row)
        {
            $options[] = (object) [
                'value' => $row['teamid'],
                'text'  => $row['teamname']
            ];
        }
        
        return array_merge(parent::getOptions(), $options);
        
    }
        

}