<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Facade;

class EcondaToPropelBridge implements EcondaToPropelBridgeInterface
{
    /** @var \Spryker\Zed\Propel\Business\PropelFacadeInterface */
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
    public function getCurrentDatabaseEngineName()
    {
        return $this->propelFacade->getCurrentDatabaseEngineName();
    }
}
