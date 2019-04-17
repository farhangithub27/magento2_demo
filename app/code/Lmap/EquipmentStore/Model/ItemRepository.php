<?php

namespace Lmap\EquipmentStore\Model;

use Lmap\EquipmentStore\Api\ItemRepositoryInterface;
use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem\CollectionFactory;

class ItemRepository implements ItemRepositoryInterface
{
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function getList()
    {
        return $this->collectionFactory->create()->getItems();
    }
}
