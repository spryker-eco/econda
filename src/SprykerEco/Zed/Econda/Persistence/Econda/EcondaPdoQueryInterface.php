<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Econda;

use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;

interface EcondaPdoQueryInterface
{
    /**
     * @return \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface
     */
    public function getCriteriaBuilder();

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteriaBuilder
     *
     * @return $this
     */
    public function setCriteriaBuilder(CriteriaBuilderInterface $criteriaBuilder);
}
