<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Plugin;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use SprykerEco\Zed\Econda\Dependency\Plugin\ExporterPluginInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Econda\EcondaConfig getConfig()
 */
class ProductExporterPlugin extends AbstractPlugin implements ExporterPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResultTransfer
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $batchResultTransfer,
        FileWriterInterface $dataWriter,
        OutputInterface $output
    ): void {
        $csvDir = $this->getConfig()->getFileExportPath();
        if (!is_dir($csvDir)) {
            mkdir($csvDir);
        }
        $dataWriter->setFolderPath($csvDir);
        $this->getFacade()
            ->exportProducts(
                $localeTransfer,
                $batchResultTransfer,
                $dataWriter,
                $output
            );
    }
}
