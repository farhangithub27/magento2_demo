<?php

namespace Lmap\EquipmentStore\Block;

use Magento\Framework\View\Element\Template;
use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem\Collection;
use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem\CollectionFactory;

class Hello extends Template
{
    private $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Lmap\EquipmentStore\Model\EquipmentItem[]
     */
    public function getItems()
    {
        return $this->collectionFactory->create()->getItems();
    }
    /*
    public function getEquipmentName()
    {
        return $this->collectionFactory->create()->getColumnValues('equipment_name');
    }*/
}