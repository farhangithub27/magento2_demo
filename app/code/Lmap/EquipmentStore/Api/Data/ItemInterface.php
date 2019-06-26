<?php

namespace Lmap\EquipmentStore\Api\Data;

interface ItemInterface
{
    /**
     * @return string
     */
    public function getEquipmentName();
    // Magic getter

    /**
     * @return mixed
     */
    public function setEquipmentName();

    /**
     * @return string|null
     */
    public function getDescription();
    // magic getter
}
