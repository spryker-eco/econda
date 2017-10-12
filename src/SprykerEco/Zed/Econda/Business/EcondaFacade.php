<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use SprykerEco\Zed\Econda\Business\Collector\AbstractDatabaseCollector;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaBusinessFactory getFactory()
 */
class EcondaFacade extends AbstractFacade implements EcondaFacadeInterface
{

    /**
     * @api
     *
     * @param string $type
     * @param string $locale
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
        $collector = $this->getFactory()->createFileCategoryCollector();
        $this->export(
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
        $collector = $this->getFactory()->createFileProductCollector();
        $this->export(
            $collector,
            $localeTransfer,
            $result,
            $dataWriter,
            $output
        );
    }

    /**
     * @param \SprykerEco\Zed\Econda\Business\Collector\AbstractDatabaseCollector $collector
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    private function export(
        AbstractDatabaseCollector $collector,
        LocaleTransfer $localeTransfer,
        BatchResultInterface $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    ) {
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
