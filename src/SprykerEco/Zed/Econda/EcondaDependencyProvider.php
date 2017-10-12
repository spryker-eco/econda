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
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPropelBridge;

class EcondaDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_LOCALE = 'facade_locale';
    const FACADE_PROPEL = 'facade_propel';
    const FACADE_PRICE = 'facade_price';
    const QUERY_CONTAINER_PRODUCT_IMAGE = 'query_container_product_image';
    const QUERY_CONTAINER_PRODUCT_CATEGORY = 'QUERY_CONTAINER_PRODUCT_CATEGORY';
    const FILE_PLUGINS = 'file plugins';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return new EcondaToLocaleBridge($container->getLocator()->locale()->facade());
        };

        $container[self::FACADE_PROPEL] = function (Container $container) {
            return new EcondaToPropelBridge($container->getLocator()->propel()->facade());
        };

        $container[self::FACADE_PRICE] = function (Container $container) {
            return new EcondaToPriceBridge($container->getLocator()->price()->facade());
        };

        $container[self::QUERY_CONTAINER_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_PRODUCT_CATEGORY] = function (Container $container) {
            return $container->getLocator()->productCategory()->queryContainer();
        };

        $container[self::FILE_PLUGINS] = function () {
            return [
                'products' => new ProductsPlugin(),
                'categories' => new CategoryPlugin(),
            ];
        };

        return $container;
    }

}
