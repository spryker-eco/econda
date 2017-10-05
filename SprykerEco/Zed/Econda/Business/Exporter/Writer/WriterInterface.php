<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter\Writer;

interface WriterInterface
{

    /**
     * @param array $dataSet
     * @param string $type
     *
     * @return bool
     */
    public function write(array $dataSet, $type = '');

    /**
     * @param string $csvDir
     *
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface
     */
    public function setFolderPath($directory);

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName);

}
