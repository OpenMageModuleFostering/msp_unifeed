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
 
class MSP_Unifeed_Block_Adminhtml_Unifeed_Edit_Tab_Ga extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset(
			'msp_unifeed_form_ga',
			array(
				'legend' => Mage::helper('msp_unifeed')->__('Google Analytics Tracking')
			)
		);
		
		$fieldset->addField('ga_source', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Campaign Source'),
			'name'      => 'feed[ga_source]',
		));
		
		$fieldset->addField('ga_medium', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Campaign Medium'),
			'name'      => 'feed[ga_medium]',
		));
		
		$fieldset->addField('ga_term', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Campaign Term'),
			'name'      => 'feed[ga_term]',
		));
		
		$fieldset->addField('ga_content', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Campaign Content'),
			'name'      => 'feed[ga_content]',
		));
		
		$fieldset->addField('ga_name', 'text', array(
			'label'     => Mage::helper('msp_unifeed')->__('Campaign Name'),
			'name'      => 'feed[ga_name]',
		));
		
		if (Mage::registry('msp_unifeed_data'))
			$form->setValues(Mage::registry('msp_unifeed_data')->getData());
		
		$this->setChild('form_after',
            $this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_ga_after')
        );
	
		return parent::_prepareForm();
	}
}
