<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter;

use Symfony\Component\Console\Output\OutputInterface;

interface RunnerInterface
{
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    public function runExport(OutputInterface $output);

    /**
     * @return array
     */
    public function getEnabledExports();
}
