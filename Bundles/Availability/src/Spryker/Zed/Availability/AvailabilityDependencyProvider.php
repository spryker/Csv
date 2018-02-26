<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability;

use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToOmsBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToProductBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToTouchBridge;
use Spryker\Zed\Availability\Dependency\QueryContainer\AvailabilityToProductBridge as AvailabilityToProductQueryContainerBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AvailabilityDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_OMS = 'FACADE_OMS';
    const FACADE_STOCK = 'FACADE_STOCK';
    const FACADE_TOUCH = 'FACADE_TOUCH';
    const FACADE_PRODUCT = 'FACADE_PRODUCT';
    const FACADE_STORE = 'FACADE_STORE';

    const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addOmsFacade($container);
        $container = $this->addStockFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addStoreFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container = $this->addProductQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function addStoreFacade(Container $container)
    {
        $container[static::FACADE_STORE] = function (Container $container) {
            return new AvailabilityToStoreFacadeBridge($container->getLocator()->store()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container)
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return new AvailabilityToProductBridge($container->getLocator()->product()->facade());
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container)
    {
        $container[static::FACADE_TOUCH] = function (Container $container) {
            return new AvailabilityToTouchBridge($container->getLocator()->touch()->facade());
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStockFacade(Container $container)
    {
        $container[static::FACADE_STOCK] = function (Container $container) {
            return new AvailabilityToStockBridge($container->getLocator()->stock()->facade());
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container)
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return new AvailabilityToOmsBridge($container->getLocator()->oms()->facade());
        };
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductQueryContainer(Container $container)
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return new AvailabilityToProductQueryContainerBridge($container->getLocator()->product()->queryContainer());
        };
        return $container;
    }
}
