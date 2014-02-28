<?php

namespace Core\ShopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreShopExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $nws = ($container->hasParameter('minishop')) ? $container->getParameter('minishop') : array();
        $nws['shop'] = true;
        $container->setParameter('minishop', $nws);

        $container->setParameter('address.required', array());
        $container->setParameter('admin.order.export', array());
        $container->setParameter('admin.order.proccess', array());

        if (isset($config['address'])) {
            if (isset($config['address']['required'])) {
                $container->setParameter('address.required', $config['address']['required']);
            }
        }
        if (isset($config['order'])) {
            if (isset($config['order']['process'])) {
                $container->setParameter('admin.order.process', $config['order']['process']);
            }
            if (isset($config['order']['export'])) {
                $container->setParameter('admin.order.export', $config['order']['export']);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
