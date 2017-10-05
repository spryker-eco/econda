<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter\Writer\File;

use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;

class FileWriter implements WriterInterface
{

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface
     */
    protected $fileWriterAdapter;

    /**
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface $fileWriterAdapter
     */
    public function __construct(AdapterInterface $fileWriterAdapter)
    {
        $this->fileWriterAdapter = $fileWriterAdapter;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileWriterAdapter->setFileName($fileName);

        return $this;
    }

    /**
     * @param string $directory
     *
     * @return $this
     */
    public function setFolderPath($directory)
    {
        $this->fileWriterAdapter->setFolderPath($directory);

        return $this;
    }

    /**
     * @param array $dataSet
     * @param string $type
     *
     * @return bool
     */
    public function write(array $dataSet, $type = '')
    {
        return (bool)$this->fileWriterAdapter->write($dataSet, $type);
    }

}
