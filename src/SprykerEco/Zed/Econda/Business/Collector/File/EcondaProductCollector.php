<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector\File;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\StorageProductImageTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface;
use SprykerEco\Zed\Econda\Business\Collector\AbstractDatabaseCollector;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface;
use SprykerEco\Zed\Econda\EcondaConfig;
use SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery;

class EcondaProductCollector extends AbstractDatabaseCollector
{
    // CSV File Columns
    const ID_COLUMN = 'ID';
    const NAME_COLUMN = 'Name';
    const DESCRIPTION_COLUMN = 'Description';
    const PRODUCT_URL_COLUMN = 'ProductURL';
    const IMAGE_URL_COLUMN = 'ImageURL';
    const PRICE_COLUMN = 'Price';
    const STOCK_COLUMN = 'Stock';
    const PRODUCT_CATEGORY_COLUMN = 'ProductCategory';

    // Internal Query Fields
    const ID_PRODUCT_ABSTRACT = 'id_product_abstract';
    const SKU = 'sku';
    const URL = 'url';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const META_DESCRIPTION = 'meta_description';
    const QUANTITY = 'quantity';
    const ID_PRODUCT_CONCRETE = 'id_product';
    const DEFAULT_QUERY_FIELD = 'default';
    const EXTERNAL_URL_SMALL_QUERY_FIELD = 'externalUrlSmall';

    const RESOURCE_TYPE = 'products';

    /**
     * @var \SprykerEco\Zed\Econda\EcondaConfig
     */
    protected $config;

    /**
     * @var \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected $productCategoryQueryContainer;

    /**
     * @var \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface
     */
    protected $priceProductFacade;

    /**
     * @var \Propel\Runtime\Collection\Collection
     */
    protected $categoryCacheCollection;

    /**
     * @var \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected $productImageQueryContainer;

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteria
     * @param \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery $pdoEcondaQuery
     * @param \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface $productCategoryQueryContainer
     * @param \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface $productImageQueryContainer
     * @param \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceProductFacadeInterface $priceProductFacade
     * @param \SprykerEco\Zed\Econda\EcondaConfig $config
     */
    public function __construct(
        CriteriaBuilderInterface $criteria,
        AbstractPdoEcondaQuery $pdoEcondaQuery,
        ProductCategoryQueryContainerInterface $productCategoryQueryContainer,
        ProductImageQueryContainerInterface $productImageQueryContainer,
        EcondaToPriceProductFacadeInterface $priceProductFacade,
        EcondaConfig $config
    ) {

        parent::__construct($criteria, $pdoEcondaQuery);

        $this->productCategoryQueryContainer = $productCategoryQueryContainer;
        $this->productImageQueryContainer = $productImageQueryContainer;
        $this->priceProductFacade = $priceProductFacade;
        $this->categoryCacheCollection = new Collection([]);
        $this->config = $config;
    }

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
        $imageUrl = $this->getImageUrlFromItemData($collectItemData);

