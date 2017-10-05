<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
