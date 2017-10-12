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
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper;
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

    /** @var \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper */
    protected $progressBarHelper;

    /**
     * CollectorManager constructor.
     *
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     * @param \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface $queryContainer
     * @param \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper $progressBarHelper
     */
    public function __construct(
        CriteriaBuilderInterface $criteriaBuilder,
        EcondaQueryContainerInterface $queryContainer,
        ProgressBarHelper $progressBarHelper
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->queryContainer = $queryContainer;
        $this->progressBarHelper = $progressBarHelper;
    }

    /**
     * @param \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface $collector
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $dataWriter
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function runCollector(
        DatabaseCollectorInterface $collector,
        LocaleTransfer $locale,
        BatchResultTransfer $result,
        WriterInterface $dataWriter,
        OutputInterface $output
    ) {
        $batchCollection = $collector->createIteratorAndPrepareQuery($locale, $this->criteriaBuilder, $this->queryContainer);
        $progressBar = $this->progressBarHelper->startProgressBar($output, '', $batchCollection->count());

        $collector->exportDataToStore($batchCollection, $result, $dataWriter, $locale, $output, $progressBar);
    }

}
