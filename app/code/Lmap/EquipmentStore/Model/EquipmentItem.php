<?php
/*
 * Model file which will use the Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem.php file to talk to database.
 */
namespace Lmap\EquipmentStore\Model;

use Magento\Framework\Model\AbstractModel;
//use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem;
/*
 * we can also use use Magento\Framework\Model\AbstractExtensibleModel;
 * This will provide ability to store extensible attributes. However, our model is not going to work with extensible attributes hence we use
 * AbstractModel
 */

class EquipmentItem extends AbstractModel
{
    protected $_eventPrefix = 'lmap_equipment_items';
    protected function _construct()
    {
        //$this->_init(EquipmentItem::class);
        $this->_init(\Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem::class);
        /*
         * We can always define setter and getter for all fields in the model however its not required as we always use
         * getdata() magic getter / setter magento data object. hence setter and getters are not created here.
         */
    }
}