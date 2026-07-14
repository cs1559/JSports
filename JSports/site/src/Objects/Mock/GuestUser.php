<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Objects\Mock;

use Joomla\CMS\User\User;

class GuestUser extends User
{
    public function __construct(array $data = [])
    {
        // Guest by default
        $defaults = [
            'id'       => 0,
            'name'     => 'Guest',
            'username' => '',
            'email'    => '',
            'guest'    => 1,
            'groups'   => [],
            'params'   => '{}',
        ];
        
        $data = array_merge($defaults, $data);
        
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    
    public function isGuest(): bool
    {
        return (int) $this->id === 0;
    }
    
    public function authorise($action, $assetName = null): bool
    {
        // Simple mock logic
        return !$this->isGuest();
    }
}
