<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileExporter extends AbstractExporter
{
    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
     */
    protected $writer;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
     */
    protected $csvNameGenerator;

    /**
     * @var string
     */
    protected $exportPath;

    /**
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $writer
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface $csvNameGenerator
     * @param string $exportPath
     * @param array $collectorPlugins
     */
    public function __construct(
        FileWriterInterface $writer,
        NameGeneratorInterface $csvNameGenerator,
        $exportPath,
        array $collectorPlugins = []
    ) {
        parent::__construct(
            $writer,
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
     * @return \Generated\Shared\Transfer\BatchResultTransfer
     */
    public function exportByType($type, LocaleTransfer $localeTransfer, OutputInterface $output): BatchResultTransfer
    {
        $result = $this->createDefaultBatchResultTransfer();
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
