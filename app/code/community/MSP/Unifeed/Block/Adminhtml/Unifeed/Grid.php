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
 
class MSP_Unifeed_Block_Adminhtml_Unifeed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('unifeedGrid');
		$this->_controller = 'msp_unifeedadm';
	}
	
	protected function _prepareCollection()
	{
 		$model = Mage::getModel('msp_unifeed/feed');
 		$collection = $model->getCollection();
 		$this->setCollection($collection);
 		
 		$this->setDefaultSort('name');
		$this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

		return parent::_prepareCollection();
	}
	
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
		
		if ($row->getBuildStatus() == MSP_Unifeed_Model_Feed::BUILD_STATUS_NOT_READY)
		{
			$class = 'critical';
			$text = $this->__('Not Ready');
		}
		elseif ($row->getBuildStatus() == MSP_Unifeed_Model_Feed::BUILD_STATUS_BUILDING)
		{
			$class = 'major';
			$text = $this->__('Building');
		}
		elseif ($row->getBuildStatus() == MSP_Unifeed_Model_Feed::BUILD_STATUS_UPDATING)
		{
			$class = 'minor';
			$text = $this->__('Updating');
		}
		elseif ($row->getBuildStatus() == MSP_Unifeed_Model_Feed::BUILD_STATUS_MARKED_REBUILD)
		{
			$class = 'minor';
			$text = $this->__('On queue');
		}
		else
		{
			$class = 'notice';
			$text = $this->__('Ready');
		}
			
		return '<span class="grid-severity-'.$class.'"><span>'.$text.'</span></span>';
    }
	
	public function decorateDate($value, $row, $column, $isExport)
    {
        $class = '';
		
		$date = $row->getBuildDate();
		
		if (!$date)
			return '';
		
		return date('Y-m-d H:i', $date);
    }

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'        => Mage::helper('msp_unifeed')->__('ID'),
			'align'         => 'right',
			'width'         => '50px',
			'index'         => 'msp_unifeed_feed_id',
		));
		
		$this->addColumn('name', array(
			'header'        => Mage::helper('msp_unifeed')->__('Name'),
			'align'         => 'left',
			'index'         => 'name',
			'type'          => 'text',
			'truncate'      => 50,
			'escape'        => true,
		));
		
		$this->addColumn('code', array(
			'header'        => Mage::helper('msp_unifeed')->__('Code'),
			'align'         => 'left',
			'index'         => 'code',
			'type'          => 'text',
			'escape'        => true,
		));
		
		$this->addColumn('msp_unifeed_template_id', array(
			'header' => Mage::helper('msp_unifeed')->__('Template'),
			'index' => 'msp_unifeed_template_id',
			'type' => 'options',
			'options' => Mage::getSingleton('msp_unifeed/template')->getHashArray(),	
		));
		
		$storeHash = array();
		$storeOptions = Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray();
		foreach ($storeOptions as $store)
			$storeHash[$store['value']] = $store['label'];
		
		$this->addColumn('store_id', array(
			'header' => Mage::helper('msp_unifeed')->__('Store'),
			'index' => 'store_id',
			'type' => 'options',
			'options' => $storeHash,	
		));
		
		$this->addColumn('status', array(
			'header' => Mage::helper('msp_unifeed')->__('Status'),
			'index' => 'status',
			'type' => 'options',
			'width' => '100px',
			'options' => Mage::getSingleton('msp_unifeed/feed_status')->getHashArray(),	
		));
		
		$this->addColumn('build_status', array(
            'header' => $this->__('Build Status'),
            'width' => '120',
            'filter' => false,
            'align' => 'left',
            'frame_callback' => array($this, 'decorateStatus')
        ));
		
		$this->addColumn('build_date', array(
            'header' => $this->__('Build Date'),
            'width' => '120',
            'filter' => false,
            'align' => 'left',
            'frame_callback' => array($this, 'decorateDate')
        ));
		
		$this->addColumn('action',
            array(
                'header'    => Mage::helper('msp_unifeed')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption'   => Mage::helper('msp_unifeed')->__('Edit'),
                    'url'       => array(
                        'base'=>'*/*/edit'
                    ),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'action',
        ));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array(
			'id' => $row->getId(),
		));
	}
}
