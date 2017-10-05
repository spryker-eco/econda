<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Dependency\Plugin;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EcondaPluginInterface
{

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(
        LocaleTransfer $locale,
        BatchResultInterface $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    );

}
