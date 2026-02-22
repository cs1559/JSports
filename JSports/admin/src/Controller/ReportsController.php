<?php   
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Reports\NoRosterReport;
use FP4P\Component\JSports\Site\Objects\Reports\ReportFactory;

class ReportsController extends BaseController
{
    
    /**
     * This function generates the output for the AJAX request.
     */
    public function ajaxPreview()
    {
        $app = Factory::getApplication();
        
        // CSRF check (POST)
        $this->checkToken('post');
        
        $model = $this->getModel('Reports', 'Administrator', ['ignore_request' => false]);      
        
        $viewmode   = $app->input->getCmd('viewmode', 'summary');
        $programid  = $app->input->getCmd('programid', 0);
        $format     = $app->input->getCmd('rformat', 'html');
        
        $context = [];
        $context['reportname'] = $viewmode;
        $context['format'] = $format;
        $context['filters']['programid'] = $programid;
               
        $report = ReportFactory::create($viewmode);
        $report->setContext($context);
        echo $report->render();
        
        $app->close();
    }

    /**
     * This function is NOT needed due to refactoring a each report object will handle the rendering of the output.
     * 
     * @deprecated
     * @param mixed $context
     * @return string 
     */
    protected function renderReport($context)
    {
        
        $filters = $context['filters'];
        
        $report = new NoRosterReport($filters);
        
        if ($context['format'] === "csv") {
            return $report->toCSV();
        } else {
            return $report->toHtml();
        }
    }
    
    /**
     * This function will handle the export capability of of the report to a CSV format.
     */
    public function export() {
        $app = Factory::getApplication();

        // CSRF check (POST)
        //$this->checkToken('post');
        
//         $model = $this->getModel('Reports', 'Administrator', ['ignore_request' => false]);
        
        $viewmode   = $app->input->getCmd('viewmode', 'summary');
        $programid  = $app->input->getCmd('programid', 0);
        $format     = $app->input->getCmd('rformat', 'html');
        
        $context = [];
        $context['reportname'] = $viewmode;
        $context['format'] = $format;
        $context['filters']['programid'] = $programid;

        $filters = $context['filters'];
        
        $report = ReportFactory::create($viewmode);
        $report->setContext($context);
//         echo $report->render($format);
        
        $outputfn = preg_replace('/\s+/', '', $report->getName());
        $rows = $report->getData(); // or $db->loadObjectList()
        
        if (empty($rows)) {
            throw new \Exception('No data to export');
        }
        
        // Force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. $outputfn . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        $output = fopen('php://output', 'w');
        
        // Write CSV header from first object
        fputcsv($output, array_keys(get_object_vars($rows[0])));
        
        // Write rows
        foreach ($rows as $row) {
            fputcsv($output, get_object_vars($row));
        }
        
        fclose($output);
        
        $app->close();
    }
}
