<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Helper;

use Spryker\Shared\Gui\ProgressBar\ProgressBarBuilder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressBarHelper implements ProgressBarHelperInterface
{
    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar;

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
    ): ProgressBar {
        $this->progressBar = $this->generateProgressBar($output, $totalCount, $resourceType);
        $this->progressBar->start();
        $this->progressBar->advance(0);

        return $this->progressBar;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $count
     * @param string $resourceType
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    protected function generateProgressBar(OutputInterface $output, $count, $resourceType): ProgressBar
    {
        $builder = new ProgressBarBuilder($output, $count, $resourceType);

        return $builder->build();
    }
}
