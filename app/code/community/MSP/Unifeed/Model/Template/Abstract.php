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

abstract class MSP_Unifeed_Model_Template_Abstract extends Mage_Core_Model_Email_Template_Filter
{
	const CONSTRUCTION_SET_PATTERN = '/{{set\s*(.*?)}}(.*?){{\\/set\s*}}/si';
	
	protected $_feed;
	
	public function getFeed()
	{
		return $this->_feed;
	}
	
	public function setFeed($feed)
	{
		$this->_feed = $feed;
		return $this;
	}
	
	public function ifDirective($construction)
	{
		$params = $this->_getIncludeParameters(' '.$construction[1]);
		
		if (isset($params['a']) && isset($params['b']) && isset($params['op']))
		{
			$val = false;
			switch ($params['op'])
			{
				case 'eq':
					if ($params['a'] == $params['b'])
						$val = true;
					break;
					
				case 'lt':
					if ($params['a'] < $params['b'])
						$val = true;
					break;
					
				case 'gt':
					if ($params['a'] > $params['b'])
						$val = true;
					break;
					
				case 'le':
					if ($params['a'] <= $params['b'])
						$val = true;
					break;
					
				case 'ge':
					if ($params['a'] >= $params['b'])
						$val = true;
					break;
			}
			
			if (!$val)
			{
				if (isset($construction[3]) && isset($construction[4]))
				{
					return $construction[4];
				}
				return '';
			}
			else
			{
				return $construction[2];
			}
			
			return parent::ifDirective($construction);
		}
	}
	
	protected function formatDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['var']))
			return '';
			
		if (!isset($params['decimals']))
			$params['decimals'] = 2;
			
		if (!isset($params['separator']))
			$params['separator'] = '.';
			
		$allowedTags = null;
		
		return number_format($params['var'], $params['decimals'], $params['separator'], '');
	}
	
	protected function striptagsDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['var']))
			return '';
		
		return strip_tags($params['var']);
	}
	
	protected function csvquoteDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['enc'])) $params['enc'] = '"';
		
		if (!isset($params['var']))
			return $params['enc'].$params['enc'];
		
		$out = $params['enc'].str_replace($params['enc'], $params['enc'].$params['enc'], $params['var']).$params['enc'];
		return $out;
	}
	
	protected function quoteDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['enc'])) $params['enc'] = '"';
		
		if (!isset($params['var']))
			return $params['enc'].$params['enc'];
		
		$out = $params['var'];
		$out = str_replace("\\", "\\\\", $out);
		$out = str_replace("\n", "\\n", $out);
		$out = str_replace($params['enc'], "\\".$params['enc'], $out);
		
		$out = $params['enc'].$out.$params['enc'];
		return $out;
	}
	
	protected function htmlunescapeDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['var']))
			return '';
		
		$out = html_entity_decode($params['var']);
		return $out;
	}
	
	protected function nlbrDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['var']))
			return '';
		
		$out = preg_replace("/[\n\r]+/", '', nl2br($params['var']));
		return $out;
	}
	
	protected function newlineDirective($construction)
	{
		return "\n";
	}
	
	protected function implodeDirective($construction)
	{
		$params = $this->_getIncludeParameters($construction[2]);
		if (!isset($params['glue']))
			$params['glue'] = '/';
			
		if (!isset($params['var']))
			return '';
			
		return implode($params['glue'], $params['var']);
	}
	
	public function setDirective($construction)
	{
		$params = $this->_getIncludeParameters(' '.$construction[1]);
		if (!isset($params['var']))
			return '';

		$this->_templateVars[$params['var']] = $this->filter($construction[2]);
		return '';
	}
	
	public function filter($value)
	{
		foreach (array(self::CONSTRUCTION_SET_PATTERN=>'setDirective') as $pattern=>$directive)
		{
			if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER))
			{
				foreach ($constructions as $index=>$construction)
				{
					$replacedValue = '';
					$callback = array($this, $directive);
					if (!is_callable($callback))
					{
						continue;
					}
					try
					{
						$replacedValue = call_user_func($callback, $construction);
					}
					catch(Exception $e)
					{
						throw $e;
					}
					
					$value = str_replace($construction[0], $replacedValue, $value);
				}
			}
		}
		
		$value = trim($value);
		$value = parent::filter($value);
		
		return $value;
	}
}

