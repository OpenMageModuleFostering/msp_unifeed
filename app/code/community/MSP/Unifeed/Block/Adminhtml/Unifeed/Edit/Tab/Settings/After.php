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

class MSP_Unifeed_Block_Adminhtml_Unifeed_Edit_Tab_Settings_After extends Mage_Adminhtml_Block_Widget
{
	protected $_feedId;
	
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('msp_unifeed/edit/settings/after.phtml');
    }
	
	public function setFeedId($feedId)
	{
		$this->_feedId = $feedId;
		return $this;
	}
	
	public function getFeedId()
	{
		return $this->_feedId;
	}
	
	public function getFeedUrl()
	{
		$model = Mage::getModel('msp_unifeed/feed')->load($this->getFeedId());
		
		$url = Mage::app()->getStore($model->getStoreId())->getUrl('msp_unifeed/output/index', array('code' => $model->getCode()));
		$url = preg_replace("/\?.+$/", '', $url);
		
		return $url;
	}
}
