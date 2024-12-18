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
namespace FP4P\Component\JSports\Site\Objects\Adapters;

require_once(JPATH_ADMINISTRATOR.'/components/com_nspro/lib.php');

use Joomla\CMS\Factory;

class NSProAdapter
{
    
    var $defaultlist = 1;
    
    /*
     * NOTES FROM DEVELOPER:
     * 
     * First of all, NS Pro can import subscribers directly from Joomla users, with one click, in case you somehow need that information.
If you want to import your custom data from another table programmatically, all you have to do is:
1. Require our library file: 
require_once(JPATH_ADMINISTRATOR.'/components/com_nspro/lib.php');

2. Use the add_or_update_subscriber function:
add_or_update_subscriber($name, $email, $datetime, $conf, $mailing_lists, $update = true, $source = 'component', $source_id = 'auto')
$conf can be 'send' to send a confirmation message or anything trueish, including 1, T, TRUE, YES, Y, CONFIRMED (all of these are case-insensitive) or a boolean true.
Of course, it can also be anything else (non-null please) that translates to false.
$mailing_lists is an array of mailing list IDs. A comma separated string works as well.
$update indicates whether to update an existing subscriber or not.
$source and $source_id are only required if you want to send a confirmation message, in order to use the specific options of a $source (component or module) with its respective $source_id.

     */
    
    
    /**
     * 
     */
    public function getContactsFromSource()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $sql = "SELECT * FROM `#__jsports_rosters` WHERE length(email)> 0 
                and programid in (select id from #__jsports_programs where status = 'A');";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
    }

    
    public function alreadyOnList($email, $listid = null) {
        
        if (is_null($listid)) {
            $listid = $this->defaultlist;
        }
        
        //select * from jos2823_nspro_subs where id in (select subscriber_id from jos2823_nspro_sub_lists where list_id = 1); 
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $sql = "select * from #__nspro_subs where lower(email) = lower('" . $email . "') and id in (select subscriber_id from #__nspro_sub_lists where list_id = " . $listid . ")";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return count($db->loadAssocList());
        
    }
    
    public function addSubscriber($name, $email) {
        
        $datetime = date("Y-m-d H:i:s");
        $mailinglist = array($this->defaultlist);
        
        //add_or_update_subscriber($name, $email, $datetime, 0, $mailing_lists, $update = true, $source = 'component', $source_id = 'auto')
        add_or_update_subscriber($name, $email, $datetime, 0, $this->defaultlist);
        
    }
    
    
    
    
}
