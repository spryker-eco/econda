<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Exception;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Econda\EcondaConstants;

class EcondaConfig extends AbstractBundleConfig
{
    const ECONDA_CSV_DELIMITER = '|';
    const ECONDA_CSV_CATEGORY_DELIMITER = '^^';

    /**
     * @return string
     */
    public function getHostYves()
    {
        return $this->get(ApplicationConstants::HOST_YVES);
    }

    /**
     * @return string
     */
    public function getFileExportPath()
    {
        return $this->get(EcondaConstants::ECONDA_CSV_FOLDER_PATH);
    }

    /**
     * @param string $pdoEcondaQueryName
     * @param string $dbEngineName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getPdoEcondaQueryClassName($pdoEcondaQueryName, $dbEngineName)
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
