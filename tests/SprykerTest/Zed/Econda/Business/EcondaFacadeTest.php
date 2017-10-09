<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Econda\Business;

use Codeception\Test\Unit;
use Spryker\Shared\Kernel\Store;
use SprykerEco\Shared\Econda\EcondaConstants;
use SprykerEco\Zed\Econda\Business\EcondaFacade;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;
use SprykerTest\Zed\Econda\EcondaBusinessTester;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group Econda
 * @group Business
 * @group Facade
 * @group EcondaFacadeTest
 * Add your own group annotations below this line
 */
class EcondaFacadeTest extends Unit
{
    const ECONDA_CSV_FOLDER_PATH = APPLICATION_ROOT_DIR . '/vendor/spryker-eco/econda/files';

    /**
     * @var array
     */
    private $exportTypes = [
        'products',
        'categories',
    ];

    /**
     * @var EcondaBusinessTester
     */
    protected $tester;

    /**
     * @var EcondaFacade
     */
    private $econdaFacade;

    /**
     * @var CsvNameGenerator
     */
    private $nameGenerator;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->econdaFacade = new EcondaFacade();
        $this->nameGenerator = new CsvNameGenerator();
        $this->removeCsvFolder();
    }

    /**
     * @return void
     */
    public function _after()
    {
        $this->removeCsvFolder();
    }

    /**
     * @return void
     */
    public function testExportFile()
    {
        $this->tester->setConfig(EcondaConstants::ECONDA_CSV_FOLDER_PATH, self::ECONDA_CSV_FOLDER_PATH);
        $outputMock = $this->createOutputMock();
        $this->econdaFacade->exportFile($outputMock);
        foreach ($this->getExports() as $export) {
            $file = $this->nameGenerator->generateFileName($export['type'], $export['locale']);
            $this->assertFileExists(self::ECONDA_CSV_FOLDER_PATH . '/' . $file);
            $fileContents = $this->econdaFacade->getFileContent($export['type'], $export['locale']);
            $this->assertGreaterThan(200, strlen($fileContents));
        }
    }

    /**
     * @return array
     */
    private function getExports()
    {
        $exports = [];
        $locales = Store::getInstance()->getLocales();
        foreach ($locales as $locale) {
            foreach ($this->exportTypes as $exportType) {
                $exports[] = [
                    'type' => $exportType,
                    'locale' => $locale,
                ];
            }
        }

        return $exports;
    }

    /**
     * @return void
     */
    private function removeCsvFolder()
    {
        if(is_dir(self::ECONDA_CSV_FOLDER_PATH)) {
            array_map('unlink', glob(self::ECONDA_CSV_FOLDER_PATH . "/*.*"));
            rmdir(self::ECONDA_CSV_FOLDER_PATH);
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createOutputMock()
    {
        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $outputFormatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $output->method('getFormatter')->willReturn($outputFormatter);

        return $output;
    }

}