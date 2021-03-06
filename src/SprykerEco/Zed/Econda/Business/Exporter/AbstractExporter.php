<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\FailedResultTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;

abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \SprykerEco\Zed\Econda\Dependency\Plugin\ExporterPluginInterface[]
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
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
     */
    protected $writer;

    /**
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface $writer
     * @param array $collectorPlugins
     */
    public function __construct(
        FileWriterInterface $writer,
        array $collectorPlugins = []
    ) {
        $this->writer = $writer;
        $this->collectorPlugins = $collectorPlugins;
        $this->failedResultTransfer = new FailedResultTransfer();
        $this->batchResultTransfer = new BatchResultTransfer();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\ExporterPluginInterface[]
     */
    public function getCollectorPlugins(): array
    {
        return $this->collectorPlugins;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isCollectorRegistered($type): bool
    {
        return array_key_exists($type, $this->collectorPlugins);
    }

    /**
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     *
     * @return void
     */
    protected function resetResult(BatchResultTransfer $result): void
    {
        $result->setProcessedCount(0);
        $result->setIsFailed(false);
        $result->setTotalCount(0);
        $result->setDeletedCount(0);
    }

    /**
     * @return \Generated\Shared\Transfer\BatchResultTransfer
     */
    protected function createDefaultBatchResultTransfer(): BatchResultTransfer
    {
        $resultBatchTransfer = clone $this->batchResultTransfer;
        $resultBatchTransfer->setDeletedCount(0);
        $resultBatchTransfer->setFailedCount(0);

        return $resultBatchTransfer;
    }
}
