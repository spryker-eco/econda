<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface EcondaFacadeInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportCategories(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $result,
        FileWriterInterface $dataWriter,
        OutputInterface $output
    );

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportProducts(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $result,
        FileWriterInterface $dataWriter,
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
     * @api
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    public function exportFile(OutputInterface $output);
}
