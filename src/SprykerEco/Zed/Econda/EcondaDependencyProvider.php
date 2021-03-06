<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Econda\Communication\Plugin\CategoryExporterPlugin;
use SprykerEco\Zed\Econda\Communication\Plugin\ProductExporterPlugin;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeBridge;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPropelFacadeBridge;

class EcondaDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_LOCALE = 'FACADE_LOCALE';
    public const FACADE_PROPEL = 'FACADE_PROPEL';
    public const FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';
    public const QUERY_CONTAINER_PRODUCT_IMAGE = 'QUERY_CONTAINER_PRODUCT_IMAGE';
    public const QUERY_CONTAINER_PRODUCT_CATEGORY = 'QUERY_CONTAINER_PRODUCT_CATEGORY';
    public const FILE_PLUGINS = 'FILE_PLUGINS';
    public const PRODUCTS_PLUGIN = 'ProductsPlugin';
    public const CATEGORIES_PLUGIN = 'CategoryPlugin';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new EcondaToLocaleFacadeBridge($container->getLocator()->locale()->facade());
        });

        $container->set(static::FACADE_PROPEL, function (Container $container) {
            return new EcondaToPropelFacadeBridge($container->getLocator()->propel()->facade());
        });

        $container->set(static::FACADE_PRICE_PRODUCT, function (Container $container) {
            return new EcondaToPriceProductFacadeBridge($container->getLocator()->priceProduct()->facade());
        });

        $container->set(static::QUERY_CONTAINER_PRODUCT_IMAGE, function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        });

        $container->set(static::QUERY_CONTAINER_PRODUCT_CATEGORY, function (Container $container) {
            return $container->getLocator()->productCategory()->queryContainer();
        });

        $container->set(static::FILE_PLUGINS, $this->getFilePlugins());

        return $container;
    }

    /**
     * @return array
     */
    public function getFilePlugins(): array
    {
        return [
            static::PRODUCTS_PLUGIN => new ProductExporterPlugin(),
            static::CATEGORIES_PLUGIN => new CategoryExporterPlugin(),
        ];
    }
}
