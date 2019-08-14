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
 
class MSP_Unifeed_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('templateGrid');
		$this->_controller = 'msp_unifeed_adm';
	}
	
	protected function _prepareCollection()
	{
 		$model = Mage::getModel('msp_unifeed/template');
 		$collection = $model->getCollection();
 		$this->setCollection($collection);
 		
 		$this->setDefaultSort('name');
		$this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'        => Mage::helper('msp_unifeed')->__('ID'),
			'align'         => 'right',
			'width'         => '50px',
			'index'         => 'msp_unifeed_template_id',
		));
		
		$this->addColumn('name', array(
			'header'        => Mage::helper('msp_unifeed')->__('Name'),
			'align'         => 'left',
			'index'         => 'name',
			'type'          => 'text',
			'truncate'      => 50,
			'escape'        => true,
		));
		
		$this->addColumn('type', array(
			'header' => Mage::helper('msp_unifeed')->__('Format Type'),
			'index' => 'type',
			'type' => 'options',
			'options' => Mage::getSingleton('msp_unifeed/template_type')->getHashArray(),	
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
                'index'     => 'msp_unifeed',
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
