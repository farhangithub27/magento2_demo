<?php

namespace Lmap\StarTrackShipping\Model;

use Magento\Framework\Model\AbstractModel;

/*
 * we can also use use Magento\Framework\Model\AbstractExtensibleModel;
 * This will provide ability to store extensible attributes. However, our model is not going to work with extensible attributes hence we use
 * AbstractModel
 */

class StarTrackRates extends AbstractModel
{
    protected $_eventPrefix = 'startrack_rates_event';// This even prefix will be used for coding event (observer) based logging latter on.
    // This prefix is used by AbstractModel to generate events.
    protected function _construct()
    {
        //$this->_init(EquipmentItem::class);
        $this->_init(\Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates::class);
        /*
         * We can always define setter and getter for all fields in the model however its not required as we always use
         * getdata() magic getter / setter magento data object. Hence setter and getters are not created here.
         */
    }
}
