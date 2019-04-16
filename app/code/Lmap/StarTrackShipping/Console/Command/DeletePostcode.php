<?php
// In order to activate this command we have define a di.xml file in etc folder as well with type Magento\Framework\Console\CommandList

// https://github.com/webkul/magento2-custom-command/blob/master/Console/Command/OrderDeleteCommand.php
namespace Lmap\StarTrackShipping\Console\Command;

use Magento\Framework\Event\ManagerInterface;
use function PHPSTORM_META\type;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lmap\StarTrackShipping\Model\Carrier\StarTrackRatesFactory;
use Lmap\StarTrackShipping\Model\ResourceModel\Carrier\StarTrackRates\CollectionFactory;
use Magento\Framework\Console\Cli;




class DeletePostcode extends Command
{
    const INPUT_KEY_POSTCODE = 'postcode';
    const INPUT_KEY_ZONE = 'zone';
    const INPUT_KEY_BASIC = 'basic';
    const INPUT_KEY_RATE_PER_KG = 'rate_per_kg';
    const INPUT_KEY_MINIMUM = 'minimum';

    private $rateFactory;
    private $equipmentItem;
    private $collectionFactory;
    private $eventManager;
    private $logger;

    public function __construct(StarTrackRatesFactory $rateFactory, CollectionFactory $collectionFactory,LoggerInterface $logger)
    {
        $this->rateFactory = $rateFactory;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        //$this->eventManager = $eventManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lmap:postcode:delete')
            //Setting the name of the command that's going to be used from command line.
            ->addArgument(
                self::INPUT_KEY_POSTCODE,
                InputArgument::REQUIRED,
                'postcode'
            )->addArgument(
                self::INPUT_KEY_ZONE,
                InputArgument::OPTIONAL,
                'Zone'
            )->addArgument(
                self::INPUT_KEY_BASIC,
                InputArgument::OPTIONAL,
                'Basic Rate'
            )->addArgument(
                self::INPUT_KEY_RATE_PER_KG,
                InputArgument::OPTIONAL,
                'Rate_Per_Kg'
            )->addArgument(
                self::INPUT_KEY_MINIMUM,
                InputArgument::OPTIONAL,
                'Minimum Rate'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $itemName = $input->getArgument(self::INPUT_KEY_POSTCODE);
        $itemCollection = $this->collectionFactory->create()->getItemsByColumnValue('postcode',$itemName);
        // $itemCollection = $this->collectionFactory->create(); will give an array type
        $output->writeln('<info>Input Postcode is:  ' . var_dump($itemName) . '</info>');

        $output->writeln('<info>Collection items are ' . var_dump($itemCollection) . '</info>');
        foreach($itemCollection as $key=>$rate)
        {
            $rate->load($rate['id']);
            $rate->delete();
        }

        // Checking the what query are we getting
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('postcode',array('eq'=>2600));
        $output->writeln('<info> collection query is: ' . $collection->getSelect()->__toString() . '</info>');

        //$this->eventManager->dispatch('lmap_command',['object'=>$itemCollection]);
        return Cli::RETURN_SUCCESS;
    }
}
