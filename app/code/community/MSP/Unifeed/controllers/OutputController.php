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
 
class MSP_Unifeed_OutputController extends Mage_Core_Controller_Front_Action
{
	protected $_feed;
	
	const XML_UNIFEED_GENERAL_ENABLED = 'msp_unifeed/general/enabled';
	
	protected function getFeed($feedCode = null)
	{
		if (!$this->_feed)
			$this->_feed = Mage::getModel('msp_unifeed/feed');
		
		if (!is_null($feedCode))
			$this->_feed->load($feedCode, 'code');
		
		return $this->_feed;
	}
	
	protected function setContentType($type)
	{
		header('Content-Type: '.$type);
		return $this;
	}

	public function buildAction()
	{exit;
		$feedCode = $this->getRequest()->getParam('code');

		$feed = $this->getFeed($feedCode);
		if (!Mage::getStoreConfig(self::XML_UNIFEED_GENERAL_ENABLED) || !$feed->getId() || !$feed->getStatus())
		{
			$this->norouteAction();
			return;
		}

		$feed->build();
	}

	public function indexAction()
	{
		$feedCode = $this->getRequest()->getParam('code');

		$feed = $this->getFeed($feedCode);
		if (
			!Mage::getStoreConfig(self::XML_UNIFEED_GENERAL_ENABLED) ||
			!$feed->getId() ||
			!$feed->getStatus() ||
			!file_exists($feed->getFinalFileName())
		) {
			$this->norouteAction();
			return;
		}
		
		switch ($feed->getTemplate()->getType())
		{
			case MSP_Unifeed_Model_Template_Type::TYPE_CSV:
				$this->setContentType('text/csv');
				break;
			
			case MSP_Unifeed_Model_Template_Type::TYPE_TXT:
				$this->setContentType('text/plain');
				break;
				
			case MSP_Unifeed_Model_Template_Type::TYPE_XML:
				$this->setContentType('text/xml');
				break;
				
			case MSP_Unifeed_Model_Template_Type::TYPE_HTML:
				$this->setContentType('text/html');
				break;
		}
		
		readfile($feed->getFinalFileName());
	}
}
