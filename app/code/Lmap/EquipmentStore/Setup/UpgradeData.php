<?php

namespace Lmap\EquipmentStore\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->update(
                $setup->getTable('lmap_equipment_items'),
                [
                    'description' => 'Default description'
                ],
                $setup->getConnection()->quoteInto('id = ?', 1)
                /*
                 * Above qouteInto is required to add description into item with id =1
                 * As description field is nullable hence other items will have null description.
                 * Also now change the version in module.xml to 1.0.1 too.
                 */
            );
        }

        $setup->endSetup();
    }
}