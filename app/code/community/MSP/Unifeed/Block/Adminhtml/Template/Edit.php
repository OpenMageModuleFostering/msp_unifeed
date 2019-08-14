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
 
class MSP_Unifeed_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'msp_unifeed';
		$this->_controller = 'adminhtml_template';
		
		$this->_updateButton('save', 'label', Mage::helper('msp_unifeed')->__('Save Template'));
		$this->_updateButton('delete', 'label', Mage::helper('msp_unifeed')->__('Delete Template'));
	}
	
	public function getHeaderText()
	{
		if (Mage::registry('msp_unifeed_template_data') && Mage::registry('msp_unifeed_template_data')->getId())
		{
			return Mage::helper('msp_unifeed')->__("Edit Template '%s'", $this->htmlEscape(Mage::registry('msp_unifeed_template_data')->getName()));
		}
		else
		{
			return Mage::helper('msp_unifeed')->__('Add Template');
		}
	}
}
