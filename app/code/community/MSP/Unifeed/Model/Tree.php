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
 
class MSP_Unifeed_Model_Tree extends Mage_Core_Model_Abstract
{
	protected $_feed;
	protected $_categoryModel;
	
	public function setFeed($feed)
	{
		$this->_feed = $feed;
		return $this;
	}
	
	public function getFeed()
	{
		return $this->_feed;
	}
	
	protected function getCategoryModel($categoryId)
	{
		if (!$this->_categoryModel)
			$this->_categoryModel = Mage::getModel('catalog/category');
		
		return $this->_categoryModel->setData(array())->load($categoryId);
	}
	
	protected function getCategoryChildren($categoryId)
	{
		// Magento 1.5.1.0 bug
		if ($categoryId == Mage_Catalog_Model_Category::TREE_ROOT_ID)
		{
			$collection = Mage::getResourceModel('catalog/category_collection');
	        $collection
	        	->addAttributeToSelect('name')
	            ->addPathFilter('^'.$categoryId.'/[0-9]+$')
	            ->load();
				
			return $collection;
		}
		
		return $this->getCategoryModel($categoryId)->getChildrenCategories();
		
		return Mage::getResourceModel('catalog/category_collection')
			->addAttributeToSelect('name') 
			->addAttributeToSelect('all_children') 
			->addAttributeToSelect('is_anchor')
			->addAttributeToFilter('parent_id', $categoryId)
			->joinUrlRewrite()->load();
	}
	
	public function getCategory($categoryId, $depth = 0, $level = 0)
	{
		$category = $this->getCategoryModel($categoryId);
		$children = $this->getCategoryChildren($categoryId);
		
		$hasChildren = $children->count() ? true : false;
		if ($level > $depth)
			return;
			
		$nodeinfo = $this->getCategoryInfo($categoryId);
		$out = $nodeinfo;
		
		if ($hasChildren)
		{
			$out['subs'] = array();
			foreach ($children as $subcategory)
			{
				$item = $this->getCategory($subcategory->getId(), $depth, $level + 1);
				if ($item)
					array_push($out['subs'], $item);
			}
		}
		
		return $out;
	}
	
	public function getCategoryInfo($categoryId)
	{
		if (!$categoryId)
			return;
			
		$category = $this->getCategoryModel($categoryId);
		
		return array(
			'title'		=> $category->getName(),
			'url'		=> $category->getUrl(),
			'id'		=> $category->getId(),
			'checked'	=> $this->isChecked($category)
		);
	}

	public function getRootCategory($depth = 0, $level = 0)
	{
		$categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
		$children = $this->getCategoryChildren($categoryId);

		$out = array();
		
		foreach ($children as $category)
		{
			if (!$category->getId())
				continue;
			//if (!$category->getName())
			//	continue;
				
			$out[] = $this->getCategory($category->getId(), $depth);
		}
		
		return $out;
	}
	
	public function isChecked(Mage_Catalog_Model_Category $category)
	{
		if (strpos($category->getMspUnifeedIds(), ':'.intval($this->getFeed()->getId()).':') !== false)
			return true;
		
		return false;
	}
}
