<?php
// In order to activate this command we have define a di.xml file in etc folder as well with type Magento\Framework\Console\CommandList
// This method doesnt work so far
// https://github.com/webkul/magento2-custom-command/blob/master/Console/Command/OrderDeleteCommand.php
namespace Lmap\EquipmentStore\Console\Command;

use function PHPSTORM_META\type;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lmap\EquipmentStore\Model\EquipmentItemFactory;
use Lmap\EquipmentStore\Model\ResourceModel\EquipmentItem\CollectionFactory;
use Lmap\EquipmentStore\Model\EquipmentItem;
use Magento\Framework\Console\Cli;
use Zend\Debug\Debug;

class DeleteEquipmentItem extends Command
{
    const INPUT_KEY_NAME = 'name';
    const INPUT_KEY_DESCRIPTION = 'description';

    private $itemFactory;
    private $equipmentItem;
    private $collectionFactory;

    public function __construct(EquipmentItemFactory $itemFactory, EquipmentItem $equipmentItem, CollectionFactory $collectionFactory)
    {
        $this->itemFactory = $itemFactory;
        $this->equipmentItem = $equipmentItem;
        $this->collectionFactory = $collectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lmap:equipmentitem:delete')
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
        $itemName = $input->getArgument(self::INPUT_KEY_NAME);
        $itemCollection = $this->collectionFactory->create()->getItemsByColumnValue('equipment_name',$itemName); //array type
        //$equipment = $this->itemFactory->create();
        $output->writeln('<info>Collection items are ' . var_dump($itemCollection) . '</info>');
        foreach($itemCollection as $key=>$equipment)
        {
            $equipment->load($equipment['id']);
            $equipment->delete();
        }


        echo gettype($itemCollection);
        //$items = $this->itemFactory->create()->getCollection()->getItemsByColumnValue('equipment_name',$itemName);



        //$item->unsEquipmentName($input->getArgument(self::INPUT_KEY_NAME));
        // unsEquipmentName is unset($_data['name']) the magic method.
        //http://www.coolryan.com/magento/2012/04/06/magic-methods-inside-magento/
        //$item->setDescription($input->getArgument(self::INPUT_KEY_DESCRIPTION));
        // setDescription is the magic method.
        //$item->setIsObjectNew(true);
        //$item->save();

        return Cli::RETURN_SUCCESS;
    }
}
