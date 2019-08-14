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

class MSP_Unifeed_Model_Template_Product extends MSP_Unifeed_Model_Template_Abstract
{
	public function gaDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['url']))
			$params['url'] = $this->_templateVars['product']->getProductUrl();
		
		$amp = $this->getFeed()->getAmp();
				
		if (strpos($params['url'], '?'))
			$params['url'] .= $amp.$this->getFeed()->getGaQueryString();
		else
			$params['url'] .= '?'.$this->getFeed()->getGaQueryString();
		
		return $params['url'];
	}

    public function attributesDirective($construction)
    {
    	$params = $this->_getIncludeParameters($construction[2]);
		
    	$product = $this->_templateVars['product'];
		
		$excludeAttr = array();
		if (isset($params['exclude']))
			$excludeAttr = preg_split('/\s*,\s*/', $params['exclude']);

		$glue = '|';
		if (isset($params['glue']))
			$glue = $params['glue'];
		
        $data = array();
        $attributes = $product->getAttributes();

        foreach ($attributes as $attribute)
        {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr))
            {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode()) || ((string)$value == '') || !$product->getData($attribute->getAttributeCode()))
                    continue;

                if (is_string($value) && strlen($value))
                {
                	if (!$attribute->getIsFilterable())
						continue;
					
					if (!$attribute->getIsVisibleOnFront())
						continue;
					
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getFrontendLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }

		$return = array();
		foreach ($data as $k => $v)
		{
			if (isset($params['uselabel']) && $params['uselabel'])
				$label = $v['label']; //strtolower(preg_replace('/[\W\s]+/', '_', $v['label']));
			else
				$label = $k;
			
			$return[] = $label."=".$v['value'];
		}

        return implode($glue, $return);
    }
}

