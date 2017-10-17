<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

use Exception;
use Generated\Shared\Transfer\BatchResultTransfer;
use Generated\Shared\Transfer\FailedResultTransfer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactory;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Econda\Business\Collector\File\EcondaCategoryCollector;
use SprykerEco\Zed\Econda\Business\Collector\File\EcondaProductCollector;
use SprykerEco\Zed\Econda\Business\Exporter\FileExporter;
use SprykerEco\Zed\Econda\Business\Exporter\Runner;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\CsvAdapter;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriter;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;
use SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper;
use SprykerEco\Zed\Econda\Business\Manager\CollectorManager;
use SprykerEco\Zed\Econda\Business\Reader\EcondaCsvFileReader;
use SprykerEco\Zed\Econda\EcondaDependencyProvider;
use SprykerEco\Zed\Econda\Persistence\EcondaQueryContainer;

/**
 * @method \SprykerEco\Zed\Econda\EcondaConfig getConfig()
 * @method \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainer getQueryContainer()
 */
class EcondaBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Econda\Business\Reader\EcondaCsvFileReader
     */
    public function createEcondaCsvFileReader()
    {
        $nameGenerator = $this->createCsvNameGenerator();

        return new EcondaCsvFileReader($this->getConfig(), $nameGenerator);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Runner
     */
    public function createRunner()
    {
        return new Runner(
            $this->getLocaleFacade(),
            $this->createFileExporter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Manager\CollectorManager
     */
    public function createCollectorManager()
    {
        $criteriaBuilder = $this->createCriteriaBuilder();
        $queryContainer = $this->getQueryContainer();
        $progressBarHelper = $this->createProgressBarHelper();
        return new CollectorManager($criteriaBuilder, $queryContainer, $progressBarHelper);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriter
     */
    protected function createFileWriter()
    {
        $csvFileWriterAdapter = $this->createCsvFileWriterAdapter();
        $fileWriter = new FileWriter($csvFileWriterAdapter);

        return $fileWriter;
    }

    /**
     * @return \Generated\Shared\Transfer\BatchResultTransfer
     */
    protected function createBatchResultModel()
    {
        return new BatchResultTransfer();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper
     */
    protected function createProgressBarHelper()
    {
        return new ProgressBarHelper();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator
     */
    protected function createCsvNameGenerator()
    {
        return new CsvNameGenerator();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\File\EcondaCategoryCollector
     */
    public function createEcondaCategoryCollector()
    {
        return new EcondaCategoryCollector(
            $this->createCriteriaBuilder(),
            $this->createStoragePdoQueryAdapterByName('CategoryNodeEcondaQuery')
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\File\EcondaProductCollector
     */
    public function createEcondaProductCollector()
    {
        return new EcondaProductCollector(
            $this->createCriteriaBuilder(),
            $this->createStoragePdoQueryAdapterByName('ProductConcreteEcondaQuery'),
            $this->getProductCategoryQueryContainer(),
            $this->getProductImageQueryContainer(),
            $this->getPriceFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface
     */
    protected function createFileExporter()
    {
        return new FileExporter(
            $this->createFileWriter(),
            new FailedResultTransfer(),
            $this->createBatchResultModel(),
            $this->createCsvNameGenerator(),
            $this->getConfig()->getFileExportPath(),
            $this->getCollectorFileExporterPlugins()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    protected function getCollectorFileExporterPlugins()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FILE_PLUGINS);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleInterface
     */
    protected function getLocaleFacade()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\CsvAdapter
     */
    protected function createCsvFileWriterAdapter()
    {
        return new CsvAdapter($this->getConfig()->getFileExportPath());
    }

    /**
     * @return string
     */
    protected function getCurrentDatabaseEngineName()
    {
        return $this->getPropelFacade()->getCurrentDatabaseEngineName();
    }

    /**
     * @return \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected function getProductImageQueryContainer()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_IMAGE);
    }

    /**
     * @param string $name
     *
     * @throws \Exception
     *
     * @return \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery
     */
    protected function createStoragePdoQueryAdapterByName($name)
    {
        $classList = $this->getConfig()->getStoragePdoQueryAdapterClassNames(
            $this->getCurrentDatabaseEngineName()
        );
        if (!array_key_exists($name, $classList)) {
            throw new Exception('Invalid StoragePdoQueryAdapter name: ' . $name);
        }

        $queryBuilderClassName = $classList[$name];

        return new $queryBuilderClassName();
    }

    /**
     * @return \Spryker\Zed\Propel\Business\PropelFacadeInterface
     */
    protected function getPropelFacade()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PROPEL);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceBridgeInterface
     */
    protected function getPriceFacade()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PRICE);
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected function createCriteriaBuilder()
    {
        $factory = new CriteriaBuilderFactory(
            $this->createCriteriaBuilderContainer()
        );

        $factory->registerWorkerCallback('CriteriaBuilderFactoryWorker', function () use ($factory) {
            return $factory->buildWorker(CriteriaBuilderFactoryWorker::class);
        });

        /** @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker $factoryWorker */
        $factoryWorker = $factory->getWorkerByName('CriteriaBuilderFactoryWorker');

        return $factoryWorker->buildCriteriaBuilder();
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer
     */
    protected function createCriteriaBuilderContainer()
    {
        return new CriteriaBuilderDependencyContainer();
    }

    /**
     * @return \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected function getProductCategoryQueryContainer()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_CATEGORY);
    }
}
