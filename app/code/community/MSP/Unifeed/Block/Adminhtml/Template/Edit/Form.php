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
 
class MSP_Unifeed_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=> 'post',
		));
		
		$fieldset = $form->addFieldset(
			'msp_unifeed_template_form',
			array(
				'legend' => Mage::helper('msp_unifeed')->__('Template settings')
			)
		);
	
		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Template name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'template[name]',
		));
		
		$fieldset->addField('type', 'select', array(
			'label'     => Mage::helper('msp_unifeed')->__('Type'),
			'name'      => 'template[type]',
			'class'     => 'required-entry',
			'values'    => Mage::getSingleton('msp_unifeed/template_type')->getOptionArray(),	
		));
		
		$fieldset->addField('template_open', 'textarea', array(
			'label'     => Mage::helper('msp_unifeed')->__('Template Open'),
			'required'  => false,
			'style'		=> 'width: 600px',
			'name'      => 'template[template_open]',
		));
		
		$fieldset->addField('template_product', 'textarea', array(
			'label'     => Mage::helper('msp_unifeed')->__('Product Template'),
			'class'     => 'required-entry',
			'required'  => true,
			'style'		=> 'width: 600px',
			'name'      => 'template[template_product]',
		));
		
		$fieldset->addField('template_close', 'textarea', array(
			'label'     => Mage::helper('msp_unifeed')->__('Template Close'),
			'required'  => false,
			'style'		=> 'width: 600px',
			'name'      => 'template[template_close]',
		));
	
		if (Mage::registry('msp_unifeed_template_data'))
			$form->setValues(Mage::registry('msp_unifeed_template_data')->getData());
			
		$form->setUseContainer(true);
		$this->setForm($form);
	
		return parent::_prepareForm();
	}
}
