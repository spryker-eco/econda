<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Exporter\Writer\File\NameGenerator;

interface NameGeneratorInterface
{

    /**
     * @return string
     */
    public function generateFileName($type, $localeName, $number = '');

}
