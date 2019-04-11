<?php

//namespace Lmap\StarTrackShipping\Model\Carrier;
namespace Lmap\StarTrackShipping\Model\ResourceModel;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class DebugHandler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/debug_lmap_shipping.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}