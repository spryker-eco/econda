<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\LocaleTransfer;
use Symfony\Component\Console\Output\OutputInterface;

interface ExporterInterface
{
    /**
     * @param string $type
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Generated\Shared\Transfer\BatchResultTransfer
     */
    public function exportByType($type, LocaleTransfer $locale, OutputInterface $output);

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\ExporterPluginInterface[]
     */
    public function getCollectorPlugins();
}
