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

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\LeaguesTable;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;

class DashboardModel extends BaseModel
{
    
    public $title;
    public $version;
        
    
    /**
     * @todo  This constructor needs to be redon.  The concept of LEAGUE is still under review.  Many of these options
     *          should be component level settings.
     */
    public function __construct() {
        parent::__construct();
                
        $this->title  = "Sports Managment Component";
        $this->version = JSHelper::getVersion();
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getVersion() {
        return $this->version;
    }
        
}
