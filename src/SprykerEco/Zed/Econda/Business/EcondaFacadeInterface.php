<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EcondaFacadeInterface
{

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportCategories(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    );

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportProducts(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    );

    /**
     * @api
     *
     * @param string $type
     * @param string $locale
     *
     * @return string
     */
    public function getFileContent($type, $locale);

    /**
     * Specification:
     * - Initiates export into a file
     *
     * @api
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface[]
     */
    public function exportFile(OutputInterface $output);

}
