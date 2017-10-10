<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use SprykerEco\Zed\Econda\Business\Model\FailedResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileExporter extends AbstractExporter
{

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriter
     */
    protected $writer;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator
     */
    protected $csvNameGenerator;

    /**
     * @var string
     */
    protected $exportPath;

    /**
     * FileExporter constructor.
     *
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $writer
     * @param \SprykerEco\Zed\Econda\Business\Model\FailedResultInterface $failedResultPrototype
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $batchResultPrototype
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator $csvNameGenerator
     * @param string $exportPath
     * @param array $collectorPlugins
     */
    public function __construct(
        WriterInterface $writer,
        FailedResultInterface $failedResultPrototype,
        BatchResultInterface $batchResultPrototype,
        CsvNameGenerator $csvNameGenerator,
        $exportPath,
        array $collectorPlugins = []
    ) {
        parent::__construct(
            $writer,
            $failedResultPrototype,
            $batchResultPrototype,
            $collectorPlugins
        );

        $this->csvNameGenerator = $csvNameGenerator;
        $this->exportPath = $exportPath;
    }

    /**
     * @param string $type
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface
     */
    public function exportByType($type, LocaleTransfer $localeTransfer, OutputInterface $output)
    {
        $result = clone $this->batchResultPrototype;
        $result->setProcessedLocale($localeTransfer);

        if (!$this->isCollectorRegistered($type)) {
            $this->resetResult($result);

            return $result;
        }

        $fileName = $this->csvNameGenerator->generateFileName($type, $localeTransfer->getLocaleName());
        $this->writer->setFileName($fileName);
        $this->writer->setFolderPath($this->exportPath);

        $collectorPlugin = $this->collectorPlugins[$type];
        $collectorPlugin->run($localeTransfer, $result, $this->writer, $output);

        return $result;
    }

}
