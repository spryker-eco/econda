<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter;

use SplFileObject;
use SprykerEco\Zed\Econda\Business\Exporter\Exception\FileWriterException;

class CsvAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    protected $folderPath;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $escape;

    /**
     * @var \SplFileObject|null
     */
    protected $csvFile;

    /**
     * @param string $directory
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($directory, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->folderPath = $directory;

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * @param array $data
     * @param string $type
     *
     * @return bool
     */
    public function write(array $data, $type = ''): bool
    {
        $result = false;
        $csvFile = $this->getCsvFile($data);
        foreach ($data as $key => $row) {
            $result = $csvFile->fputcsv($row, $this->delimiter) !== false ? true : false;
        }

        return $result;
    }

    /**
     * @param string $folderPath
     *
     * @return $this
     */
    public function setFolderPath($folderPath)
    {
        $this->folderPath = $folderPath;
        $this->csvFile = null;

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        $this->csvFile = null;

        return $this;
    }

    /**
     * @param \SplFileObject $csvFile
     * @param array $dataRow
     *
     * @return void
     */
    protected function initializeHeaderColumns(SplFileObject $csvFile, array $dataRow): void
    {
        $csvFile->fputcsv(array_keys($dataRow), $this->delimiter);
    }

    /**
     * @param array $data
     *
     * @return \SplFileObject
     */
    protected function getCsvFile(array $data): SplFileObject
    {
        if (!$this->csvFile) {
            $this->csvFile = new SplFileObject($this->getAbsolutePath(), 'w');
            $this->csvFile->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

            $this->initializeHeaderColumns($this->csvFile, current($data));
        }

        return $this->csvFile;
    }

    /**
     * @throws \SprykerEco\Zed\Econda\Business\Exporter\Exception\FileWriterException
     *
     * @return string
     */
    protected function getAbsolutePath(): string
    {
        if (!$this->folderPath) {
            throw new FileWriterException('Path to export file to not set properly');
        }
        if (!$this->fileName) {
            throw new FileWriterException('File name to export to not set properly');
        }

        $absolutePath = $this->folderPath . DIRECTORY_SEPARATOR . $this->fileName;

        return $absolutePath;
    }
}
