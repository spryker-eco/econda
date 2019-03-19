<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Business\Collector;

use Generated\Shared\Transfer\LocaleTransfer;

abstract class AbstractCollector
{
    /**
     * @var \SprykerEco\Zed\Econda\Persistence\Econda\EcondaPdoQueryInterface
     */
    protected $econdaPdoQuery;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $locale;

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    abstract protected function collectItem(array $collectItemData): array;

    /**
     * @param array $collectedSet
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return array
     */
    abstract protected function collectData(array $collectedSet, LocaleTransfer $localeTransfer): array;

    /**
     * @return string
     */
    abstract protected function collectResourceType();
}
