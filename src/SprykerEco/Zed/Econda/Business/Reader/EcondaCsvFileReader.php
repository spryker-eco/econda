<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace  SprykerEco\Zed\Econda\Business\Reader;

use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;
use SprykerEco\Zed\Econda\EcondaConfig;

class EcondaCsvFileReader implements EcondaCsvFileReaderInterface
{

    /** @var \SprykerEco\Zed\Econda\EcondaConfig */
    protected $config;

    /** @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator */
    protected $csvNameGenerator;

    /**
     * EcondaCsvFileReader constructor.
     *
     * @param \SprykerEco\Zed\Econda\EcondaConfig $config
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator $csvNameGenerator
     */
    public function __construct(EcondaConfig $config, CsvNameGenerator $csvNameGenerator)
    {
        $this->config = $config;
        $this->csvNameGenerator = $csvNameGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($type, $locale)
    {
        $fileName = $this->csvNameGenerator->generateFileName($type, $locale);

        $directory = $this->config->getFileExportPath();

        return file_get_contents(
            $directory . DIRECTORY_SEPARATOR . $fileName
        );
    }

}
