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
 * @method \Spryker\Zed\Collector\CollectorConfig getConfig()
 */
class CategoryPlugin extends AbstractPlugin implements EcondaPluginInterface
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
        $this->getFacade()
            ->exportCategories(
                $locale,
                $result,
                $dataWriter,
                $output
            );
    }

}
