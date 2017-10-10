<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Econda\EcondaConstants;
use SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql\CategoryNodeEcondaQuery;
use SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql\ProductConcreteEcondaQuery;

class EcondaConfig extends AbstractBundleConfig
{

    const ECONDA_CSV_DELIMITER = "|";
    const ECONDA_CSV_CATEGORY_DELIMITER = "^^";

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
     * @param string $dbEngineName
     *
     * @return array
     */
    public function getStoragePdoQueryAdapterClassNames($dbEngineName)
    {
        $data = [
            'MySql' => [

            ],
            'PostgreSql' => [
                'CategoryNodeEcondaQuery' => CategoryNodeEcondaQuery::class,
                'ProductConcreteEcondaQuery' => ProductConcreteEcondaQuery::class,
            ],
        ];

        return $data[$dbEngineName];
    }

}
