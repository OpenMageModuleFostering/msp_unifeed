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
 
class MSP_Unifeed_Block_Adminhtml_Unifeed_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$this->setTemplate('msp_unifeed/edit/form.phtml');
		
		$fieldset = $form->addFieldset(
			'msp_unifeed_form_settings',
			array(
				'legend' => Mage::helper('msp_unifeed')->__('Feed settings')
			)
		);
	
		$fieldset->addField('code', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Feed Code'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'feed[code]',
			'note'		=> Mage::helper('msp_unifeed')->__('Must contain only standard lowercase characters or numbers (0-9, a-z)')
		));
		
		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Feed Description'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'feed[name]',
		));
		
		$fieldset->addField('msp_unifeed_template_id', 'select', array(
			'label'     => Mage::helper('msp_unifeed')->__('Template'),
			'name'      => 'feed[msp_unifeed_template_id]',
			'class'     => 'required-entry',
			'values'    => Mage::getSingleton('msp_unifeed/template')->getOptionArray(),	
		));
		
		$fieldset->addField('store_id', 'select', array(
			'label'     => Mage::helper('msp_unifeed')->__('Store'),
			'name'      => 'feed[store_id]',
			'class'     => 'required-entry',
			'values'    => Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray(),	
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('msp_unifeed')->__('Status'),
			'name'      => 'feed[status]',
			'class'     => 'required-entry',
			'values'    => Mage::getSingleton('msp_unifeed/feed_status')->getOptionArray(),	
		));
		
		if (Mage::registry('msp_unifeed_data') && Mage::registry('msp_unifeed_data')->getData())
		{
			$data = Mage::registry('msp_unifeed_data')->getData();
			$form->setValues($data);
			$this->setChild('form_after',
	            $this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_settings_after')->setFeedId($data['msp_unifeed_feed_id'])
	        );
		}
		else
		{
			$form->setValues(array('status' => 1));
		}
	
		return parent::_prepareForm();
	}
}
