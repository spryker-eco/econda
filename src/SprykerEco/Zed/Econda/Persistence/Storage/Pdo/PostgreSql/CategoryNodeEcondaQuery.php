<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Persistence\Storage\Pdo\PostgreSql;

use SprykerEco\Zed\Econda\Persistence\Econda\AbstractEcondaPdoQuery;

class CategoryNodeEcondaQuery extends AbstractEcondaPdoQuery
{
    /**
     * @return void
     */
    protected function prepareQuery(): void
    {
        $sql = '
 WITH RECURSIVE
     tree AS
   (
     SELECT
       n.id_category_node,
       n.fk_parent_category_node,
       n.fk_category,
       n.node_order
     FROM spy_category_node n
       INNER JOIN spy_category c ON c.id_category = n.fk_category AND c.is_active = TRUE
     WHERE n.is_root = TRUE

     UNION

     SELECT
       n.id_category_node,
       n.fk_parent_category_node,
       n.fk_category,
       n.node_order
     FROM tree
       INNER JOIN spy_category_node n ON n.fk_parent_category_node = tree.id_category_node
       INNER JOIN spy_category c ON c.id_category = n.fk_category AND c.is_active = TRUE
   )
 SELECT
   tree.*,
   u.url,
   ca.name,
   ca.meta_title,
   ca.meta_description,
   ca.meta_keywords,
   ca.category_image_name,
   \'\' AS "children",
   \'\' AS "parents"
 FROM tree
   INNER JOIN spy_url u ON (u.fk_resource_categorynode = tree.id_category_node AND u.fk_locale = :fk_locale_1)
   INNER JOIN spy_category_attribute ca ON (ca.fk_category = tree.fk_category AND ca.fk_locale = :fk_locale_2)
';
        $this->criteriaBuilder
            ->sql($sql)
            ->setOrderBy([
                'tree.fk_parent_category_node' => 'ASC',
                'tree.node_order' => 'DESC',
            ])
            ->setParameter('fk_locale_1', $this->locale->getIdLocale())
            ->setParameter('fk_locale_2', $this->locale->getIdLocale());
    }
}
