<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Plugin;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EcondaPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    public function run(
        LocaleTransfer $locale,
        BatchResultTransfer $result,
        FileWriterInterface $dataWriter,
        OutputInterface $output
    );
}
