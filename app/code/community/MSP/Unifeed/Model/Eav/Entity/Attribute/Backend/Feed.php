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

class MSP_Unifeed_Model_Eav_Entity_Attribute_Backend_Feed extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	public function beforeSave($object)
	{
		$data = $object->getData($this->getAttribute()->getAttributeCode());
		if (is_array($data))
		{
			$data = array_unique($data);
			$value = preg_replace('/:+/', ':', ':'.implode(':', $data).':');
			$object->setData($this->getAttribute()->getAttributeCode(), $value);
		}
		else
		{
			$object->setData($this->getAttribute()->getAttributeCode(), '');
		}

		return parent::beforeSave($object);
	}
}
