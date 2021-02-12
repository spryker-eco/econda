<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Manager;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CollectorManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResultTransfer
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function runCollector(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $batchResultTransfer,
        FileWriterInterface $dataWriter,
        OutputInterface $output
    );
}
