<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface;
use Spryker\Service\UtilDataReader\Model\BatchIterator\PdoBatchIterator;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractDatabaseCollector extends AbstractCollector implements DatabaseCollectorInterface
{
    /**
     * @var \SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery
     */
    protected $econdaPdoQuery;

    /**
     * @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected $criteriaBuilder;

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     * @param \SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery $econdaPdoQuery
     */
    public function __construct(
        CriteriaBuilderInterface $criteriaBuilder,
        AbstractEcondaPdoQuery $econdaPdoQuery
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->econdaPdoQuery = $econdaPdoQuery;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     * @param \Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface $queryContainer
     * @param int $chunkSize
     *
     * @return \Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface
     */
    public function createIteratorAndPrepareQuery(
        LocaleTransfer $locale,
        CriteriaBuilderInterface $criteriaBuilder,
        QueryContainerInterface $queryContainer,
        $chunkSize = 100
    ): CountableIteratorInterface {
        $this->econdaPdoQuery
            ->setCriteriaBuilder($criteriaBuilder)
            ->setLocale($locale)
            ->prepare();

        return new PdoBatchIterator($criteriaBuilder, $queryContainer, $chunkSize);
    }

    /**
     * @param \Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface $batchCollection
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResult
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $storeWriter
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Symfony\Component\Console\Helper\ProgressBar $progressBar
     *
     * @return void
     */
    public function exportDataToStore(
        CountableIteratorInterface $batchCollection,
        BatchResultTransfer $batchResult,
        FileWriterInterface $storeWriter,
        LocaleTransfer $locale,
        OutputInterface $output,
        ProgressBar $progressBar
    ): void {
        $totalCount = $batchCollection->count();
        $batchResult->setTotalCount($totalCount);

        $progressBar->setMessage($this->collectResourceType(), 'barTitle');

        foreach ($batchCollection as $batch) {
            $this->processBatchForExport(
                $batch,
                $progressBar,
                $locale,
                $batchResult,
                $storeWriter
            );
        }

        $progressBar->finish();
        $output->writeln('');
    }

    /**
     * @param array $batch
     * @param \Symfony\Component\Console\Helper\ProgressBar $progressBar
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResult
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $storeWriter
     *
     * @return void
     */
    protected function processBatchForExport(
        array $batch,
        ProgressBar $progressBar,
        LocaleTransfer $locale,
        BatchResultTransfer $batchResult,
        FileWriterInterface $storeWriter
    ): void {
        $batchSize = count($batch);
        $progressBar->advance($batchSize);

        $collectedData = $this->collectData($batch, $locale);
        $collectedDataCount = count($collectedData);

        $storeWriter->write($collectedData);

        $batchResult->setProcessedCount($batchResult->getProcessedCount() + $collectedDataCount);
    }
}
