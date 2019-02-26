<?php

namespace Lmap\EquipmentStore\Setup;

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
            $setup->getTable('lmap_equipment_items'),
            [
                'equipment_name' => 'Equipment 1'
            ]
        );

        $setup->getConnection()->insert(
            $setup->getTable('lmap_equipment_items'),
            [
                'equipment_name' => 'Equipment 2'
            ]
        );

        $setup->endSetup();
    }
}