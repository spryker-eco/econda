<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter;

interface AdapterInterface
{
    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName);

    /**
     * @param array $dataSet
     * @param string $type
     *
     * @return bool
     */
    public function write(array $dataSet, $type = '');

    /**
     * @param string $directory
     *
     * @return $this
     */
    public function setFolderPath($directory);
}
