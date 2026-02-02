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

namespace FP4P\Component\JSports\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
//use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Service\JSportsNomenuRules as NomenuRules;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Routing class from com_contact
 *
 * @since  3.3
 */
class Router extends RouterView
{
    /**
     * Flag to remove IDs
     *
     * @var    boolean
     */
    protected $noIDs = true;

    /**
     * The category factory
     *
     * @var CategoryFactoryInterface
     *
     * @since  4.0.0
     */
    private $categoryFactory;

    /**
     * The category cache
     *
     * @var  array
     *
     * @since  4.0.0
     */
    private $categoryCache = [];

    /**
     * The db
     *
     * @var DatabaseInterface
     *
     * @since  4.0.0
     */
    private $db;

    /**
     * Content Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        //* @todo for views that have no arguments, then remove the appropriate setKey statements below.
        
        $this->categoryFactory = $categoryFactory;
        $this->db              = $db;

        $params      = ComponentHelper::getParams('com_jsports');
        $this->noIDs = (bool) $params->get('sef_ids');

        // DEFINE/CONFIGURE ALL VIEWS ON THE SITE SIDE OF THE EXTENSION
        // NOTE:  The KEY should map to a variable passed on the URL
        $dashboard = new RouterViewConfiguration('dashboard');
//         $dashboard->setKey('id');
        $this->registerView($dashboard);
        
        $register = new RouterViewConfiguration('register');
        $register->setKey('id');
        $this->registerView($register);
        
        $standings  = new RouterViewConfiguration('standings');
//         $standings->setKey('id');
        $this->registerView($standings);

        $teams = new RouterViewConfiguration('teams');
//         $teams->setKey('id');
        $this->registerView($teams);
        
        $team = new RouterViewConfiguration('team');
        $team->setKey('id');
        $this->registerView($team);
        
        $myteams = new RouterViewConfiguration('myteams');
//         $myteams->setKey('id');
        $this->registerView($myteams);

        $venues = new RouterViewConfiguration('venues');
//         $venues->setKey('id');
        $this->registerView($venues);
        
        $openings = new RouterViewConfiguration('openings');
//         $openings->setKey('id');
        $this->registerView($openings);

        $postings = new RouterViewConfiguration('postings');
        //         $openings->setKey('id');
        $this->registerView($postings);
        
//         $bulletins = new RouterViewConfiguration('bulletins');
//         $bulletins->setKey('id');
//         $this->registerView($bulletins);

//         $bulletin = new RouterViewConfiguration('bulletin');
//         $bulletin->setKey('id');
//         $this->registerView($bulletin);
        
        $games = new RouterViewConfiguration('games');
//         $games->setKey('id');
        $this->registerView($games);
        
        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    
    public function preprocess($query)
    {
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = (int) $params->get('itemid');
        
        if (!isset($query['Itemid']) && $itemid > 0) {
            $query['Itemid'] = $itemid;
        }
        
        return $query;
        
//         $params = ComponentHelper::getParams('com_jsports');
//         $itemid = $params->get('itemid');

//         if (!key_exists('Itemid', $query)) {
//             $query['Itemid'] = $itemid;
//         }
        
//         // Process the parsed variables based on custom defined rules
//         foreach ($this->rules as $rule) {
//             $rule->preprocess($query);
//         }
        
//         return $query;
    }
        
    /*   
     * 
     * 
     * $id = This value looks something like:   1011:two-rivers-tribe   ("two-rivers-tribe" is the alias returned by the DB query)
     * RETURN VALUE = The array that is returned by this function looks like:   Array ( [1011] => two-rivers-tribe ) 
     * 
     */
    public function getTeamSegment($id, $query)
    {

            // Accept "1011" or "1011:alias"
            $id = (int) explode(':', (string) $id, 2)[0];
            
            $db = $this->db;
            $q  = $db->getQuery(true)
            ->select($db->quoteName('alias'))
            ->from($db->quoteName('#__jsports_teams'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);
            
            $db->setQuery($q);
            $alias = (string) $db->loadResult();
            
            // If no alias found, fall back to numeric id segment
            if ($alias === '') {
                return [$id => (string) $id];
            }
            
            return [$id => $alias];
            
//         $db         = $this->db;
//         $dbquery    = $db->getQuery(true);
        
//         $dbquery->select($dbquery->qn('alias'))
//             ->from($db->qn('#__jsports_teams'))
//             ->where('id = ' . $db->q($id));
        
//         $db->setQuery($dbquery);
            
//         $id .= ':' . $db->loadResult();
        
//         list($void, $segment) = explode(':', $id, 2);
     
//         return array($void => $segment);
    }
    
    
    public function getTeamId($segment, $query)
    {

        $db = $this->db;
        
        $q = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__jsports_teams'))
        ->where($db->quoteName('alias') . ' = :alias')
        ->bind(':alias', (string) $segment, ParameterType::STRING);
        
        $db->setQuery($q);
        $id = (int) $db->loadResult();
        
        return $id ?: false;
        
        
//         $db         = $this->db;
//         $dbquery    = $db->getQuery(true);
        
//         $dbquery->select($dbquery->qn('id'))
//         ->from($dbquery->qn('#__jsports_teams'))
//         ->where('alias = ' . $dbquery->q($segment));
        
//         $db->setQuery($dbquery);
        
//         if (!(int) $db->loadResult())
//         {
//             return false;
//         }
//         return (int) $db->loadResult();
    }
    
    
}
