<?php
// In order to activate this command we have define a di.xml file in etc folder as well with type Magento\Framework\Console\CommandList
namespace Lmap\EquipmentStore\Console\Command;

use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lmap\EquipmentStore\Model\EquipmentItemFactory;
use Magento\Framework\Console\Cli;


class AddEquipmentItem extends Command
{
    const INPUT_KEY_NAME = 'name';
    const INPUT_KEY_DESCRIPTION = 'description';

    private $itemFactory;
    //private $logger;
    //private $eventManager;

    //public function __construct(EquipmentItemFactory $itemFactory, LoggerInterface $logger)
    //public function __construct(EquipmentItemFactory $itemFactory, ManagerInterface $eventManager)
    public function __construct(EquipmentItemFactory $itemFactory)
    {
        $this->itemFactory = $itemFactory;
        //$this->logger = $logger;
        //$this->eventManager = $eventManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lmap:equipmentitem:add')
            //Setting the name of the command that's going to be used from command line.
            ->addArgument(
                self::INPUT_KEY_NAME,
                InputArgument::REQUIRED,
                'Item name'
            )->addArgument(
                self::INPUT_KEY_DESCRIPTION,
                InputArgument::OPTIONAL,
                'Item description'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $item = $this->itemFactory->create();
        $item->setEquipmentName($input->getArgument(self::INPUT_KEY_NAME));
        // setEquipmentName is the magic method.
        $item->setDescription($input->getArgument(self::INPUT_KEY_DESCRIPTION));
        // setDescription is the magic method.
        $item->setIsObjectNew(true);
        $item->save();
        //$this->logger-debug('Item was created'); refers to code in di.xml file from line 13 to line 57.
        //$this->eventManager->dispatch('lmap_command',['object'=>$item]);

        return Cli::RETURN_SUCCESS;
    }
}
