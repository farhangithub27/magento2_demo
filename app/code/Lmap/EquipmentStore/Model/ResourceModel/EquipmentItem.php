<?php

namespace Lmap\EquipmentStore\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EquipmentItem extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('lmap_equipment_items', 'id');
    }
}