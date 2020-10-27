<?php


namespace ApiMessageDispatcher;


use ApiMessageDispatcher\DependencyInjection\ApiMessageDispatcherExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiMessageDispatcherBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @return ApiMessageDispatcherExtension
     */
    public function getContainerExtension()
    {
        $class = $this->getContainerExtensionClass();

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensionClass()
    {
        return ApiMessageDispatcherExtension::class;
    }
}
