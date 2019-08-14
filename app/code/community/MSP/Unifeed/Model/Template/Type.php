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

class MSP_Unifeed_Model_Template_Type extends Mage_Core_Model_Abstract
{
	const TYPE_CSV = 'csv';
	const TYPE_XML = 'xml';
	const TYPE_TXT = 'txt';
	const TYPE_HTML = 'html';
	
	static public function getHashArray()
	{
		return array(
			self::TYPE_CSV=>Mage::helper('msp_unifeed')->__('CSV'),
			self::TYPE_TXT=>Mage::helper('msp_unifeed')->__('TXT'),
			self::TYPE_XML=>Mage::helper('msp_unifeed')->__('XML'),
			self::TYPE_HTML=>Mage::helper('msp_unifeed')->__('HTML'),
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
