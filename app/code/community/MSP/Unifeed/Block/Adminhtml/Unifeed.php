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
 
class MSP_Unifeed_Block_Adminhtml_Unifeed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
 		$this->_blockGroup = 'msp_unifeed';
		$this->_controller = 'adminhtml_unifeed';
		$this->_headerText = Mage::helper('msp_unifeed')->__('Feeds List');
		$this->_addButtonLabel = Mage::helper('msp_unifeed')->__('Add New Feed');
		
		$this->_addButton('rebuild', array(
			'label'     => Mage::helper('msp_unifeed')->__('Rebuild Feeds'),
			'onclick'   => "self.location.href='".$this->getUrl('*/*/rebuild/')."'",
			'level'     => -1
		));
		
		parent::__construct();
    }
}
