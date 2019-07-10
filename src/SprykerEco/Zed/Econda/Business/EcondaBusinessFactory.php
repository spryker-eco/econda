<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Business;

use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactory;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface;
use SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface;
use SprykerEco\Zed\Econda\Business\Collector\File\EcondaCategoryCollector;
use SprykerEco\Zed\Econda\Business\Collector\File\EcondaProductCollector;
use SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface;
use SprykerEco\Zed\Econda\Business\Exporter\FileExporter;
use SprykerEco\Zed\Econda\Business\Exporter\Runner;
use SprykerEco\Zed\Econda\Business\Exporter\RunnerInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\CsvAdapter;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriter;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface;
use SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelper;
use SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface;
use SprykerEco\Zed\Econda\Business\Manager\CollectorManager;
use SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface;
use SprykerEco\Zed\Econda\Business\Reader\File\CsvFileReader;
use SprykerEco\Zed\Econda\Business\Reader\File\FileReaderInterface;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPropelFacadeBridge;
use SprykerEco\Zed\Econda\EcondaDependencyProvider;
use SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery;

/**
 * @method \SprykerEco\Zed\Econda\EcondaConfig getConfig()
 * @method \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface getQueryContainer()
 */
class EcondaBusinessFactory extends AbstractBusinessFactory
{
    protected const CATEGORY_NODE_ECONDA_QUERY = 'CategoryNodeEcondaQuery';
    protected const CRITERIA_BUILDER_FACTORY_WORKER = 'CriteriaBuilderFactoryWorker';

    /**
     * @return \SprykerEco\Zed\Econda\Business\Reader\File\FileReaderInterface
     */
    public function createEcondaCsvFileReader(): FileReaderInterface
    {
        $nameGenerator = $this->createCsvNameGenerator();

        return new CsvFileReader($this->getConfig(), $nameGenerator);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\RunnerInterface
     */
    public function createRunner(): RunnerInterface
    {
        return new Runner(
            $this->getLocaleFacade(),
            $this->createFileExporter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface
     */
    public function createCategoryCollectorManager(): CollectorManagerInterface
    {
        return new CollectorManager(
            $this->createCriteriaBuilder(),
            $this->getQueryContainer(),
            $this->createProgressBarHelper(),
            $this->createEcondaCategoryCollector()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface
     */
    public function createProductCollectorManager(): CollectorManagerInterface
    {
        return new CollectorManager(
            $this->createCriteriaBuilder(),
            $this->getQueryContainer(),
            $this->createProgressBarHelper(),
            $this->createEcondaProductCollector()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
     */
    protected function createFileWriter(): FileWriterInterface
    {
        $csvFileWriterAdapter = $this->createCsvFileWriterAdapter();

        return new FileWriter($csvFileWriterAdapter);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface
     */
    protected function createProgressBarHelper(): ProgressBarHelperInterface
    {
        return new ProgressBarHelper();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
     */
    protected function createCsvNameGenerator(): NameGeneratorInterface
    {
        return new CsvNameGenerator();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaCategoryCollector(): DatabaseCollectorInterface
    {
        return new EcondaCategoryCollector(
            $this->createCriteriaBuilder(),
            $this->createEcondaPdoQuery(static::CATEGORY_NODE_ECONDA_QUERY)
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaProductCollector(): DatabaseCollectorInterface
    {
        return new EcondaProductCollector(
            $this->createCriteriaBuilder(),
            $this->createEcondaPdoQuery('ProductConcreteEcondaQuery'),
            $this->getProductCategoryQueryContainer(),
            $this->getProductImageQueryContainer(),
            $this->getPriceProductFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface
     */
    protected function createFileExporter(): ExporterInterface
    {
        return new FileExporter(
            $this->createFileWriter(),
            $this->createCsvNameGenerator(),
            $this->getConfig()->getFileExportPath(),
            $this->getCollectorFileExporterPlugins()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\ExporterPluginInterface[]
     */
    protected function getCollectorFileExporterPlugins(): array
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FILE_PLUGINS);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface
     */
    protected function getLocaleFacade(): EcondaToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface
     */
    protected function createCsvFileWriterAdapter(): AdapterInterface
    {
        return new CsvAdapter(
            $this->getConfig()->getFileExportPath(),
            $this->getConfig()->getCsvDelimiter()
        );
    }

    /**
     * @return \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected function getProductImageQueryContainer(): ProductImageQueryContainerInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_IMAGE);
    }

    /**
     * @param string $econdaPdoQueryName
     *
     * @return \SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery
     */
    protected function createEcondaPdoQuery($econdaPdoQueryName): AbstractEcondaPdoQuery
    {
        $econdaPdoQuery = $this->getConfig()->getEcondaPdoQueryClassName(
            $this->getPropelFacade()->getCurrentDatabaseEngineName(),
            $econdaPdoQueryName
        );

        return new $econdaPdoQuery();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPropelFacadeBridge
     */
    protected function getPropelFacade(): EcondaToPropelFacadeBridge
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PROPEL);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface
     */
    protected function getPriceProductFacade(): EcondaToPriceProductFacadeInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected function createCriteriaBuilder(): CriteriaBuilderInterface
    {
        $factory = new CriteriaBuilderFactory(
            $this->createCriteriaBuilderContainer()
        );

        $factory->registerWorkerCallback(static::CRITERIA_BUILDER_FACTORY_WORKER, function () use ($factory) {
            return $factory->buildWorker(CriteriaBuilderFactoryWorker::class);
        });

        /** @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker $factoryWorker */
        $factoryWorker = $factory->getWorkerByName(static::CRITERIA_BUILDER_FACTORY_WORKER);

        return $factoryWorker->buildCriteriaBuilder();
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer
     */
    protected function createCriteriaBuilderContainer(): CriteriaBuilderDependencyContainer
    {
        return new CriteriaBuilderDependencyContainer();
    }

    /**
     * @return \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected function getProductCategoryQueryContainer(): ProductCategoryQueryContainerInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_CATEGORY);
    }
}
