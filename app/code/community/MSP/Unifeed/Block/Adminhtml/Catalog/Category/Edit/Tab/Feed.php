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
 
class MSP_Unifeed_Block_Adminhtml_Catalog_Category_Edit_Tab_Feed
	extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function getTabLabel()
    {
        return Mage::helper('msp_unifeed')->__('UniFeed');
    }

    public function getTabTitle()
    {
        return Mage::helper('msp_unifeed')->__('UniFeed');
    }
	
	public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }	
	
	public function getCategory()
	{
		return Mage::registry('category');
	}
	
	public function getStoreId()
	{
		return $this->getCategory()->getStoreId();
	}
	
	public function getCategoryId()
	{
		return $this->getCategory()->getId();
	}
	
	protected function hasFeed($feedId)
	{
		if (strpos($this->getCategory()->getMspUnifeedIds(), ':'.intval($feedId).':') !== false)
			return true;
			
		return false;
	}

	protected function getFeedsCollection()
	{
		return Mage::getModel('msp_unifeed/feed')->getResourceCollection()->load();
	}
}
