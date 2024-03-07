<?php
namespace FP4P\Component\JSports\Administrator\View\Leagues;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Services\LeagueService\LeagueService;
use FP4P\Component\JSports\Administrator\Table\LeaguesTable;

class HtmlView extends BaseHtmlView
{
    public $form;
    
    public function display($tpl = null)
    {
        
        $db = Factory::getDbo();
        $leagues = new LeaguesTable($db);
        
        $leagues->load(1);
        
        $fields = array();
        $fields['name'] = 'SWIBL2';
        
        $leagues->save($fields);
        
        print_r($leagues->configuration);
        echo "*** done ***";
        exit;
        //         $this->addToolBar();
        
        parent::display($tpl);
    }
    
    protected function addToolBar()
    {
        ToolbarHelper::title(Text::_('JSports'));
        ToolbarHelper::title(Text::_('Add'));
        
        ToolbarHelper::apply('planet.apply');
    }
}