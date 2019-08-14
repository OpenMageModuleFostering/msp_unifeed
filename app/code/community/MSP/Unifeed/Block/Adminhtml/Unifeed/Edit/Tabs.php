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
 
class MSP_Unifeed_Block_Adminhtml_Unifeed_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('msp_unifeed_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('msp_unifeed')->__('Feed'));
	}
	
	public function getFeed()
	{
		return Mage::registry('msp_unifeed_data');
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('settings_section', array(
			'label'     => Mage::helper('msp_unifeed')->__('Feed Settings'),
			'title'     => Mage::helper('msp_unifeed')->__('Feed Settings'),
 			'content'   => $this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_settings')->toHtml(),
		));

		$this->addTab('categories', array(
			'label'     => Mage::helper('msp_unifeed')->__('Categories Selection'),
			'title'     => Mage::helper('msp_unifeed')->__('Categories Selection'),
 			'content'   => $this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_categories')->toHtml(),
		));
		
		$this->addTab('ga_section', array(
			'label'     => Mage::helper('msp_unifeed')->__('Google Analytics Tracking'),
			'title'     => Mage::helper('msp_unifeed')->__('Google Analytics Tracking'),
 			'content'   => $this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_ga')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
