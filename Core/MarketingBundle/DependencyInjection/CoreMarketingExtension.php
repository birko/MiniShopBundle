<?php

namespace Core\MarketingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreMarketingExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $nws = ($container->hasParameter('minishop')) ? $container->getParameter('minishop') : array();
        $nws['marketing'] = true;
        $container->setParameter('minishop', $nws);

        if (!empty($config['google'])) {
            $container->setParameter('marketing.google', $config['google']);
        }

        if (!empty($config['facebook'])) {
            $container->setParameter('marketing.facebook', $config['facebook']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
