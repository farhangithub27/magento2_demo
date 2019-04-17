<?php
/*
 * Setup folder contains database related scripts.
 * When module is installed or enabled then database install scripts in setup folder automatically runs and gets associated with the module.
 * If your model is already installed then schema and data install scripts will not work. You have to use schema upgrade and data upgrade scripts.
 * Hack the database and delete the Lmap_EquipmentStore module from setup_module table.
 * Then run following command in bash
 *  bin/magento setup:upgrade
 * This will install the module and relevant database install scripts again with version mentioned in module.xml file.
 * Then run sql commands to verify
 * select * FROM magento23demo.setup_module where module='Lmap_EquipmentStore';
 * select * FROM magento23demo.lmap_equipment_items;
 */
namespace Lmap\StarTrackShipping\Setup_oldway;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->insert(
            $setup->getTable('lmap_shipping_tablerate'),
            [
                'postcode' => 2600,
                'zone' => 'NC3',
                'basic' => 9.0816,
                'rate_per_kg' => 0.4832,
                'minimum' => 11.4127
            ]
        );


        $setup->endSetup();
    }
}