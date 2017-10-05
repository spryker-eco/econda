<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface;
use Spryker\Service\UtilDataReader\Model\BatchIterator\PdoBatchIterator;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractDatabaseCollector extends AbstractCollector implements DatabaseCollectorInterface
{

    /**
     * @var \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery
     */
    protected $queryBuilder;

    /**
     * @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected $criteriaBuilder;

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteria
     * @param \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery $query
     */
    public function __construct(
        CriteriaBuilderInterface $criteria,
        AbstractPdoEcondaQuery $query
    ) {
        $this->criteriaBuilder = $criteria;
        $this->queryBuilder = $query;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     * @param \Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface $connection
     * @param int $chunkSize
     *
     * @return \Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface
     */
    public function createIteratorAndPrepareQuery(
        LocaleTransfer $locale,
        CriteriaBuilderInterface $criteriaBuilder,
        QueryContainerInterface $connection,
        $chunkSize = 100
    ) {
        $this->queryBuilder
            ->setCriteriaBuilder($criteriaBuilder)
            ->setLocale($locale)
            ->prepare();

        return new PdoBatchIterator($criteriaBuilder, $connection, $chunkSize);
    }

    /**
     * @param \Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface $batchCollection
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $batchResult
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $storeWriter
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Symfony\Component\Console\Helper\ProgressBar $progressBar
     *
     * @return void
     */
    public function exportDataToStore(
        CountableIteratorInterface $batchCollection,
        BatchResultInterface $batchResult,
        WriterInterface $storeWriter,
        LocaleTransfer $locale,
        OutputInterface $output,
        ProgressBar $progressBar
    ) {

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
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $batchResult
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $storeWriter
     *
     * @return void
     */
    protected function processBatchForExport(
        array $batch,
        ProgressBar $progressBar,
        LocaleTransfer $locale,
        BatchResultInterface $batchResult,
        WriterInterface $storeWriter
    ) {
        $batchSize = count($batch);
        $progressBar->advance($batchSize);

        $collectedData = $this->collectData($batch, $locale);
        $collectedDataCount = count($collectedData);

        $storeWriter->write($collectedData);

        $batchResult->increaseProcessedCount($collectedDataCount);
    }

}
