<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacade getFacade()
 */
class EcondaFileExportConsole extends Console
{

    const COMMAND_NAME = 'econda:file:export';
    const COMMAND_DESCRIPTION = 'Export data to files';

    /**
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    public function getHelperSet()
    {
        return new HelperSet();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
    protected function buildSummary(array $resultData)
    {
        if (empty($resultData)) {
            return PHP_EOL . '<fg=yellow>Nothing exported.</fg=yellow>' . PHP_EOL;
        }

        $summary = PHP_EOL;

        ksort($resultData);
        foreach ($resultData as $type => $result) {
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
                $result->getSuccessCount() > 0 ? '<fg=green>' . $result->getSuccessCount() . '</fg=green>' : $result->getSuccessCount(),
                $result->getDeletedCount() > 0 ? '<fg=yellow>' . $result->getDeletedCount() . '</fg=yellow>' : $result->getDeletedCount(),
                $result->isFailed() ? '<fg=red>' . $result->getFailedCount() . '</fg=red>' : $result->getFailedCount()
            );

            $summary .= PHP_EOL;
        }

        return $summary . PHP_EOL;
    }

    /**
     * @param \SprykerEco\Zed\Econda\Business\Model\BatchResultInterface[] $results
     *
     * @return string
     */
    protected function buildNestedSummary(array $results)
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
