<?php

namespace Site\ProductBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SiteProductExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if (isset($config['per_page'])) {
            $container->setParameter("site.product.perpage", $config['per_page']);
        }
        if (isset($config['recursive'])) {
            $container->setParameter("site.product.recursive", $config['recursive']);
        }
        if (isset($config['show_disabled'])) {
            $container->setParameter("site.product.show_disabled", $config['show_disabled']);
        }
        
        $services = ($container->hasParameter('site_product.listservices')) ? $container->getParameter('site_product.listservices') : array();
        $container->setParameter('site_product.listservices', $services);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
