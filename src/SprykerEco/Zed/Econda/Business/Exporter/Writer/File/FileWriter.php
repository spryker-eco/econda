<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter\Writer\File;

use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface;

class FileWriter implements FileWriterInterface
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
        return $this->fileWriterAdapter->write($dataSet, $type);
    }
}
