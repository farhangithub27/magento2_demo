<?php
/*
 * Collections are created in ResourceModel folder (namespace) as they are also resource models. A folder named same as EquipmentItem (same name EquipmentItem.php in Model Folder and
 * EquipmentItem.php in ResourceModel Folder) is created under resource model. Inside this folder Collection.php is created.
 */
namespace Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lmap\EquipmentStore\Model\EquipmentItem;
use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem as EquipmentItemResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(EquipmentItem::class, EquipmentItemResource::class);
        /*
         * It seems that collection resource model kind of connects EquipmentItem model and EquipmentItem ResourceModel
         */
    }
}