<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Facade;

class EcondaToPriceProductFacadeBridge implements EcondaToPriceProductFacadeInterface
{
    /**
     * @var \Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface
     */
    protected $facade;

    /**
     * @param \Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface $facade
     */
    public function __construct($facade)
    {
        $this->facade = $facade;
    }

    /**
     * @param string $sku
     * @param string|null $priceTypeName
     *
     * @return int|null
     */
    public function findPriceBySku($sku, $priceTypeName = null): ?int
    {
        return $this->facade->findPriceBySku($sku, $priceTypeName);
    }
}
