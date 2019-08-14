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
 
class MSP_Unifeed_Adminhtml_Unifeed_FeedController extends Mage_Adminhtml_Controller_Action
{
	protected function _prapareAction()
	{
		if (Mage::registry('msp_unifeed_data') && Mage::registry('msp_unifeed_data')->getId())
			return $this;
		
		$id = $this->getRequest()->getParam('id');
		
		$model = Mage::getModel('msp_unifeed/feed');
		
		if ($id)
			$model->load($id);
		
		Mage::register('msp_unifeed_data', $model);
		return $this;
	}
	
	protected function getFeed()
	{
		return Mage::registry('msp_unifeed_data');
	}

	protected function _isAllowed(){
		return Mage::getSingleton('admin/session')->isAllowed('catalog/msp_unifeed/msp_unifeed_feed');
	}
	
	public function indexAction()
	{
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed'));
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function productsAction()
	{
		$this->_prapareAction();
		
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tab_products')->toHtml()
		);
	}

	public function rebuildAction()
	{
		$this->_prapareAction();
		
		if ($this->getFeed()->getId())
		{
			$this->getFeed()->setRebuildFlag();
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feed marked for rebuild'));
			$this->_redirect('*/*/edit', array('id' => $this->getFeed()->getId()));
		}
		else
		{
			$feeds = $this->getFeed()->getCollection();
			foreach ($feeds as $feed)
				$feed->setRebuildFlag();
			
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('All feeds are marked for rebuild'));
			$this->_redirect('*/*/index');
		}
	}
	
	public function editAction()
	{
		$this->_prapareAction();
		$model = $this->getFeed();

		$this->loadLayout();
		$this->_setActiveMenu('msp_unifeed/items');
		
		$this->_addContent($this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit'));
		$this->_addLeft($this->getLayout()->createBlock('msp_unifeed/adminhtml_unifeed_edit_tabs'));
		
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$this->_prapareAction();
		$model = $this->getFeed();
		
		$feedId = $model->getId();

		if ($this->getRequest()->getPost())
		{
			try
			{
				$postData = $this->getRequest()->getPost();
				$feedData = $postData['feed'];
				
				$feedData['code'] = trim($feedData['code']);
				
				if (!preg_match('/^[a-z0-9]+$/', $feedData['code']))
				{
					trigger_error(Mage::helper('msp_unifeed')->__('Invalid code format'));
				}

				$model
					->setData('name', $feedData['name'])
					->setData('code', $feedData['code'])
					->setData('store_id', $feedData['store_id'])
					->setData('msp_unifeed_template_id', $feedData['msp_unifeed_template_id'])
					->setData('ga_source', $feedData['ga_source'])
					->setData('ga_term', $feedData['ga_term'])
					->setData('ga_content', $feedData['ga_content'])
					->setData('ga_name', $feedData['ga_name'])
					->setData('ga_medium', $feedData['ga_medium'])
					->setData('status', $feedData['status']);

				$model->save();
				
				if (!$feedId)
					$feedId = $model->getId();
				
				if ($feedId)
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feed was successfully saved'));
				else
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('One error occurred while trying to save'));
	
				if ($feedId)
				{
					$productFeed = Mage::getModel('msp_unifeed/product_feed');
					if ($postData['add_sku'])
					{
						$skus = preg_split("/[\s\n]+/", $postData['add_sku']);
						$res = $productFeed->addProducts(array($feedId), $skus, true);

						if ($res)
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('%d products were added to this feed', $res));
						else
							Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('No new products were added to this feed'));
					}

					if ($postData['del_sku'])
					{
						$skus = preg_split("/[\s\n]+/", $postData['del_sku']);
						$res = $productFeed->removeProducts(array($feedId), $skus, true);

						if ($res)
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('%d products were removed from this feed', $res));
						else
							Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('No new products were removed from this feed'));
					}

					if (!$postData['categories'])
						$postData['categories'] = '';
					
					$ids = explode(",", $postData['categories']);
					$res = $productFeed->setCategories($feedId, $ids);
					
					$this->_redirect('*/*/edit', array('id' => $feedId));
					return;
				}
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $feedId));
				return;
			}
		}
		
		$this->_redirect('*/*/index');
	}
	
	public function deleteAction()
	{
		$this->_prapareAction();
		$model = $this->getFeed();

		if ($model->getId())
		{
			try
			{
				$model->delete();
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feed was successfully deleted'));
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
	
	protected function getRecursiveTree($res)
	{
		$out = array();
 		foreach ($res as $item)
 		{
			$childrenCount = array_key_exists('subs', $item) ? sizeof($item['subs']) : 0;
  			
  			$info = array(
 				'id'		=> $item['id'],
 				'text'		=> $item['title'],
				'expanded'	=> true,
				'checked'	=> $item['checked'],
 				'leaf'		=> !$childrenCount,
 				'allowDrop'	=> true,
 				'allowDrag'	=> true,
 				'cls'		=> 'folder'
 			);
			
 			if ($childrenCount)
			{
				$info['children'] = $this->getRecursiveTree($item['subs']);
			}

 			$out[] = $info;
 		}
		
		return $out;
	}
	
	public function jsonAction()
 	{
 		$this->_prapareAction();
		
		$categoryId = $this->getRequest()->getParam('node');
 		$feed = $this->getFeed();
		
		$model = Mage::getModel('msp_unifeed/tree');
		$model->setFeed($feed);
		
 		if ($categoryId)
 		{
 			$res = $model->getCategory($categoryId, 100);
 			if (array_key_exists('subs', $res))
 				$res = $res['subs'];
 			else
 				$res = array();
 		}
 		else
 		{
 			$res = $model->getRootCategory(100);
 		}
		
		$out = $this->getRecursiveTree($res);

 		$this->getResponse()->setBody(json_encode($out));
 	}
}
