<?php

namespace SURFnet\OATHBundle\DependencyInjection;

use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SURFnetOATHExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $storage = $container->getParameter('surfnet_oath.userstorage');
        switch ($storage ["type"]) {
            case 'pdo':
                $loader->load('pdo.yml');
                break;

            case 'pdohsm':
                $loader->load('pdohsm.yml');
                break;

            default:
                throw new RuntimeException('Unknown storage type: ' . $storage ["type"]);
        }
    }
}
