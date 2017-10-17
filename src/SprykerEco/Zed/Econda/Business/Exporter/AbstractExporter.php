<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\FailedResultTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;

abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    protected $collectorPlugins = [];

    /**
     * @var \Generated\Shared\Transfer\FailedResultTransfer
     */
    protected $failedResultTransfer;

    /**
     * @var \Generated\Shared\Transfer\BatchResultTransfer
     */
    protected $batchResultTransfer;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface
     */
    protected $writer;

    /**
     * @var \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * AbstractExporter constructor.
     *
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $writer
     * @param \Generated\Shared\Transfer\FailedResultTransfer $failedResultTransfer
     * @param \Generated\Shared\Transfer\BatchResultTransfer $batchResultTransfer
     * @param array $collectorPlugins
     */
    public function __construct(
        WriterInterface $writer,
        FailedResultTransfer $failedResultTransfer,
        BatchResultTransfer $batchResultTransfer,
        array $collectorPlugins = []
    ) {
        $this->writer = $writer;
        $this->failedResultTransfer = $failedResultTransfer;
        $this->batchResultTransfer = $batchResultTransfer;
        $this->collectorPlugins = $collectorPlugins;
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    public function getCollectorPlugins()
    {
        return $this->collectorPlugins;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isCollectorRegistered($type)
    {
        return array_key_exists($type, $this->collectorPlugins);
    }

    /**
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     *
     * @return void
     */
    protected function resetResult(BatchResultTransfer $result)
    {
        $result->setProcessedCount(0);
        $result->setIsFailed(false);
        $result->setTotalCount(0);
        $result->setDeletedCount(0);
    }

    /**
     * @return \Generated\Shared\Transfer\BatchResultTransfer
     */
    protected function getBatchResultTransfer()
    {
        $resultBatchTransfer = clone $this->batchResultTransfer;
        $resultBatchTransfer->setDeletedCount(0);
        $resultBatchTransfer->setFailedCount(0);

        return $resultBatchTransfer;
    }
}
