<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Econda;

use Generated\Shared\Transfer\LocaleTransfer;

abstract class AbstractEcondaQuery
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
    public function prepare(): self
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
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function setLocale(LocaleTransfer $locale): LocaleTransfer
    {
        $this->locale = $locale;

        return $this;
    }
}
