<?php

namespace Core\CommonBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreCommonExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $nws = ($container->hasParameter('minishop')) ? $container->getParameter('minishop') : array();
        $nws['common'] = true;
        $container->setParameter('minishop', $nws);

        if (isset($config['emails'])) {
            $container->setParameter('default.emails', $config['emails']);
        }

        if (isset($config['cultures'])) {
            $container->setParameter('core.cultures', $config['cultures']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
