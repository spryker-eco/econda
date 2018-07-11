<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Service\UtilDataReader\Model\BatchIterator\CountableIteratorInterface;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

interface DatabaseCollectorInterface
{
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
    );

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
    );
}
