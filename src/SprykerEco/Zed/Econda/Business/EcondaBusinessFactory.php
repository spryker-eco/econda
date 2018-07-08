<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business;

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
use SprykerEco\Zed\Econda\Business\Reader\File\CsvFileReader;
use SprykerEco\Zed\Econda\EcondaDependencyProvider;

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
    public function createEcondaCsvFileReader(): \SprykerEco\Zed\Econda\Business\Reader\File\FileReaderInterface
    {
        $nameGenerator = $this->createCsvNameGenerator();

        return new CsvFileReader($this->getConfig(), $nameGenerator);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\RunnerInterface
     */
    public function createRunner(): \SprykerEco\Zed\Econda\Business\Exporter\RunnerInterface
    {
        return new Runner(
            $this->getLocaleFacade(),
            $this->createFileExporter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface
     */
    public function createCollectorManager(): \SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface
    {
        return new CollectorManager(
            $this->createCriteriaBuilder(),
            $this->getQueryContainer(),
            $this->createProgressBarHelper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
     */
    protected function createFileWriter(): \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\FileWriterInterface
    {
        $csvFileWriterAdapter = $this->createCsvFileWriterAdapter();

        return new FileWriter($csvFileWriterAdapter);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface
     */
    protected function createProgressBarHelper(): \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface
    {
        return new ProgressBarHelper();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
     */
    protected function createCsvNameGenerator(): \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
    {
        return new CsvNameGenerator();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaCategoryCollector(): \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
    {
        return new EcondaCategoryCollector(
            $this->createCriteriaBuilder(),
            $this->createPdoEcondaQuery(static::CATEGORY_NODE_ECONDA_QUERY)
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaProductCollector(): \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
    {
        return new EcondaProductCollector(
            $this->createCriteriaBuilder(),
            $this->createPdoEcondaQuery('ProductConcreteEcondaQuery'),
            $this->getProductCategoryQueryContainer(),
            $this->getProductImageQueryContainer(),
            $this->getPriceProductFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface
     */
    protected function createFileExporter(): \SprykerEco\Zed\Econda\Business\Exporter\ExporterInterface
    {
        return new FileExporter(
            $this->createFileWriter(),
            $this->createCsvNameGenerator(),
            $this->getConfig()->getFileExportPath(),
            $this->getCollectorFileExporterPlugins()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface[]
     */
    protected function getCollectorFileExporterPlugins(): \SprykerEco\Zed\Econda\Dependency\Plugin\EcondaPluginInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FILE_PLUGINS);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface
     */
    protected function getLocaleFacade(): \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface
     */
    protected function createCsvFileWriterAdapter(): \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface
    {
        return new CsvAdapter(
            $this->getConfig()->getFileExportPath(),
            $this->getConfig()->getFileExportDelimiter()
        );
    }

    /**
     * @return \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected function getProductImageQueryContainer(): \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_IMAGE);
    }

    /**
     * @param string $pdoEcondaQueryName
     *
     * @return \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery
     */
    protected function createPdoEcondaQuery($pdoEcondaQueryName): \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery
    {
        $pdoEcondaQuery = $this->getConfig()->getPdoEcondaQueryClassName(
            $pdoEcondaQueryName,
            $this->getPropelFacade()->getCurrentDatabaseEngineName()
        );

        return new $pdoEcondaQuery();
    }

    /**
     * @return \Spryker\Zed\Propel\Business\PropelFacadeInterface
     */
    protected function getPropelFacade(): \Spryker\Zed\Propel\Business\PropelFacadeInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PROPEL);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface
     */
    protected function getPriceProductFacade(): \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    protected function createCriteriaBuilder(): \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
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
    protected function createCriteriaBuilderContainer(): \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer
    {
        return new CriteriaBuilderDependencyContainer();
    }

    /**
     * @return \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected function getProductCategoryQueryContainer(): \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_CATEGORY);
    }
}
