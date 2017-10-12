<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Manager;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CollectorManagerInterface
{

    /**
     * @param \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface $collector
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function runCollector(
        DatabaseCollectorInterface $collector,
        LocaleTransfer $locale,
        BatchResultTransfer $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    );

}
