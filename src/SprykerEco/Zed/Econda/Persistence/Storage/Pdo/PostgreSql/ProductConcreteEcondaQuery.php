<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql;

use SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery;

class ProductConcreteEcondaQuery extends AbstractEcondaPdoQuery
{
    /**
     * @return void
     */
    protected function prepareQuery(): void
    {
        $sql = '
SELECT
 spy_product.id_product AS id_product,
 spy_product.sku AS sku,
 spy_product_localized_attributes.name AS name,
 spy_product.attributes AS attributes,
 spy_product_abstract.attributes AS abstract_attributes,
 spy_product_abstract.id_product_abstract AS id_product_abstract,
 spy_product.attributes AS concrete_attributes,
 spy_product_abstract_localized_attributes.attributes AS abstract_localized_attributes,
 spy_product_abstract_localized_attributes.meta_title AS meta_title,
 spy_product_abstract_localized_attributes.meta_keywords AS meta_keywords,
 spy_product_abstract_localized_attributes.meta_description AS meta_description,
 spy_product_localized_attributes.description AS concrete_description,
 spy_product_localized_attributes.attributes AS concrete_localized_attributes,
 spy_product_abstract_localized_attributes.description as abstract_description,
 spy_url.url AS url,
 (SELECT SUM(spy_stock_product.quantity)
   FROM spy_stock_product
   WHERE spy_stock_product.fk_product = spy_product.id_product) AS quantity
FROM spy_product
 INNER JOIN spy_product_abstract ON (spy_product_abstract.id_product_abstract = spy_product.fk_product_abstract)
 INNER JOIN spy_product_localized_attributes ON (spy_product_localized_attributes.fk_product = spy_product.id_product)
 INNER JOIN spy_locale ON (spy_locale.id_locale = :fk_locale_1 and spy_locale.id_locale = spy_product_localized_attributes.fk_locale)
 INNER JOIN spy_product_abstract_localized_attributes ON (spy_product_abstract_localized_attributes.fk_product_abstract = spy_product_abstract.id_product_abstract AND spy_product_abstract_localized_attributes.fk_locale = spy_locale.id_locale)
 LEFT JOIN spy_url ON (spy_product_abstract.id_product_abstract = spy_url.fk_resource_product_abstract AND spy_url.fk_locale = spy_locale.id_locale)
        ';

        $this->criteriaBuilder
            ->sql($sql)
            ->setParameter('fk_locale_1', $this->locale->getIdLocale());
    }
}
