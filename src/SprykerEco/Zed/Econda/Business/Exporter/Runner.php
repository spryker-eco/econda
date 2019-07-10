<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Business\Exporter;

use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\Kernel\Store;
use SprykerEco\Zed\Econda\Business\Exporter\Exception\BatchResultException;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Runner implements RunnerInterface
{
    /**
     * @var \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface
     */
    protected $exporter;

    /**
     * @var \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @param \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface $localeFacade
     * @param \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface $exporter
     */
    public function __construct(
        EcondaToLocaleFacadeInterface $localeFacade,
        ExporterInterface $exporter
    ) {
        $this->localeFacade = $localeFacade;
        $this->exporter = $exporter;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    public function runExport(OutputInterface $output): array
    {
        $storeCollection = Store::getInstance()->getAllowedStores();

        $results = [];

        foreach ($storeCollection as $storeName) {
            $output->writeln('');
            $output->writeln('<fg=yellow>----------------------------------------</fg=yellow>');
            $output->writeln(sprintf(
                '<fg=yellow>Exporting Store:</fg=yellow> <fg=white>%s</fg=white>',
                $storeName
            ));
            $output->writeln('');

            $localeCollection = Store::getInstance()->getLocalesPerStore($storeName);
            foreach ($localeCollection as $localeCode) {
                $localeTransfer = $this->localeFacade->getLocale($localeCode);
                $results[$storeName . '@' . $localeCode] = $this->exportStoreByLocale($localeTransfer, $output);
            }
        }

        return $results;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return array
     */
    protected function exportStoreByLocale(LocaleTransfer $locale, OutputInterface $output): array
    {
        $results = [];
        $types = $this->getEnabledExports();

        $output->writeln('');
        $output->writeln(sprintf('<fg=yellow>Locale:</fg=yellow> <fg=white>%s</fg=white>', $locale->getLocaleName()));
        $output->writeln('<fg=yellow>-------------</fg=yellow>');

        foreach ($types as $type) {
            $result = $this->exporter->exportByType($type, $locale, $output);

            $this->handleResult($result);

            if ($result !== null) {
                if ($this->nothingWasProcessed($result)) {
                    continue;
                }
                $results[$type] = $result;
            }
        }

        return $results;
    }

    /**
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     *
     * @return bool
     */
    protected function nothingWasProcessed(BatchResultTransfer $result): bool
    {
        return $result->getProcessedCount() === 0;
    }

    /**
     * @param \Generated\Shared\Transfer\BatchResultTransfer $result
     *
     * @throws \SprykerEco\Zed\Econda\Business\Exporter\Exception\BatchResultException
     *
     * @return void
     */
    protected function handleResult(BatchResultTransfer $result): void
    {
        if ($result->getFailedCount()) {
            throw new BatchResultException(
                sprintf(
                    'Processed %d from %d for locale %s, where %d were deleted and %d failed.',
                    $result->getProcessedCount(),
                    $result->getTotalCount(),
                    $result->getProcessedLocale()->getLocaleName(),
                    $result->getDeletedCount(),
                    $result->getFailedCount()
                )
            );
        }
    }

    /**
     * @return array
     */
    public function getEnabledExports(): array
    {
        return array_keys($this->exporter->getCollectorPlugins());
    }
}
