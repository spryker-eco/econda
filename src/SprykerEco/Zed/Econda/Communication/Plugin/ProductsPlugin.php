<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Plugin;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacade getFacade()
 * @method \SprykerEco\Zed\Econda\EcondaConfig getConfig()
 * @method \SprykerEco\Zed\Econda\Communication\EcondaCommunicationFactory getFactory()
 */
class ProductsPlugin extends AbstractPlugin implements EcondaPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function run(
        LocaleTransfer $locale,
        BatchResultTransfer $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    ) {
        $csvDir = $this->getConfig()->getFileExportPath();
        if (!is_dir($csvDir)) {
            mkdir($csvDir);
        }
        $dataWriter->setFolderPath($csvDir);
        $this->getFacade()
            ->exportProducts(
                $locale,
                $result,
                $dataWriter,
                $output
            );
    }
}
