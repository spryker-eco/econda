<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Plugin;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacade getFacade()
 * @method \Spryker\Zed\Collector\CollectorConfig getConfig()
 */
class CategoryPlugin extends AbstractPlugin implements EcondaPluginInterface
{

    /**
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
