<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacadeInterface getFacade()
 */
class EcondaFileExportConsole extends Console
{
    protected const COMMAND_NAME = 'econda:file:export';

    protected const COMMAND_DESCRIPTION = 'Export data to files';

    /**
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    public function getHelperSet(): HelperSet
    {
        return new HelperSet();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $exportResults = $this->getFacade()->exportFile($output);
        $message = $this->buildNestedSummary($exportResults);
        $message = '<info>' . $message . '</info>';

        $output->write($message);
    }

    /**
     * @param array $resultData
     *
     * @return string
     */
    protected function buildSummary(array $resultData): string
    {
        if (empty($resultData)) {
            return PHP_EOL . '<fg=yellow>Nothing exported.</fg=yellow>' . PHP_EOL;
        }

        $summary = PHP_EOL;

        ksort($resultData);
        foreach ($resultData as $type => $result) {
            $successCount = $result->getTotalCount() - $result->getFailedCount();
            $summary .= sprintf(
                ' <fg=green>%s</fg=green><fg=yellow> </fg=yellow><fg=yellow></fg=yellow>' . PHP_EOL .
                ' <fg=white>Total: %d</fg=white>' . PHP_EOL .
                ' <fg=white>Processed: %d</fg=white>' . PHP_EOL .
                ' <fg=white>Succeeded: %s</fg=white>' . PHP_EOL .
                ' <fg=white>Deleted: %s</fg=white>' . PHP_EOL .
                ' <fg=white>Failed: %s </fg=white>' . PHP_EOL,
                mb_strtoupper($type),
                $result->getTotalCount(),
                $result->getProcessedCount(),
                $successCount > 0 ? '<fg=green>' . $successCount . '</fg=green>' : $successCount,
                $result->getDeletedCount() > 0 ? '<fg=yellow>' . $result->getDeletedCount() . '</fg=yellow>' : $result->getDeletedCount(),
                $result->getFailedCount() ? '<fg=red>' . $result->getFailedCount() . '</fg=red>' : $result->getFailedCount()
            );

            $summary .= PHP_EOL;
        }

        return $summary . PHP_EOL;
    }

    /**
     * @param array $results
     *
     * @return string
     */
    protected function buildNestedSummary(array $results): string
    {
        $summary = '';
        foreach ($results as $localeName => $summaryData) {
            $summary .= PHP_EOL;
            $summary .= '<fg=yellow>----------------------------------------</fg=yellow>';
            $summary .= PHP_EOL;
            $summary .= sprintf('<fg=yellow>Summary:</fg=yellow> <fg=white>%s</fg=white>', $localeName);
            $summary .= PHP_EOL;
            $summary .= $this->buildSummary($summaryData);
        }

        $summary .= PHP_EOL . 'All done.' . PHP_EOL;

        return $summary;
    }
}
