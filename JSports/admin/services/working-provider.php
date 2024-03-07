<?php
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\FP4P\\Component\\JSports'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\FP4P\\Component\\JSports'));
       // $container->registerServiceProvider(new RouterFactory('\\FP4P\\Component\\JSports'));
        $container->set(
            ComponentInterface::class,
            function (Container $container)
            {
                $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                
                return $component;
        }
        );
    }
};

