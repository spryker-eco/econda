<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
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
     * @return string
     */
    public function getFileContent($type, $locale)
    {
        return $this->getFactory()->createEcondaCsvFileReader()->readFile($type, $locale);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    public function exportFile(OutputInterface $output)
    {
        return $this->getFactory()->createRunner()->runExport($output);
    }

    /**
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
    ) {
        $collector = $this->getFactory()->createEcondaCategoryCollector();
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
    ) {
        $collector = $this->getFactory()->createEcondaProductCollector();
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
