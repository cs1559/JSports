<?php 
namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Reports\NoRosterReport;

class ReportsController extends BaseController
{
    public function ajaxPreview()
    {
        $app = Factory::getApplication();
        
        // CSRF check (POST)
        $this->checkToken('post');
        
        $model = $this->getModel('Reports', 'Administrator', ['ignore_request' => false]);
        
//         echo get_class($model);
//         exit;
        
        
        $viewmode = $app->input->getCmd('viewmode', 'summary');
        
        // Typically you'd call your Service here
        $displayData = [
            'stats' => ['teams' => 159, 'divisions' => 19],
            'rows'  => [
                ['team' => 'Athletics', 'wins' => 25],
                ['team' => 'Sox', 'wins' => 13],
            ],
        ];
        
        $layout = match ($viewmode) {
            'table' => 'ajax.table',
            'noroster' => 'reports.noroster',
            default => 'ajax.summary',
        };
        
        if ($viewmode === "noroster") {
            echo $this->renderReport();
        } else {
        // Render from administrator component layouts folder
            echo LayoutHelper::render($layout, $displayData, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        }
        
        $app->close();
    }

    protected function renderReport()
    {
           $report = new NoRosterReport();
           return $report->toHtml();
    }
    
    
    protected function renderSummary()
    {
        return '<div class="alert alert-info">
            <strong>Summary View</strong><br>
            Total Teams: 159<br>
            Divisions: 19
        </div>';
    }

    protected function renderTable()
    {
        return '
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Wins</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Athletics</td><td>5</td></tr>
                <tr><td>Sox</td><td>3</td></tr>
            </tbody>
        </table>';
    }
}
