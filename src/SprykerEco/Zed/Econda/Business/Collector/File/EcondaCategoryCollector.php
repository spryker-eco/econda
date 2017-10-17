<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector\File;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\Econda\Business\Collector\AbstractDatabaseCollector;

class EcondaCategoryCollector extends AbstractDatabaseCollector
{
    // CSV file columns
    const ID_COLUMN = 'ID';
    const PARENT_COLUMN = 'ParentID';
    const NAME_COLUMN = 'Name';

    // Internal Query Fields
    const ID_CATEGORY_NODE_QUERY_FIELD = 'id_category_node';
    const PARENTS_QUERY_FIELD = 'parents';
    const NAME_QUERY_FIELD = 'name';
    const CHILDREN_QUERY_FIELD = 'children';
    const FK_PARENT_CATEGORY_NODE = 'fk_parent_category_node';

    const CATEGORIES = 'categories';

    /**
     * @param array $collectedSet
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return array
     */
    protected function collectData(array $collectedSet, LocaleTransfer $localeTransfer)
    {
        $setToExport = [];

        foreach ($collectedSet as $collectedItemData) {
            $collectedItemData[self::CHILDREN_QUERY_FIELD] = $this->getChildren($collectedItemData, $collectedSet);
            $collectedItemData[self::PARENTS_QUERY_FIELD] = $this->getParents($collectedItemData, $collectedSet);

            $setToExport[] = $this->collectItem($collectedItemData);
        }

        return $setToExport;
    }

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem(array $collectItemData)
    {
        return $this->formatCategoryNode($collectItemData);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @param array $node
     * @param array $data
     * @param bool $nested
     *
     * @return array
     */
    protected function getChildren(array $node, array $data, $nested = true)
    {
        $children = array_filter($data, function ($item) use ($node) {
            return ((int)$item[self::FK_PARENT_CATEGORY_NODE] === (int)$node[self::ID_CATEGORY_NODE_QUERY_FIELD]);
        });

        foreach ($children as $index => $child) {
            if ($nested) {
                $children[$index][self::CHILDREN_QUERY_FIELD] = $this->getChildren($children[$index], $data);
            }

            $children[$index] = $this->formatCategoryNode($children[$index]);
        }

        return $children;
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return self::CATEGORIES;
    }

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    protected function formatCategoryNode(array $collectItemData)
    {
        return [
            static::ID_COLUMN => $collectItemData[self::ID_CATEGORY_NODE_QUERY_FIELD],
            static::PARENT_COLUMN => $collectItemData[self::PARENTS_QUERY_FIELD],
            static::NAME_COLUMN => $collectItemData[self::NAME_QUERY_FIELD],
        ];
    }

    /**
     * @param array $node
     * @param array $data
     *
     * @return string
     */
    protected function getParents(array $node, array $data)
    {
        $parents = array_filter($data, function ($item) use ($node) {
            return ((int)$item[self::ID_CATEGORY_NODE_QUERY_FIELD] === (int)$node[self::FK_PARENT_CATEGORY_NODE]);
        });

        $result = 'ROOT';
        foreach ($parents as $parent) {
            $result = $parent[self::ID_CATEGORY_NODE_QUERY_FIELD];
        }

        return $result;
    }
}
