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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;

class GamesController extends AdminController
{
    protected $default_view = 'games';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }

    public function export(): void
    {
        // CSRF check
        //         if (!Session::checkToken('request')) {
        //             throw new \RuntimeException('Invalid token', 403);
        //         }
        
        $app   = Factory::getApplication();
        $user  = $app->getIdentity();
        
        // Optional ACL gate (define 'core.export' in access.xml if you want)
        // if (!$user->authorise('core.export', 'com_example')) {
        //     throw new \RuntimeException('Not authorized', 403);
        // }
        
        // Use the LIST model with current filters/search/sort.
        // NOTE: Use your list model name here (usually 'Items').
        $model = $this->getModel('Games', 'Administrator', ['ignore_request' => false]);
        
        // Ensure we export ALL matching rows (not just the current page)
        $model->setState('list.start', 0);
        $model->setState('list.limit', 0); // 0 = no limit
        
        $rows = $model->getItems();
        
        // Normalize to array-of-assoc
        $rows = array_map(function($row) { return (array) $row; }, $rows ?? []);
        
        // Prepare headers
        $filename = 'games-' . gmdate('Ymd_His') . '.csv';
        $app->clearHeaders();
        $app->setHeader('Content-Type', 'text/csv; charset=utf-8', true);
        $app->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"', true);
        $app->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $app->sendHeaders();
        
        // Stream CSV
        $out = fopen('php://output', 'w');
        
        // Write header row
        if (!empty($rows)) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $r) {
                // Flatten any arrays/objects to JSON strings to avoid CSV errors
                foreach ($r as $k => $v) {
                    if (is_array($v) || is_object($v)) {
                        $r[$k] = json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                fputcsv($out, $r);
            }
        } else {
            // If no results, still emit a minimal CSV with a notice column
            fputcsv($out, ['notice']);
            fputcsv($out, ['No data matches your current filters.']);
        }
        
        fclose($out);
        // Hard exit to prevent Joomla from rendering the list view after streaming
        $app->close();
    }
    
    
}