        return [
            static::ID_COLUMN => $collectItemData[static::SKU],
            static::NAME_COLUMN => $collectItemData[static::NAME],
            static::DESCRIPTION_COLUMN => $collectItemData[static::META_DESCRIPTION],
            static::PRODUCT_URL_COLUMN => $this->config->getHostYves() . $collectItemData[static::URL],
            static::IMAGE_URL_COLUMN => $imageUrl,
            static::PRICE_COLUMN => number_format($this->findPriceBySku($collectItemData[static::SKU]) / 100, 2),
            static::STOCK_COLUMN => (int)$collectItemData[static::QUANTITY],
            static::PRODUCT_CATEGORY_COLUMN => implode(EcondaConfig::ECONDA_CSV_CATEGORY_DELIMITER, $this->generateCategories($collectItemData[static::ID_PRODUCT_ABSTRACT])),
        ];
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return static::RESOURCE_TYPE;
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    protected function findPriceBySku($sku)
    {
        return $this->priceProductFacade->findPriceBySku($sku);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function generateCategories($idProductAbstract)
    {
        if ($this->categoryCacheCollection->offsetExists($idProductAbstract)) {
            return $this->categoryCacheCollection->get($idProductAbstract);
        }

        $productCategoryMappings = $this->getProductCategoryMappings($idProductAbstract);

        $categories = [];
        foreach ($productCategoryMappings as $mapping) {
            $categories[] = $mapping->getSpyCategory()->getIdCategory();
        }

        $this->categoryCacheCollection->set($idProductAbstract, $categories);

        return $categories;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return \Orm\Zed\ProductCategory\Persistence\SpyProductCategory[]|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function getProductCategoryMappings($idProductAbstract)
    {
        return $this->productCategoryQueryContainer
            ->queryLocalizedProductCategoryMappingByIdProduct($idProductAbstract)
            ->innerJoinSpyCategory()
            ->addAnd(
                SpyCategoryTableMap::COL_IS_ACTIVE,
                true,
                Criteria::EQUAL
            )
            ->orderByProductOrder()
            ->find();
    }

    /**
     * @param int $idProductAbstract
     * @param int $idProductConcrete
     *
     * @return array
     */
    protected function generateProductConcreteImageSets($idProductAbstract, $idProductConcrete)
    {
        $imageSets = $this->productImageQueryContainer
            ->queryProductImageSet()
            ->filterByFkProductAbstract($idProductAbstract)
            ->_or()
            ->filterByFkProduct($idProductConcrete)
            ->find();

        $result = [];
        foreach ($imageSets as $imageSetEntity) {
            $result[$imageSetEntity->getName()] = [];
            $productsToImages = $imageSetEntity->getSpyProductImageSetToProductImages(
                $this->productImageQueryContainer->queryProductImageSetToProductImage()
                    ->orderBySortOrder(Criteria::DESC)
            );
            foreach ($productsToImages as $productToImageEntity) {
                $imageEntity = $productToImageEntity->getSpyProductImage();
                $result[$imageSetEntity->getName()][] = [
                    StorageProductImageTransfer::ID_PRODUCT_IMAGE => $imageEntity->getIdProductImage(),
                    StorageProductImageTransfer::EXTERNAL_URL_LARGE => $imageEntity->getExternalUrlLarge(),
                    StorageProductImageTransfer::EXTERNAL_URL_SMALL => $imageEntity->getExternalUrlSmall(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param array $collectItemData
     *
     * @return string
     */
    protected function getImageUrlFromItemData(array $collectItemData)
    {
        $imageSet = $this->generateProductConcreteImageSets(
            $collectItemData[static::ID_PRODUCT_ABSTRACT],
            $collectItemData[static::ID_PRODUCT_CONCRETE]
        );

        return $this->getSmallPictureUrlFromDefaultImageSet($imageSet);
    }

    /**
     * @param array $imageSet
     *
     * @return null|string
     */
    protected function getSmallPictureUrlFromDefaultImageSet($imageSet)
    {
        $defaultImageSet = $this->getDefaultImageSet($imageSet);

        if (is_array($defaultImageSet[0])) {
            return $this->getSmallImageUrl($defaultImageSet[0]);
        }

        return null;
    }

    /**
     * @param array $imageSet
     *
     * @return array
     */
    protected function getDefaultImageSet($imageSet)
    {
        if (array_key_exists(static::DEFAULT_QUERY_FIELD, $imageSet) && is_array($imageSet[static::DEFAULT_QUERY_FIELD])) {
            return $imageSet[static::DEFAULT_QUERY_FIELD];
        }

        return [];
    }

    /**
     * @param array $imageArray
     *
     * @return string
     */
    protected function getSmallImageUrl($imageArray)
    {
        if (array_key_exists(static::EXTERNAL_URL_SMALL_QUERY_FIELD, $imageArray)) {
            return $imageArray[static::EXTERNAL_URL_SMALL_QUERY_FIELD];
        }
        return '';
    }
}
