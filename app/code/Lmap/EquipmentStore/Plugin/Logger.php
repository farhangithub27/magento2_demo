<?php
// A plugin, or interceptor, is a class that modifies the behavior of public class functions by intercepting a function
// call and running code before, after, or around that function call. This allows you to substitute or extend the behavior of original, public methods for any class or interface.

// They can used to extend functionality by creating"before", "after" or "around" plugins.
// In previous coding we added observer customization based on events. Not all magento functionality is covered by events.
// hence plugin in used.

// We will create a plugin for our AddEquipmentItem Cli command for logging purpose. But execute method in /var/www/html/magento23demo/app/code/Lmap/EquipmentStore/Console/Command/AddEquipmentItem.php
// is protected hence we cannot create plugin for that.
// However, our AddEquipmentItem command extends Symfony\Component\Console\Command\Command class and it has a public function run. which we can use to create plugin.
// /var/www/html/magento23demo/vendor/symfony/console/Command/Command.php

namespace Lmap\EquipmentStore\Plugin;

use Lmap\EquipmentStore\Console\Command\AddEquipmentItem;
use Lmap\EquipmentStore\Console\Command\DeleteEquipmentItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logger
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function beforeRun(
        AddEquipmentItem $command,
        //DeleteEquipmentItem $command,
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('beforeExecute');
    }

    public function aroundRun(
        AddEquipmentItem $command,
        //DeleteEquipmentItem $command,
        \Closure $proceed, // Cab be used to invoke next plugin or original class method
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('aroundExecute before call');
        $proceed->call($command, $input, $output);
        $output->writeln('aroundExecute after call');
        $this->output = $output; // Assigning $output to output property of this class to use it in afterRun method
    }

    public function afterRun(AddEquipmentItem $command)
    {
        $this->output->writeln('afterExecute');
    }
}