<?php

/**
 * MIT License
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
use SprykerEco\Zed\Econda\Business\Reader\EcondaCsvFileReader;
use SprykerEco\Zed\Econda\EcondaDependencyProvider;

/**
 * @method \SprykerEco\Zed\Econda\EcondaConfig getConfig()
 * @method \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainer getQueryContainer()
 */
class EcondaBusinessFactory extends AbstractBusinessFactory
{
    const CRITERIA_BUILDER_FACTORY_WORKER = 'CriteriaBuilderFactoryWorker';

    /**
     * @return \SprykerEco\Zed\Econda\Business\Reader\EcondaCsvFileReaderInterface
     */
    public function createEcondaCsvFileReader()
    {
        $nameGenerator = $this->createCsvNameGenerator();

        return new EcondaCsvFileReader($this->getConfig(), $nameGenerator);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\RunnerInterface
     */
    public function createRunner()
    {
        return new Runner(
            $this->getLocaleFacade(),
            $this->createFileExporter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Manager\CollectorManagerInterface
     */
    public function createCollectorManager()
    {
        $criteriaBuilder = $this->createCriteriaBuilder();
        $queryContainer = $this->getQueryContainer();
        $progressBarHelper = $this->createProgressBarHelper();

        return new CollectorManager($criteriaBuilder, $queryContainer, $progressBarHelper);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\WriterInterface
     */
    protected function createFileWriter()
    {
        $csvFileWriterAdapter = $this->createCsvFileWriterAdapter();

        return new FileWriter($csvFileWriterAdapter);
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Helper\ProgressBarHelperInterface
     */
    protected function createProgressBarHelper()
    {
        return new ProgressBarHelper();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\NameGeneratorInterface
     */
    protected function createCsvNameGenerator()
    {
        return new CsvNameGenerator();
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaCategoryCollector()
    {
        return new EcondaCategoryCollector(
            $this->createCriteriaBuilder(),
            $this->createPdoEcondaQuery('CategoryNodeEcondaQuery')
        );
    }

    /**
     * @return \SprykerEco\Zed\Econda\Business\Collector\DatabaseCollectorInterface
     */
    public function createEcondaProductCollector()
    {
        return new EcondaProductCollector(
            $this->createCriteriaBuilder(),
            $this->createPdoEcondaQuery('ProductConcreteEcondaQuery'),
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
     * @return \SprykerEco\Zed\Econda\Business\Exporter\Writer\File\Adapter\AdapterInterface
     */
    protected function createCsvFileWriterAdapter()
    {
        return new CsvAdapter($this->getConfig()->getFileExportPath());
    }

    /**
     * @return \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected function getProductImageQueryContainer()
    {
        return $this->getProvidedDependency(EcondaDependencyProvider::QUERY_CONTAINER_PRODUCT_IMAGE);
    }

    /**
     * @param string $pdoEcondaQueryName
     *
     * @return \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery
     */
    protected function createPdoEcondaQuery($pdoEcondaQueryName)
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

        $factory->registerWorkerCallback(self::CRITERIA_BUILDER_FACTORY_WORKER, function () use ($factory) {
            return $factory->buildWorker(CriteriaBuilderFactoryWorker::class);
        });

        /** @var \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker $factoryWorker */
        $factoryWorker = $factory->getWorkerByName(self::CRITERIA_BUILDER_FACTORY_WORKER);

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
