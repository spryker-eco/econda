<?php

/**
 * MIT License
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
