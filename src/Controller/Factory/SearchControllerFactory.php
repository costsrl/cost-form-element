<?php
/**
 * Created by PhpStorm.
 * User: renato
 * Date: 19/12/17
 * Time: 9.55
 */
namespace CostFormElement\Controller\Factory;

use CostFormElement\Controller\SearchController;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;


class SearchControllerFactory implements FactoryInterface
{
    /*
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return Translator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator      = $container->get('MvcTranslator');
        $searchController = new SearchController();
        $searchController->setServiceLocator($container);
        $searchController->setTranslator($translator);
        return $searchController;
    }

}