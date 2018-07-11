<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Facade;

class EcondaToPropelFacadeBridge implements EcondaToPropelFacadeInterface
{
    /**
     * @var \Spryker\Zed\Propel\Business\PropelFacadeInterface
     */
    protected $propelFacade;

    /**
     * @param \Spryker\Zed\Propel\Business\PropelFacadeInterface $propelFacade
     */
    public function __construct($propelFacade)
    {
        $this->propelFacade = $propelFacade;
    }

    /**
     * @return string
     */
    public function getCurrentDatabaseEngineName(): string
    {
        return $this->propelFacade->getCurrentDatabaseEngineName();
    }
}
