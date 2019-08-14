<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_Unifeed
 * @copyright  Copyright (c) 2014 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MSP_Unifeed_Model_Feed extends Mage_Core_Model_Abstract
{
    const BUILD_STATUS_NOT_READY = 0;
    const BUILD_STATUS_BUILDING = 1;
    const BUILD_STATUS_MARKED_REBUILD = 2;
    const BUILD_STATUS_UPDATING = 3;
    const BUILD_STATUS_READY = 4;

    protected $_categoryIdsArray = null;
    protected $_templateModel = null;
    protected $_containerFilter = null;
    protected $_productFilter = null;
    protected $_categories = array();
    protected $_fpBuffer = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('msp_unifeed/feed');
    }

    public function getTemplate()
    {
        if (!$this->_templateModel)
            $this->_templateModel = Mage::getModel('msp_unifeed/template')->load($this->getMspUnifeedTemplateId());

        return $this->_templateModel;
    }

    protected function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    protected function getUnifeedWorkingPath()
    {
        $path = Mage::getBaseDir('var').DS.'unifeed';

        if (!is_dir($path))
            mkdir($path);

        chmod($path, 0777);

        return $path;
    }

    protected function getLockFile()
    {
        return $this->getUnifeedWorkingPath().DS.'export.lck';
    }

    protected function getBufferFileName()
    {
        if (!$this->getId())
            return '';

        return $this->getUnifeedWorkingPath().DS.'unifeed-'.$this->getId().'.buf';
    }

    public function getFinalFileName()
    {
        if (!$this->getId())
            return '';

        return $this->getUnifeedWorkingPath().DS.'unifeed-'.$this->getId().'.exp';
    }

    protected function getRebuildFileName()
    {
        if (!$this->getId())
            return '';

        return $this->getUnifeedWorkingPath().DS.'unifeed-'.$this->getId().'.reb';
    }

    protected function getCategoriesCollection()
    {
        if (!$this->getId())
            return null;

        $store = $this->getStore();
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection
            ->setStoreId($store->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(array(array('attribute'=>'msp_unifeed_ids', 'like'=>"%:".$this->getId().":%")))
            ->load();

        return $collection;
    }

    protected function getCategoryIdsArray()
    {
        if (!$this->getId())
            return array();

        if (!is_array($this->_categoryIdsArray))
        {
            $this->_categoryIdsArray = array();

            $categories = $this->getCategoriesCollection();
            foreach ($categories as $category)
            {
                $this->_categoryIdsArray[] = $category->getId();
            }
        }

        return $this->_categoryIdsArray;
    }

    public function getProductsCollection()
    {
        if (!$this->getId())
            return null;

        $store = $this->getStore();
        $collection = Mage::getModel('msp_unifeed/resource_catalog_eav_mysql4_product_collection');

        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        $categories = $this->getCategoryIdsArray();
        if (!count($categories))
            return null;

        $collection
            ->addAttributeToSelect('*')
            ->addStoreFilter($this->getStoreId())
            ->addWebsiteFilter($this->getStore()->getWebsite())
            ->addPriceData(0, $this->getStore()->getWebsiteId())
            ->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId())
            ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId())
            ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId())
            ->addAttributeToFilter('visibility', $visibility)
            ->addCategoriesFilter($categories);

        $this->addMediaGalleryAttributeToCollection($collection);

        $collection
            ->load();

        return $collection;
    }

    protected function getContainerFilterModel()
    {
        if (!$this->_containerFilter)
            $this->_containerFilter = Mage::getModel('msp_unifeed/template_container');

        return $this->_containerFilter;
    }

    protected function getProductFilterModel()
    {
        if (!$this->_productFilter)
            $this->_productFilter = Mage::getModel('msp_unifeed/template_product');

        return $this->_productFilter;
    }

    /**
     * Get category
     * @param $id
     * @return Mage_Catalog_Model_Category
     */
    protected function getCategory($id)
    {
        if (!isset($this->_categories[$id]))
            $this->_categories[$id] = Mage::getModel('catalog/category')->load($id);

        return $this->_categories[$id];
    }

    /**
     * Open buffer file
     *
     * @return MSP_UniFeed_Model_Feed
     */
    protected function openBufferFile()
    {
        if (!$this->getId())
            return $this;

        $this->_fpBuffer = fopen($this->getBufferFileName(), 'a');
        return $this;
    }

    /**
     * Clear buffer file
     *
     * @return MSP_UniFeed_Model_Feed
     */
    protected function clearBufferFile()
    {
        if (!$this->getId())
            return $this;

        @unlink($this->getBufferFileName());
        return $this;
    }

    /**
     * Write buffer immediately
     *
     * @param string $buffer
     * @return MSP_UniFeed_Model_Feed
     */
    protected function writeBufferFile($buffer)
    {
        if (!$this->getId())
            return $this;

        if (!$this->_fpBuffer)
            $this->openBufferFile();

        fputs($this->_fpBuffer, $buffer);

        return $this;
    }

    /**
     * Close buffer file
     *
     * @return MSP_UniFeed_Model_Feed
     */
    protected function closeBufferFile()
    {
        if (!$this->getId())
            return $this;

        if (!$this->_fpBuffer)
            return $this;

        fclose($this->_fpBuffer);
        $this->_fpBuffer = 0;
        return $this;
    }

    /**
     * Finalize buffer file
     *
     * @return MSP_UniFeed_Model_Feed
     */
    protected function finalizeBufferFile()
    {
        if (!$this->getId())
            return $this;

        @rename($this->getBufferFileName(), $this->getFinalFileName());
        @unlink($this->getRebuildFileName());
        @unlink($this->getPositionFileName());
        @unlink($this->getIdsFileName());

        return $this;
    }

    /**
     * Get AMP for current format
     *
     * @return string
     */
    public function getAmp()
    {
        if (($this->getType() == 'xml') || ($this->getType() == 'html'))
            return '&amp;';

        return '&';
    }

    /**
     * Get GoogleAnalytics string
     *
     * @return string
     */
    public function getGaQueryString()
    {
        $utm = array();

        $amp = $this->getAmp();

        if ($this->getGaSource())
            $utm[] = 'utm_source='.urlencode($this->getGaSource());

        if ($this->getGaMedium())
            $utm[] = 'utm_medium='.urlencode($this->getGaMedium());

        if ($this->getGaTerm())
            $utm[] = 'utm_term='.urlencode($this->getGaTerm());

        if ($this->getGaContent())
            $utm[] = 'utm_content='.urlencode($this->getGaContent());

        if ($this->getGaName())
            $utm[] = 'utm_campaign='.urlencode($this->getGaName());

        return join($amp, $utm);
    }

    /**
     * Load mediagallery with product's collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function addMediaGalleryAttributeToCollection(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection)
    {
        if (!count($productCollection->getAllIds()))
            return $productCollection;

        $mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('catalog_read');

        $mediaGalleryData = $read->fetchAll('
	        SELECT
	            main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
	            `value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
	            `default_value`.`position` AS `position_default`,
	            `default_value`.`disabled` AS `disabled_default`
	        FROM '.$resource->getTableName('catalog_product_entity_media_gallery').' AS `main`
	            LEFT JOIN '.$resource->getTableName('catalog_product_entity_media_gallery_value').' AS `value`
	                ON main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId() . '
	            LEFT JOIN '.$resource->getTableName('catalog_product_entity_media_gallery_value').' AS `default_value`
	                ON main.value_id=default_value.value_id AND default_value.store_id=0
	        WHERE (
	            main.attribute_id = '.$read->quote($mediaGalleryAttributeId).')
	            AND (main.entity_id IN ('.$read->quote($productCollection->getAllIds()).'))
	        ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC
	    ');

        $mediaGalleryByProductId = array();
        foreach ($mediaGalleryData as $galleryImage) {
            $k = $galleryImage['entity_id'];
            unset($galleryImage['entity_id']);
            if (!isset($mediaGalleryByProductId[$k])) {
                $mediaGalleryByProductId[$k] = array();
            }
            $mediaGalleryByProductId[$k][] = $galleryImage;
        }
        unset($mediaGalleryData);

        foreach ($productCollection as &$product) {
            $productId = $product->getData('entity_id');
            if (isset($mediaGalleryByProductId[$productId])) {
                $product->setData('media_gallery', array('images' => $mediaGalleryByProductId[$productId]));
            }
        }
        unset($mediaGalleryByProductId);

        return $productCollection;
    }

    /**
     * Return single product feed
     *
     * @param Mage_Core_Model_Catalog_Product
     * @param array $baseInfo [optional]
     * @param bool $categorySkip [optional]
     * @return string
     */
    protected function getProductFeed($product, $baseInfo = array())
    {
        if (!$this->getId())
            return '';

        $info = $baseInfo;

        $info['product'] = $product;
        $info['stock'] = $this->_stockModel->loadByProduct($product);
        $info['final_price'] = $product->getFinalPrice();
        $info['final_price_rule'] = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product, $product->getMinimalPrice());
        $info['final_price_tax'] = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
        $info['final_price_notax'] = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), false);
        $info['catalog_price'] = $product->getPriceModel()->getFinalPrice(1, $product);

        $info['images'] = array();
        $mediaGallery = $product->getMediaGalleryImages();

        foreach ($mediaGallery as $image)
            $info['images'][] = $image['url'];

        $n = 0;
        $categories = array();
        $categoryNames = array();

        $categoryIds = array();
        $categoryIdsTmp = $product->getCategoryIds();

        foreach ($categoryIdsTmp as $categoryId) $categoryIds[] = $categoryId;

        foreach ($categoryIds as $categoryId)
        {
            $category = $this->getCategory($categoryId);
            if ($category->getIsActive()) continue;

            $info["category$n"] = $category;

            $categories[] = $category;
            $categoryNames[] = $category->getName();
            $n++;
        }

        $info['categories'] = $categories;
        $info['category_names'] = $categoryNames;

        $item = $this->getProductFilterModel()->setVariables($info)->setFeed($this)->filter($this->getTemplate()->getTemplateProduct());

        return $item;
    }

    /**
     * Set rebuild flag
     *
     * @return MSP_UniFeed_Model_Feed
     */
    public function setRebuildFlag()
    {
        if (!$this->getId())
            return $this;

        $fp = fopen($this->getRebuildFileName(), 'w');
        fputs($fp, time());
        fclose($fp);

        return $this;
    }

    /**
     * Get rebuild flag
     *
     * @return bool
     */
    public function getRebuildFlag()
    {
        if (!$this->getId())
            return 0;

        clearstatcache();
        return file_exists($this->getRebuildFileName()) || !file_exists($this->getFinalFileName());
    }

    /**
     * Get build status
     *
     * @return int
     */
    public function getBuildStatus()
    {
        if (!$this->getId())
            return -1;

        if (!file_exists($this->getFinalFileName()))
        {
            if (file_exists($this->getBufferFileName()))
                return self::BUILD_STATUS_BUILDING;

            return self::BUILD_STATUS_NOT_READY;
        }

        if (file_exists($this->getRebuildFileName()))
        {
            if (file_exists($this->getBufferFileName()))
                return self::BUILD_STATUS_UPDATING;
            else
                return self::BUILD_STATUS_MARKED_REBUILD;
        }

        return self::BUILD_STATUS_READY;
    }

    /**
     * Get build date
     *
     * @return int
     */
    public function getBuildDate()
    {
        if (!$this->getId())
            return -1;

        if (!file_exists($this->getFinalFileName()))
            return 0;

        return filemtime($this->getFinalFileName());
    }

    public function buildAll()
    {
        $fp = fopen($this->getLockFile(), "w");

        if (flock($fp, LOCK_EX|LOCK_NB))
        {
            $collection = Mage::getModel('msp_unifeed/feed')->getCollection();
            foreach ($collection as $f)
            {
                $feed = Mage::getModel('msp_unifeed/feed')->load($f->getId());
                $feed->build();
            }

            fclose($fp);
            unlink($this->getLockFile());
        }
    }

    /**
     * Build feed
     * @return MSP_UniFeed_Model_Feed
     */
    public function build()
    {
        if (!$this->getId())
            return $this;

        if (!$this->getRebuildFlag())
            return $this;

        if (!$this->getStatus())
        {
            @unlink($this->getBufferFileName());
            @unlink($this->getFinalFileName());
            return $this;
        }

        // Initialize
        $this->_stockModel = Mage::getModel('cataloginventory/stock_item');

        // Common data info
        $store = $this->getStore();
        $baseInfo['store'] = $store;
        $baseInfo['timestamp'] = date('Y-m-d').'T'.date('H:i:s').'Z';
        $baseInfo['locale'] = Mage::app()->getLocale()->getLocaleCode();

        $this->clearBufferFile();
        $this->writeBufferFile($this->getContainerFilterModel()->filter($this->getTemplate()->getTemplateOpen()), null);

        // Categories
        $collection = $this->getProductsCollection();
        if ($collection)
        {
            foreach ($collection as $product)
            {
                $product->setStoreId($store->getId());
                $this->writeBufferFile($this->getProductFeed($product, $baseInfo, false));
            }
        }

        $this->writeBufferFile($this->getContainerFilterModel()->filter($this->getTemplate()->getTemplateClose()), null);
        $this->closeBufferFile();
        $this->finalizeBufferFile();

        return $this;
    }
}
