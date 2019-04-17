<?php

namespace Lmap\EquipmentStore\Api;

interface ItemRepositoryInterface
// It will provide access to all items
{
    /**
     * @return \Lmap\EquipmentStore\Api\Data\ItemInterface[]
     */
    public function getList();
}
