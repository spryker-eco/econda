<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface;
use SprykerEco\Zed\Econda\Business\Model\BatchResultInterface;
use SprykerEco\Zed\Econda\Business\Model\FailedResultInterface;

abstract class AbstractExporter implements ExporterInterface
{

    /**
     * @var \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    protected $collectorPlugins = [];

    /**
     * @var \SprykerEco\Zed\Econda\Business\Model\FailedResultInterface
     */
    protected $failedResultPrototype;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface
     */
    protected $batchResultPrototype;

    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface
     */
    protected $writer;

    /**
     * @var \Spryker\Zed\Touch\Persistence\TouchQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface $writer
     * @param \SprykerEco\Zed\Econda\Business\Model\FailedResultInterface $failedResultPrototype
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $batchResultPrototype
     * @param \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[] $collectorPlugins
     */
    public function __construct(
        WriterInterface $writer,
        FailedResultInterface $failedResultPrototype,
        BatchResultInterface $batchResultPrototype,
        array $collectorPlugins = []
    ) {
        $this->writer = $writer;
        $this->failedResultPrototype = $failedResultPrototype;
        $this->batchResultPrototype = $batchResultPrototype;
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
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface $result
     *
     * @return void
     */
    protected function resetResult(BatchResultInterface $result)
    {
        $result->setProcessedCount(0);
        $result->setIsFailed(false);
        $result->setTotalCount(0);
        $result->setDeletedCount(0);
    }

}
