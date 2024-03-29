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

class MSP_Unifeed_Model_Observer
{
    const XML_UNIFEED_GENERAL_ENABLED = "msp_unifeed/general/enabled";
    const XML_UNIFEED_GENERAL_DAILY_REBUILD = "msp_unifeed/general/daily-rebuild";

    public function rebuildCatalog()
    {
        if (!Mage::getStoreConfig(self::XML_UNIFEED_GENERAL_ENABLED))
            return;

        if (!Mage::getStoreConfig(self::XML_UNIFEED_GENERAL_DAILY_REBUILD))
            return;

        $feeds = Mage::getModel('msp_unifeed/feed')->getCollection();
        foreach ($feeds as $feed)
        {
            $feed->setRebuildFlag();
        }
    }
}
