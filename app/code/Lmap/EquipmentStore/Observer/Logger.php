<?php

namespace Lmap\EquipmentStore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Logger implements ObserverInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->debug($observer->getEvent()->getObject()->getEquipmentName() . " Description : " . $observer->getEvent()->getObject()->getDescription());
        //getObject is the magic method
        //echo("Observer/Logger Value: " . var_dump($observer->getEvent()->getObject()->getEquipmentName()));
        //echo("Observer/Logger Value: " . var_dump($observer->getEvent()->getObject()->getDescription()));
    }
}