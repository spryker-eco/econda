<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Helper;

use Symfony\Component\Console\Output\OutputInterface;

interface ProgressBarHelperInterface
{
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $resourceType
     * @param int $totalCount
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function startProgressBar(
        OutputInterface $output,
        $resourceType,
        $totalCount
    );
}
