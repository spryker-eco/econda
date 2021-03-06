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
    protected const ROOT_CATEGORY = 'ROOT';

    // CSV File Columns
    protected const ID_COLUMN = 'ID';
    protected const PARENT_COLUMN = 'ParentID';
    protected const NAME_COLUMN = 'Name';

    // Internal Query Fields
    protected const ID_CATEGORY_NODE_QUERY_FIELD = 'id_category_node';
    protected const PARENTS_QUERY_FIELD = 'parents';
    protected const NAME_QUERY_FIELD = 'name';
    protected const CHILDREN_QUERY_FIELD = 'children';
    protected const FK_PARENT_CATEGORY_NODE = 'fk_parent_category_node';

    protected const RESOURCE_TYPE = 'categories';

    /**
     * @param array $collectedSet
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return array
     */
    protected function collectData(array $collectedSet, LocaleTransfer $localeTransfer): array
    {
        $setToExport = [];

        foreach ($collectedSet as $collectedItemData) {
            $collectedItemData[static::CHILDREN_QUERY_FIELD] = $this->getChildren($collectedItemData, $collectedSet);
            $collectedItemData[static::PARENTS_QUERY_FIELD] = $this->getParents($collectedItemData, $collectedSet);

            $setToExport[] = $this->collectItem($collectedItemData);
        }

        return $setToExport;
    }

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem(array $collectItemData): array
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
    protected function getChildren(array $node, array $data, $nested = true): array
    {
        $children = array_filter($data, function ($item) use ($node) {
            return ((int)$item[static::FK_PARENT_CATEGORY_NODE] === (int)$node[static::ID_CATEGORY_NODE_QUERY_FIELD]);
        });

        foreach ($children as $index => $child) {
            if ($nested) {
                $children[$index][static::CHILDREN_QUERY_FIELD] = $this->getChildren($children[$index], $data);
            }

            $children[$index] = $this->formatCategoryNode($children[$index]);
        }

        return $children;
    }

    /**
     * @return string
     */
    protected function collectResourceType(): string
    {
        return static::RESOURCE_TYPE;
    }

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    protected function formatCategoryNode(array $collectItemData): array
    {
        return [
            static::ID_COLUMN => $collectItemData[static::ID_CATEGORY_NODE_QUERY_FIELD],
            static::PARENT_COLUMN => $collectItemData[static::PARENTS_QUERY_FIELD],
            static::NAME_COLUMN => $collectItemData[static::NAME_QUERY_FIELD],
        ];
    }

    /**
     * @param array $node
     * @param array $data
     *
     * @return string
     */
    protected function getParents(array $node, array $data): string
    {
        $parents = array_filter($data, function ($item) use ($node) {
            return ((int)$item[static::ID_CATEGORY_NODE_QUERY_FIELD] === (int)$node[static::FK_PARENT_CATEGORY_NODE]);
        });

        $lastParent = end($parents);
        if ($lastParent === false) {
            return static::ROOT_CATEGORY; // 'ROOT'
        }

        return $lastParent[static::ID_CATEGORY_NODE_QUERY_FIELD];
    }
}
