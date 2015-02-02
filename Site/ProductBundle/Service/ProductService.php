<?php

namespace Site\ProductBundle\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Core\CategoryBundle\Entity\Category;

class ProductService
{
    private $requestStack = null;
    private $em = null;
    private $container = null;

    public function __construct(RequestStack $requestStack, EntityManager $entityManager, Container $container)
    {
        $this->requestStack = $requestStack;
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function loadResources($productIds = array(), $result = array(), $params = array())
    {
        $minishop  = $this->container->getParameter('minishop');
        $result['minishop'] = $minishop;
        $result['media'] = array();
        $result['ordered'] = array();
        $result['stocks'] = array();
        $result['prices'] = array();
        if (!empty($productIds)) {
            $result['media'] = $this->em ->getRepository("CoreProductBundle:ProductMedia")->getProductsMediasArray($productIds);
            $result['stocks'] = $this->em ->getRepository("CoreProductBundle:Stock")->getStocksArray($productIds);
            $result['prices'] = $this->em ->getRepository("CoreProductBundle:Price")->getPricesArray($productIds);
            if (array_key_exists("shop", $minishop) && $minishop['shop']) {
                $result['ordered']  = $this->em ->getRepository("CoreShopBundle:OrderItem")->getProductsOrderCount($productIds);
            }
        }
        $services =  $this->container->getParameter('site_product.listservices');
        foreach ($services as $key => $service) {
            if ($this->container->has($service)) {
                $result = $this->container->get($service)->loadResources($productIds, $result, $params);
            }
        }

        return $result;
    }
}
