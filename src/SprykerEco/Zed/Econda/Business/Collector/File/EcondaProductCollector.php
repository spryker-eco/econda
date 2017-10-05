<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Business\Collector\File;

use Everon\Component\Collection\Collection;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\StorageProductImageTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface;
use SprykerEco\Zed\Econda\Business\Collector\AbstractDatabaseCollector;
use SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceBridgeInterface;
use SprykerEco\Zed\Econda\EcondaConfig;
use SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery;
use Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface;

class EcondaProductCollector extends AbstractDatabaseCollector
{

    //CSV file columns
    const ID_COLUMN = 'ID';
    const NAME_COLUMN = 'Name';
    const DESCRIPTION_COLUMN = 'Description';
    const PRODUCTURL_COLUMN = 'PRODUCTURL';
    const IMAGE_URL_COLUMN = 'ImageURL';
    const PRICE_COLUMN = 'Price';
    const STOCK_COLUMN = 'Stock';
    const PRODUCT_CATEGORY_COLUMN = 'ProductCategory';

    //internal query fields
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

    const PRODUCTS = 'products';

    /**
     * @var \SprykerEco\Zed\Econda\EcondaConfig
     */
    protected $config;

    /**
     * @var \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected $productCategoryQueryContainer;

    /**
     * @var \Spryker\Zed\Price\Business\PriceFacadeInterface
     */
    protected $priceFacade;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $categoryCacheCollection;

    /**
     * @var \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected $productImageQueryContainer;

    /**
     * @var array
     */
    protected $superAttributes;

    /**
     * @param \Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderInterface $criteria
     * @param \SprykerEco\Zed\Econda\Persistence\Econda\AbstractPdoEcondaQuery $query
     * @param \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface $productCategoryQueryContainer
     * @param \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface $productImageQueryContainer
     * @param \SprykerEco\Zed\Econda\Dependency\Facade\EcondaToPriceBridgeInterface|\Spryker\Zed\Price\Business\PriceFacadeInterface $priceFacade
     * @param \SprykerEco\Zed\Econda\EcondaConfig $config
     */
    public function __construct(
        CriteriaBuilderInterface $criteria,
        AbstractPdoEcondaQuery $query,
        ProductCategoryQueryContainerInterface $productCategoryQueryContainer,
        ProductImageQueryContainerInterface $productImageQueryContainer,
        EcondaToPriceBridgeInterface $priceFacade,
        EcondaConfig $config
    ) {

        parent::__construct($criteria, $query);

        $this->productCategoryQueryContainer = $productCategoryQueryContainer;
        $this->productImageQueryContainer = $productImageQueryContainer;
        $this->priceFacade = $priceFacade;
        $this->categoryCacheCollection = new Collection([]);
        $this->config = $config;
    }

    protected function collectData(array $collectedSet, LocaleTransfer $locale)
    {
        $setToExport = [];

        foreach ($collectedSet as $index => $collectedItemData) {
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
            self::ID_COLUMN => $collectItemData[self::SKU],
            self::NAME_COLUMN => $collectItemData[self::NAME],
            self::DESCRIPTION_COLUMN => $collectItemData[self::META_DESCRIPTION],
            self::PRODUCTURL_COLUMN => $this->config->getHostYves() . $collectItemData[self::URL],
            self::IMAGE_URL_COLUMN => $imageUrl,
            self::PRICE_COLUMN => $this->getPriceBySku($collectItemData[self::SKU]),
            self::STOCK_COLUMN => (int)$collectItemData[self::QUANTITY],
            self::PRODUCT_CATEGORY_COLUMN => implode(EcondaConfig::ECONDA_CSV_CATEGORY_DELIMITER, $this->generateCategories($collectItemData[self::ID_PRODUCT_ABSTRACT])),
        ];
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return self::PRODUCTS;
    }

    /**
     * @param string $sku
     *
     * @return float
     */
    protected function getPriceBySku($sku)
    {
        return $this->priceFacade->getPriceBySku($sku) / 100;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function generateCategories($idProductAbstract)
    {
        if ($this->categoryCacheCollection->has($idProductAbstract)) {
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
            $collectItemData[self::ID_PRODUCT_ABSTRACT],
            $collectItemData[self::ID_PRODUCT_CONCRETE]
        );

        return $this->getSmallPictureUrlFromDefaultImageSet($imageSet);
    }

    protected function getSmallPictureUrlFromDefaultImageSet($imageSet)
    {
        $defaultImageSet = $this->getDefaultImageSet($imageSet);

        if (is_array($defaultImageSet[0])) {
            return $this->getSmallImageUrl($defaultImageSet[0]);
        }

        return '';
    }

    /**
     * @param array $imageSet
     *
     * @return array
     */
    protected function getDefaultImageSet($imageSet)
    {
        if (array_key_exists(self::DEFAULT_QUERY_FIELD, $imageSet) && is_array($imageSet[self::DEFAULT_QUERY_FIELD])) {
            return $imageSet[self::DEFAULT_QUERY_FIELD];
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
        if (array_key_exists(self::EXTERNAL_URL_SMALL_QUERY_FIELD, $imageArray)) {
            return $imageArray[self::EXTERNAL_URL_SMALL_QUERY_FIELD];
        }
        return '';
    }

}
