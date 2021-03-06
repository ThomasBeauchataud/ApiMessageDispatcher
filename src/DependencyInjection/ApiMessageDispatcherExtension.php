<?php


namespace ApiMessageDispatcher\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}.
 *
 * Class ApiMessageDispatcherExtension
 * @package ApiMessageDispatcher\DependencyInjection
 */
class ApiMessageDispatcherExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        foreach ($config as $key => $value) {
            $container->setParameter('api_message_dispatcher.'.$key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'api_message_dispatcher';
    }
}
