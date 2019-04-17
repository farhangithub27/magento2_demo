<?php

/*
 * Setup folder contains database related scripts.
 * When module is installed or enabled then database install scripts in setup folder automatically runs and gets associated with the module.
 * If your model is already installed then schema and data install scripts will not work. You have to use schema upgrade and data upgrade scripts.
 * Hack the database and delete the Lmap_EquipmentStore module from setup_module table.
 * Then run following command in bash
 *  bin/magento setup:upgrade
 * This will install the module and relevant database install scripts again with with version mentioned in module.xml file.
 * Then run sql commands to verify
 * select * FROM magento23demo.setup_module where module='Lmap_EquipmentStore';
 * select * FROM magento23demo.lmap_equipment_items;
 */
namespace Lmap\StarTrackShipping\Setup_oldway;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('lmap_shipping_tablerate')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Item ID'
        )->addColumn(
            'postcode',
            Table::TYPE_INTEGER,
            4,
            ['nullable' => false ,'unique' => true],
            'Post Code'
        )->addColumn(
            'zone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Zone'
        )->addColumn(
            'basic',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Basic Rate'
        )->addColumn(
            'rate_per_kg',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Rate Per Kilogram'
        )->addColumn(
            'minimum',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Minimum Rate'
        )->addIndex(
            $setup->getIdxName('lmap_shipping_tablerate', ['postcode']),
            ['postcode']
        )->setComment(
            'Lmap Shipping Table Rate'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}