<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use FP4P\Component\JSports\Administrator\Extension\JSportsComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\MVCComponent;

return new class implements ServiceProviderInterface
{
    
    public function register(Container $container)
    {
        $container->registerServiceProvider(new CategoryFactory('\\FP4P\\Component\\JSports'));
        $container->registerServiceProvider(new MVCFactory('\\FP4P\\Component\\JSports'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\FP4P\\Component\\JSports'));
        $container->registerServiceProvider(new RouterFactory('\\FP4P\\Component\\JSports'));
        $container->set(
            ComponentInterface::class,
            function (Container $container)
            {
            
                $component = new JSportsComponent($container->get(ComponentDispatcherFactoryInterface::class));
                
//                 $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                return $component;
        }
        );
    }
};

