<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25/02/19
 * Time: 9:34 AM
 * Without this file the module is invisible to magento. It is referenced in the composer.json file auto load section
 * every module, theme, language pack, library or anything that is built for magento.
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Lmap_EquipmentStore',
    __DIR__
);
