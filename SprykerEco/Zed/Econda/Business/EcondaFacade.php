<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaBusinessFactory getFactory()
 */
class EcondaFacade extends AbstractFacade implements EcondaFacadeInterface
{

    /**
     * @api
     *
     * @param string $locale
     * @param string $type
     *
     * @return mixed
     */
    public function getFileContent($type, $locale)
    {
        return $this->getFactory()->getEcondaCsvFileContent($type, $locale);
    }

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
    public function exportFile(OutputInterface $output)
    {
        $exporter = $this->getFactory()->createRunner();
        return $exporter->runExport($output);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportCategories(
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    ) {
        $collector = $this->getFactory()
            ->createFileCategoryCollector();

        $this->getFactory()
            ->createCollectorManager()
            ->runCollector(
                $collector,
                $localeTransfer,
                $result,
                $dataWriter,
                $output
            );
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function exportProducts(
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    ) {
        $collector = $this->getFactory()
            ->createFileProductCollector();

        $this->getFactory()
            ->createCollectorManager()
            ->runCollector(
                $collector,
                $localeTransfer,
                $result,
                $dataWriter,
                $output
            );
    }

}
