<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Facade;

class EcondaToPriceBridge implements EcondaToPriceBridgeInterface
{

    /** @var \Spryker\Zed\Price\Business\PriceFacadeInterface */
    protected $facade;

    /**
     * @param \Spryker\Zed\Price\Business\PriceFacadeInterface $facade
     */
    public function __construct($facade)
    {
        $this->facade = $facade;
    }

    /**
     * @param string $sku
     * @param string|null $priceTypeName
     *
     * @return int
     */
    public function getPriceBySku($sku, $priceTypeName = null)
    {
        $this->facade->getPriceBySku($sku, $priceTypeName);
    }

}
