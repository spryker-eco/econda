<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Dependency\Facade;

interface EcondaToPropelFacadeInterface
{
    /**
     * @return string
     */
    public function getCurrentDatabaseEngineName();
}
