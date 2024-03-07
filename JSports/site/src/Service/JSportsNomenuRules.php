<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Service;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\RulesInterface;

/**
 * Rule to process URLs without a menu item
 *
 * @since  3.4
 */
class JSportsNomenuRules implements RulesInterface
{
    /**
     * Router this rule belongs to
     *
     * @var RouterView
     * @since 3.4
     */
    protected $router;
    
    /**
     * Class constructor.
     *
     * @param   RouterView  $router  Router this rule belongs to
     *
     * @since   3.4
     */
    public function __construct(RouterView $router)
    {
        $this->router = $router;
        
    }
    
    /**
     * Dummymethod to fullfill the interface requirements
     *
     * @param   array  &$query  The query array to process
     *
     * @return  void
     *
     * @since   3.4
     * @codeCoverageIgnore
     */
    public function preprocess(&$query)
    {
        $test = 'Test';
    }
    
    /**
     * Parse a menu-less URL
     *
     * @param   array  &$segments  The URL segments to parse
     * @param   array  &$vars      The vars that result from the segments
     *
     * @return  void
     *
     * @since   3.4
     */
    public function parse(&$segments, &$vars)
    {
        
        
        //  
        //with this url: http://localhost/j4x/my-walks/mywalk-n/walk-title.html
        // segments: [[0] => mywalk-n, 
        //      [1] => walk-title]
        // vars: [[option] => com_mywalks, [view] => mywalks, [id] => 0]
        
        $vars['view'] = $segments[0];
        
        if (isset($segments[1])) {
            $ids = explode('-',$segments[1]);
            $vars['id'] = $ids[0];
        }
        
        array_shift($segments);
        array_shift($segments);
        return;
    }
    
    /**
     * Build a menu-less URL
     *
     * @param   array  &$query     The vars that should be converted
     * @param   array  &$segments  The URL segments to create
     *
     * @return  voidx`
     *
     * @since   3.4
     */
    public function build(&$query, &$segments)
    {
        // content of $query ($segments is empty or [[0] => mywalk-3])
        // when called by the menu: [[option] => com_mywalks, [Itemid] => 126]
        // when called by the component: [[option] => com_mywalks, [view] => mywalk, [id] => 1, [Itemid] => 126]
        // when called from a module: [[option] => com_mywalks, [view] => mywalks, [format] => html, [Itemid] => 126]
        // when called from breadcrumbs: [[option] => com_mywalks, [view] => mywalks, [Itemid] => 126]
    
        // the url should look like this: /site-root/mywalks/walk-n/walk-title.html
      
        // if the view is not mywalk - the single walk view
        
        if (!isset($query['view']) || (isset($query['view']) && $query['view'] !== 'team') || isset($query['format']))
        {
            return;
        }
        //$segments[] = $query['view'] . '-' . $query['id'];
        $segments[] = $query['view'];
        
        if (isset($query['view'])) {
            switch ($query['view']) {
                case 'team':
                    $tseg = $this->router->getTeamSegment($query['id'], $query);
                    foreach ($tseg as $key => $value) {
                        $val = $key . "-" . $value;
                        $segments[] = $val;
                    }
                    break;
                default:
                    break;
            }
        }
        
        // the last part of the url may be missing
        if (isset($query['slug'])) {
            $segments[] = $query['slug'];
            unset($query['slug']);
        }

            
        unset($query['view']);
        unset($query['id']);
    }
}