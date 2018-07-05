<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Econda\Communication\Plugin\CategoryPlugin;
use SprykerEco\Zed\Econda\Communication\Plugin\ProductsPlugin;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPropelFacadeBridge;

class EcondaDependencyProvider extends AbstractBundleDependencyProvider
{
    protected const FACADE_LOCALE = 'FACADE_LOCALE';
    protected const FACADE_PROPEL = 'FACADE_PROPEL';
    protected const FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';
    protected const QUERY_CONTAINER_PRODUCT_IMAGE = 'QUERY_CONTAINER_PRODUCT_IMAGE';
    protected const QUERY_CONTAINER_PRODUCT_CATEGORY = 'QUERY_CONTAINER_PRODUCT_CATEGORY';
    protected const FILE_PLUGINS = 'FILE_PLUGINS';
    protected const PRODUCTS = 'products';
    protected const CATEGORIES = 'categories';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return new EcondaToLocaleFacadeBridge($container->getLocator()->locale()->facade());
        };

        $container[static::FACADE_PROPEL] = function (Container $container) {
            return new EcondaToPropelFacadeBridge($container->getLocator()->propel()->facade());
        };

        $container[static::FACADE_PRICE_PRODUCT] = function (Container $container) {
            return new EcondaToPriceProductFacadeBridge($container->getLocator()->priceProduct()->facade());
        };

        $container[static::QUERY_CONTAINER_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        };

        $container[static::QUERY_CONTAINER_PRODUCT_CATEGORY] = function (Container $container) {
            return $container->getLocator()->productCategory()->queryContainer();
        };

        $container[static::FILE_PLUGINS] = function () {
            return [
                static::PRODUCTS => new ProductsPlugin(),
                static::CATEGORIES => new CategoryPlugin(),
            ];
        };

        return $container;
    }
}
