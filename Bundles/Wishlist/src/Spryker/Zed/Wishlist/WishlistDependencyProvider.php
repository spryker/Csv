<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Wishlist;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Wishlist\Business\Operator\Add;
use Spryker\Zed\Wishlist\Business\Operator\Decrease;
use Spryker\Zed\Wishlist\Business\Operator\Increase;
use Spryker\Zed\Wishlist\Business\Operator\Remove;
use Spryker\Zed\Wishlist\Dependency\Facade\WishlistToLocaleBridge;
use Spryker\Zed\Wishlist\Dependency\Facade\WishlistToProductBridge;

class WishlistDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_LOCALE = 'FACADE_LOCALE';
    const FACADE_PRODUCT = 'FACADE_PRODUCT';


    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return new WishlistToProductBridge($container->getLocator()->product()->facade());
        };

        $container[static::FACADE_LOCALE] = function (Container $container) {
            return new WishlistToLocaleBridge($container->getLocator()->locale()->facade());
        };

        return $container;
    }

}
