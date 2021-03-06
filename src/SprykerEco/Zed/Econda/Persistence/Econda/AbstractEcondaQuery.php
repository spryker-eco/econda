<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Econda;

use Generated\Shared\Transfer\LocaleTransfer;

abstract class AbstractEcondaQuery implements EcondaQueryInterface
{
    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $locale;

    /**
     * @return void
     */
    abstract protected function prepareQuery(): void;

    /**
     * @return $this
     */
    public function prepare()
    {
        $this->prepareQuery();

        return $this;
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getLocale(): LocaleTransfer
    {
        return $this->locale;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return $this
     */
    public function setLocale(LocaleTransfer $locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
