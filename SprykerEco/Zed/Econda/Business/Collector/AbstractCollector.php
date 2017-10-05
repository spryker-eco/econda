<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector;

use Generated\Shared\Transfer\LocaleTransfer;

abstract class AbstractCollector
{

    /**
     * @var \SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaQuery
     */
    protected $queryBuilder;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $locale;

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    abstract protected function collectItem(array $collectItemData);

    /**
     * @param array $collectedSet
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return array
     */
    abstract protected function collectData(array $collectedSet, LocaleTransfer $locale);

    /**
     * @return string
     */
    abstract protected function collectResourceType();

}
