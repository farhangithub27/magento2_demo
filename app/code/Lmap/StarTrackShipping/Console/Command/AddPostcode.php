<?php
// In order to activate this command we have define a di.xml file in etc folder as well with type Magento\Framework\Console\CommandList
namespace Lmap\StarTrackShipping\Console\Command;

use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lmap\StarTrackShipping\Model\StarTrackRatesFactory;
use Magento\Framework\Console\Cli;


class AddPostcode extends Command
{
    const INPUT_KEY_POSTCODE = 'postcode';
    const INPUT_KEY_ZONE = 'zone';
    const INPUT_KEY_BASIC = 'basic';
    const INPUT_KEY_RATE_PER_KG = 'rate_per_kg';
    const INPUT_KEY_MINIMUM = 'minimum';

    private $rateFactory;
    private $logger;
    //private $eventManager;

    public function __construct(StarTrackRatesFactory $rateFactory, LoggerInterface $logger)
    //public function __construct(EquipmentItemFactory $itemFactory, ManagerInterface $eventManager)
    //public function __construct(EquipmentItemFactory $itemFactory)
    {
        $this->rateFactory = $rateFactory;
        $this->logger = $logger;
        //$this->eventManager = $eventManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lmap:postcode:add')
            //Setting the name of the command that's going to be used from command line.
            ->addArgument(
                self::INPUT_KEY_POSTCODE,
                InputArgument::REQUIRED,
                'postcode'
            )->addArgument(
                self::INPUT_KEY_ZONE,
                InputArgument::REQUIRED,
                'Zone'
            )->addArgument(
                self::INPUT_KEY_BASIC,
                InputArgument::REQUIRED,
                'Basic Rate'
            )->addArgument(
                self::INPUT_KEY_RATE_PER_KG,
                InputArgument::REQUIRED,
                'Rate_Per_Kg'
            )->addArgument(
                self::INPUT_KEY_MINIMUM,
                InputArgument::REQUIRED,
                'Minimum Rate'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $item = $this->rateFactory->create();
        $item->setPostcode($input->getArgument(self::INPUT_KEY_POSTCODE));
        // setPostcode is the magic method.
        $item->setZone($input->getArgument(self::INPUT_KEY_ZONE));
        // setZone is the magic method.
        $item->setBasic($input->getArgument(self::INPUT_KEY_BASIC));
        $item->setRatePerKg($input->getArgument(self::INPUT_KEY_RATE_PER_KG));
        $item->setMinimum($input->getArgument(self::INPUT_KEY_MINIMUM));
        $item->setIsObjectNew(true);
        $item->save();
        $this->logger->debug('New Postcode Rate Entered'); //refers to code in di.xml file from line 13 to line 57.
        //$this->eventManager->dispatch('lmap_command',['object'=>$item]);

        return Cli::RETURN_SUCCESS;
    }
}
