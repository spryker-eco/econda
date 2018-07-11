<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Reader\File;

interface FileReaderInterface
{
    /**
     * @param string $type
     * @param string $locale
     *
     * @return string
     */
    public function readFile($type, $locale);
}
