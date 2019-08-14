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


class MSP_Unifeed_Model_Feed_Status extends Mage_Core_Model_Abstract
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	static public function getHashArray()
	{
		return array(
			self::STATUS_ENABLED=>Mage::helper('msp_unifeed')->__('Enabled'),
			self::STATUS_DISABLED=>Mage::helper('msp_unifeed')->__('Disabled')
		);
	}
	
	static public function getOptionArray()
	{
		$out = array();
		$options = self::getHashArray();
		
		foreach ($options as $k => $v)
			$out[] = array('label' => $v, 'value' => $k);
		
		return $out;		
	}
}
