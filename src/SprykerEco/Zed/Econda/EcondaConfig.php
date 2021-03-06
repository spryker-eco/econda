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
use SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql\CategoryNodeEcondaQuery as StorageCategoryNodeEcondaQuery;
use SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql\ProductConcreteEcondaQuery as StorageProductConcreteEcondaQuery;

class EcondaConfig extends AbstractBundleConfig
{
    protected const ECONDA_CSV_DELIMITER = '|';
    protected const ECONDA_CSV_CATEGORY_DELIMITER = '^^';

    /**
     * @api
     *
     * @return string
     */
    public function getHostYves(): string
    {
        return $this->get(ApplicationConstants::HOST_YVES);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getFileExportPath(): string
    {
        return $this->get(EcondaConstants::CSV_FOLDER_PATH);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getCsvDelimiter(): string
    {
        return static::ECONDA_CSV_DELIMITER;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getCsvCategoryDelimiter(): string
    {
        return static::ECONDA_CSV_CATEGORY_DELIMITER;
    }

    /**
     * @api
     *
     * @param string $dbEngineName
     * @param string $econdaPdoQueryName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getEcondaPdoQueryClassName($dbEngineName, $econdaPdoQueryName): string
    {
        $data = [
            'PostgreSql' => [
                'CategoryNodeEcondaQuery' => StorageCategoryNodeEcondaQuery::class,
                'ProductConcreteEcondaQuery' => StorageProductConcreteEcondaQuery::class,
            ],
        ];

        if (!isset($data[$dbEngineName][$econdaPdoQueryName])) {
            throw new Exception('Invalid EcondaPdoQueryName name: ' . $econdaPdoQueryName);
        }

        return $data[$dbEngineName][$econdaPdoQueryName];
    }
}
