<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace  SprykerEco\Zed\Econda\Business\Reader\File;

use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface;
use SprykerEco\Zed\Econda\EcondaConfig;

class CsvFileReader implements FileReaderInterface
{
    /**
     * @var \SprykerEco\Zed\Econda\EcondaConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
     */
    protected $csvNameGenerator;

    /**
     * @param \SprykerEco\Zed\Econda\EcondaConfig $config
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface $csvNameGenerator
     */
    public function __construct(EcondaConfig $config, NameGeneratorInterface $csvNameGenerator)
    {
        $this->config = $config;
        $this->csvNameGenerator = $csvNameGenerator;
    }

    /**
     * @param string $type
     * @param string $locale
     *
     * @return string
     */
    public function readFile($type, $locale): string
    {
        $fileName = $this->csvNameGenerator->generateFileName($type, $locale);

        $directory = $this->config->getFileExportPath();

        return file_get_contents(
            $directory . DIRECTORY_SEPARATOR . $fileName
        );
    }
}
