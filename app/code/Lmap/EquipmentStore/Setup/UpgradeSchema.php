<?php
/*
 * Adding new column description to the lmap_equipment_items table
 */
namespace Lmap\EquipmentStore\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /*
             * In order to track changes above line is added
             */
            $setup->getConnection()->addColumn(
                $setup->getTable('lmap_equipment_items'),
                'description',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Equipment Description'
                ]
            );
        }
        /*
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'base_tax_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'comment' => 'Base Tax Amount'
                ]
            );
        }*/

        $setup->endSetup();
    }
}