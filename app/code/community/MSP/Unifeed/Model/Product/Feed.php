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
 
class MSP_Unifeed_Model_Product_Feed extends Mage_Core_Model_Abstract
{
	public function setCategories($feedId, $categoryIds)
	{
		$count = 0;
		
		$collection = Mage::getModel('catalog/category')
			->getCollection();
		
		$collection
			->addAttributeToSelect(array('msp_unifeed_ids'))
			->load();
		
		$categoryModel = Mage::getModel('catalog/category');
		foreach ($collection as $category)
		{	
			// Add category
			if (in_array($category->getId(), $categoryIds))
			{
				$categoryFeeds = explode(':', $category->getMspUnifeedIds());
				$categoryFeeds[] = $feedId;
				
				$categoryModel
					->setData(array())
					->load($category->getId())
					->setMspUnifeedIds($categoryFeeds)
					->save();
				
				$count++;
			}
			
			// Remove category
			else
			{
				$categoryFeeds2 = array();
				$categoryFeeds = explode(':', $category->getMspUnifeedIds());

				if (in_array($feedId, $categoryFeeds))
				{
					foreach ($categoryFeeds as $i)
					{
						if (!$i || ($i == $feedId))
							continue;
						
						$categoryFeeds2[] = $i;
					}
					
					$categoryModel
						->setData(array())
						->load($category->getId())
						->setMspUnifeedIds($categoryFeeds2)
						->save();
				}
			}
		}
		
		return $count;
	}
}
