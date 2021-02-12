<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Econda;

use Generated\Shared\Transfer\LocaleTransfer;

interface EcondaQueryInterface
{
    /**
     * @return $this
     */
    public function prepare();

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getLocale();

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return $this
     */
    public function setLocale(LocaleTransfer $locale);
}
