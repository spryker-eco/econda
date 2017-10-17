<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Econda\Business\Exporter\Writer\File\NameGenerator;

use Codeception\Test\Unit;
use SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator\CsvNameGenerator;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group Econda
 * @group Business
 * @group Exporter
 * @group Writer
 * @group File
 * @group NameGenerator
 * @group CsvNameGeneratorTest
 * Add your own group annotations below this line
 */
class CsvNameGeneratorTest extends Unit
{
    /**
     * @return void
     */
    public function testGenerateFileName()
    {
        $generator = new CsvNameGenerator();
        $fileName = $generator->generateFileName('products', 'en_US');

        $this->assertSame('products_en_US.csv', $fileName); //only thing we can check if it is a valid folder
    }
}
