<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\FailedResultTransfer;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;

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
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
     */
    protected $writer;

    /**
     * @var \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $queryContainer;

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
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    public function getCollectorPlugins(): EcondaPluginInterface
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
