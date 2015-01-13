<?php

namespace Core\ProductBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreProductExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $nws = ($container->hasParameter('minishop')) ? $container->getParameter('minishop') : array();
        $nws['product'] = true;
        $container->setParameter('minishop', $nws);
        $container->setParameter('admin.product.actions', array());

        if (isset($config['images'])) {
            $container->setParameter('product.images', $config['images']);
        }
        if (isset($config['tags'])) {
            $container->setParameter('product.tags', $config['tags']);
        }
        if (isset($config['prices'])) {
            $container->setParameter('product.prices', $config['prices']);
        }
        if (isset($config['process'])) {
            if (isset($config['process']['actions'])) {
                $container->setParameter('admin.product.actions', $config['process']['actions']);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
