<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves;

use Spryker\Yves\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Econda\EcondaConstants;

class EcondaConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->get(EcondaConstants::ACCOUNT_ID);
    }
}
