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
 
class MSP_Unifeed_Adminhtml_Unifeed_TemplateController extends Mage_Adminhtml_Controller_Action
{
	protected function _prapareAction()
	{
		if (Mage::registry('msp_unifeed_template_data'))
			return $this;
		
		$id = $this->getRequest()->getParam('id');
		
		$model = Mage::getModel('msp_unifeed/template');
		
		if ($id)
			$model->load($id);
		
		Mage::register('msp_unifeed_template_data', $model);
		return $this;
	}
	
	protected function getFeed()
	{
		return Mage::registry('msp_unifeed_template_data');
	}

	protected function _isAllowed(){
		return Mage::getSingleton('admin/session')->isAllowed('catalog/msp_unifeed/msp_unifeed_template');
	}
	
	public function indexAction()
	{
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('msp_unifeed/adminhtml_template'));
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$this->_prapareAction();
		$model = $this->getFeed();

		$this->loadLayout();
		$this->_setActiveMenu('msp_unifeed_template/items');
		
		$this->_addContent($this->getLayout()->createBlock('msp_unifeed/adminhtml_template_edit'));
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$this->_prapareAction();
		$model = $this->getFeed();
		
		$templateId = $model->getId();

		if ($this->getRequest()->getPost())
		{
			try
			{
				$postData = $this->getRequest()->getPost();
				$templateData = $postData['template'];

				$model
					->setData('name', $templateData['name'])
					->setData('template_open', $templateData['template_open'])
					->setData('template_close', $templateData['template_close'])
					->setData('template_product', $templateData['template_product'])
					->setData('type', $templateData['type'])
				;

				$model->save();
				
				if (!$templateId)
					$templateId = $model->getId();
				
				if ($templateId)
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template was successfully saved'));
				else
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('One error occurred while trying to save'));
	
				$this->_redirect('*/*/edit', array('id' => $templateId));
				return;
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $templateId));
				return;
			}
		}
		
		$this->_redirect('*/*/index');
	}
	
	public function deleteAction()
	{
		$this->_prapareAction();
		$model = $this->getTemplate();

		if ($model->getId())
		{
			try
			{
				$model->delete();
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template was successfully deleted'));
				$this->_redirect('*/*/index');
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		
		$this->_redirect('*/*/index');
	}
}
