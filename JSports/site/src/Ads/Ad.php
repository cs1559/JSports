<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Ads
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Ads;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\CampaignService;
use Joomla\CMS\Router\Route;

/**
 * This is an abstract class that defines an common attributes and functions for an "ad".  Specifically, it 
 * provides a common function that returns a signed URL string that can be rendered to help prevent BOTS or spiders
 * from triggering a "click" event.
 * 
 * @author Chris Strieter
 *
 */
abstract class Ad
{
    /** @var int $id */
    public $id = 0;
    
    /** @var string $type */
    public $type = '';
    
    /**
     * Copy of the original data that was used to load the object. 
     * @var array $data 
     */
    public $data;
    
    /**
     * Layout format of the ad. 
     * @var string $layout 
     */
    public $layout = '';
    
    /**
     * This function will returned a SIGNED URL (to prevent BOTS, spiders, etc.)
     * @param string $controller
     * @return string
     */
    public function getClickUrl(string $controller = null) : string {
                  
        if (empty($controller)) {
            $controller = strtolower((new \ReflectionClass($this))->getShortName());
        }
        
        $params = ComponentHelper::getParams('com_jsports');
        $secret = $params->get('secretkey', "jsports");
               
        $id = $this->id;
        $ts = time();
        
        $token = hash_hmac('sha256', $id . '|' . $ts, $secret);
        $urlstring = "index.php?option=com_jsports&task={$controller}.click&id={$id}&ts={$ts}&sig={$token}";
        $clickurl = Route::_($urlstring);   
        return $clickurl;
    }
    

    /**
     * The KEY ID for the specific ad.  This typically represents the primary key from the Sponsors
     * or Campaign table.
     * 
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The AD Title.
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Defines a function that each subclass must implement that will return the layout template when rendering an ad.
     */
    abstract public function getLayout();
    
    /**
     * This function loads an array into the specific ad object.
     * @return void
     */
    public function loadObject($source) : void {
        $this->data = $source;
        foreach (get_object_vars($source) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        
    }
    

}

