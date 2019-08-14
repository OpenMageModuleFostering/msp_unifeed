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

class MSP_Unifeed_Model_Resource_Catalog_Eav_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    public function addCategoriesFilter($categories)
    {
        $alias = 'cat_index';
        $categoryCondition = $this->getConnection()->quoteInto(
            $alias.'.product_id=e.entity_id AND '.$alias.'.store_id=? AND ',
            $this->getStoreId()
        );

        $categoryCondition.= $alias.'.category_id IN ('.join(',', $categories).')';

        $this->getSelect()
            ->distinct()
            ->joinInner(
                array($alias => $this->getTable('catalog/category_product_index')),
                $categoryCondition,
                array()
            );

        $this->_categoryIndexJoined = true;
        $this->_joinFields['position'] = array('table'=>$alias, 'field'=>'position' );

        return $this;
    }
}