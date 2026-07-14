<?php 
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace FP4P\Component\JSports\Administrator\Extension;
          
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for JSports
 *
 */
      
class JSportsComponent extends MVCComponent implements
BootableExtensionInterface, RouterServiceInterface
{
    //use RouterServiceTrait;
    use HTMLRegistryAwareTrait;
    use RouterServiceTrait;
    

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param   ContainerInterface  $container  The container
     *
     * @return  void
     *
     */
    public function boot(ContainerInterface $container)
    {
        //$this->getRegistry()->register('jsportsadministrator', new AdministratorService);
    }
}