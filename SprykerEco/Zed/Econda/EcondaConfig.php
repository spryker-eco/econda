<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda;

use Spryker\Shared\Application\ApplicationConstants;
use SprykerEco\Shared\Econda\EcondaConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

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
            ],
        ];

        return $data[$dbEngineName];
    }

}
