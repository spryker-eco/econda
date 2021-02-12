<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Manager;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface;
use SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CollectorManager implements CollectorManagerInterface
{
    /**
     * @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected $criteriaBuilder;

    /**
     * @var \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface
     */
    protected $progressBarHelper;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    protected $databaseCollector;

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     * @param \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface $queryContainer
     * @param \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface $progressBarHelper
     * @param \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface $databaseCollector
     */
    public function __construct(
        CriteriaBuilderInterface $criteriaBuilder,
        EcondaQueryContainerInterface $queryContainer,
        ProgressBarHelperInterface $progressBarHelper,
        DatabaseCollectorInterface $databaseCollector
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->queryContainer = $queryContainer;
        $this->progressBarHelper = $progressBarHelper;
        $this->databaseCollector = $databaseCollector;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResultTransfer
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function runCollector(
        LocaleTransfer $localeTransfer,
        BatchResultTransfer $batchResultTransfer,
        FileWriterInterface $dataWriter,
        OutputInterface $output
    ): void {
        $batchCollection = $this->databaseCollector
            ->createIteratorAndPrepareQuery($localeTransfer, $this->criteriaBuilder, $this->queryContainer);
        $progressBar = $this->progressBarHelper->startProgressBar($output, '', $batchCollection->count());
        $this->databaseCollector
            ->exportDataToStore($batchCollection, $batchResultTransfer, $dataWriter, $localeTransfer, $output, $progressBar);
    }
}
