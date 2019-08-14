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

/* @var $this Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('msp_unifeed_feed')};
CREATE TABLE IF NOT EXISTS {$this->getTable('msp_unifeed_feed')} (
`msp_unifeed_feed_id` int(11) NOT NULL auto_increment,
`store_id` int(11) NOT NULL,
`code` char(32) NOT NULL,
`name` varchar(255) NOT NULL,
`status` tinyint(1) NOT NULL default '1',
`msp_unifeed_template_id` int(11) NOT NULL,
`ga_source` text NOT NULL default '',
`ga_medium` text NOT NULL default '',
`ga_term` text NOT NULL default '',
`ga_content` text NOT NULL default '',
`ga_name` text NOT NULL default '',
PRIMARY KEY  (`msp_unifeed_feed_id`),
UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('msp_unifeed_template')};
CREATE TABLE IF NOT EXISTS {$this->getTable('msp_unifeed_template')} (
`msp_unifeed_template_id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`type` varchar(255) NOT NULL,
`template_open` text NOT NULL default '',
`template_product` text NOT NULL default '',
`template_close` text NOT NULL default '',
PRIMARY KEY  (`msp_unifeed_template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1
");
/*
$installer->run("
INSERT INTO {$this->getTable('msp_unifeed_template')} (`name`, `type`, `template_open`, `template_product`, `template_close`) VALUES
('Ciao', 'xml', '<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">\r\n<products>', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\"#\"}}{{/set}}\r\n<product num=\"{{var product.getSku()}}\">\r\n<category>{{htmlescape var=\$cstm_category}}</category>\r\n<brand>{{htmlescape var=\$product.getAttributeText(''manufacturer'')}}</brand>\r\n<internal_ref>{{htmlescape var=\$product.getSku()}}</internal_ref>\r\n<designation>{{htmlescape var=\$product.getName()}}</designation>\r\n<price currency=\"{{var store.getBaseCurrencyCode()}}\">{{format var=\$final_price}}</price>\r\n<availability>{{format decimals=0 var=\$stock.getQty()}}</availability>\r\n<url>{{ga url=\$product.getProductUrl()}}</url>\r\n<img>{{var product.getImageUrl()}}</img>\r\n<decription>{{htmlescape var=\$product.getShortDescription()}}</decription>\r\n</product>', '</products>'),
('Kelkoo', 'xml', '<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">\r\n<products>', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\"#\"}}{{/set}}\r\n<product>\r\n<Category>{{htmlescape var=\$cstm_category}}</Category>\r\n<Manufacturer>{{htmlescape var=\$product.getAttributeText(''manufacturer'')}}</Manufacturer>\r\n<Productname>{{htmlescape var=\$product.getName()}}</Productname>\r\n<Productcode>{{htmlescape  var=\$product.getSku()}}</Productcode>\r\n<Price>{{format var=\$final_price}}</Price>\r\n<Availability>{{if a=\$stock.getQty() b=0 op=gt}}disponibile{{else}}non disponibile{{/if}}</Availability>\r\n<ProductURL>{{ga url=\$product.getProductUrl()}}</ProductURL>\r\n<ImageURL>{{var product.getImageUrl()}}</ImageURL>\r\n<Description>{{htmlescape var=\$product.getShortDescription()}}</Description>\r\n</product>', '</products>'),
('Buyplaza', 'txt', '', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\",\"}}{{/set}}\r\n{{var product.getName()}}|{{var product.getAttributeText(''manufacturer'')}}|{{striptags var=\$product.getShortDescription()}}|{{format var=\$final_price separator=\",\"}}|{{ga url=\$product.getProductUrl()}}|{{var cstm_category}}|{{var product.getImageUrl()}}{{newline}}', ''),
('Ilpiubasso', 'txt', '', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\",\"}}{{/set}}\r\n{{var cstm_category}}|{{var product.getName()}}|{{var product.getAttributeText(''manufacturer'')}}|{{striptags var=\$product.getShortDescription()}}|{{format var=\$final_price separator='',''}}|{{var product.getSku()}}|{{ga url=\$product.getProductUrl()}}|{{if a=\$stock.getQty() b=0 op=gt}}Disponibile{{else}}Non disponibile{{/if}}|{{var product.getImageUrl()}}|Vedi sito<Endrecord>', ''),
('MrWallet', 'xml', '<"."?xml version=\"1.0\" encoding=\"UTF-8\" ?".">\r\n<prodotti>', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\"#\"}}{{/set}}\r\n<prodotto>\r\n<categoria>{{htmlescape var=\$cstm_category}}</categoria>\r\n<marca>{{htmlescape var=\$product.getAttributeText(''manufacturer'')}}</marca>\r\n<nome>{{htmlescape var=\$product.getName()}}</nome>\r\n<id>{{htmlescape var=\$product.getSku()}}</id>\r\n<prezzo>{{format var=\$final_price}}</prezzo>\r\n<url>{{ga url=\$product.getProductUrl()}}</url>\r\n<immagine>{{var product.getImageUrl()}}</immagine>\r\n<descrizione>{{htmlescape var=\$product.getShortDescription()}}</descrizione>\r\n<lingua>{{var locale.getLocaleCode()}}</lingua>\r\n<valuta>{{var store.getBaseCurrencyCode()}}</valuta>\r\n<speseconsegna>-1</speseconsegna>\r\n</prodotto>', '</prodotti>'),
('TrovaPrezzi', 'txt', '', '{{set var=\"cstm_category\"}}{{implode var=\$category_names glue=\",\"}}{{/set}}\r\n{{var product.getName()}}|{{var product.getAttributeText(''manufacturer'')}}|{{striptags var=\$product.getShortDescription()}}|{{format var=\$final_price}}|{{var product.getSku()}}|{{ga url=\$product.getProductUrl()}}|{{if a=\$stock.getQty() b=0 op=gt}}disponibile{{else}}non disponibile{{/if}}|{{var cstm_category}}|{{var product.getImageUrl()}}|-1|{{var product.getSku()}}<endrecord>', '');
");
*/
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$setup->addAttribute('catalog_category', 'msp_unifeed_ids', array(
    'label'				=> 'MSP UniFeed',
    'type'				=> 'varchar',
    'input'				=> '',
    'backend'			=> 'msp_unifeed/eav_entity_attribute_backend_feed',
    'visible'			=> false,
    'visible_on_front'	=> false,
    'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'			=> false
));

$setup->endSetup();
$installer->endSetup();
