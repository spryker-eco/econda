<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Exception;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Econda\EcondaConstants;

class EcondaConfig extends AbstractBundleConfig
{
    public const ECONDA_CSV_DELIMITER = '|';
    public const ECONDA_CSV_CATEGORY_DELIMITER = '^^';

    /**
     * @return string
     */
    public function getHostYves(): string
    {
        return $this->get(ApplicationConstants::HOST_YVES);
    }

    /**
     * @return string
     */
    public function getFileExportPath(): string
    {
        return $this->get(EcondaConstants::ECONDA_CSV_FOLDER_PATH);
    }

    /**
     * @return string
     */
    public function getFileExportDelimiter(): string
    {
        return $this->get(EcondaConstants::ECONDA_CSV_DELIMITER);
    }

    /**
     * @param string $pdoEcondaQueryName
     * @param string $dbEngineName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getPdoEcondaQueryClassName($pdoEcondaQueryName, $dbEngineName): string
    {
        $data = [
            'MySql' => [

            ],
            'PostgreSql' => [

            ],
        ];

        if (!isset($data[$dbEngineName][$pdoEcondaQueryName])) {
            throw new Exception('Invalid PdoEcondaQueryName name: ' . $pdoEcondaQueryName);
        }

        return $data[$dbEngineName][$pdoEcondaQueryName];
    }
}
