<?php

/**
 * MIT License
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
 * @method \Spryker\Zed\Collector\CollectorConfig getConfig()
 * @method \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface getQueryContainer()
 */
class CategoryExporterPlugin extends AbstractPlugin implements ExporterPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
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
        $this->getFacade()
            ->exportCategories(
                $localeTransfer,
                $batchResultTransfer,
                $dataWriter,
                $output
            );
    }
}
