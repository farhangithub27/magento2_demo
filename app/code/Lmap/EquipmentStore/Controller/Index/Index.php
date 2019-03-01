<?php
/*
 * This is frontend controller
 * localhost/frontName/controllerName(it's a folder inside /Lmap/EquipmentStore/Controller/)/actionName
 * frontName is defined in this routes.xml file.
 */
namespace Lmap\EquipmentStore\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        // This execute method runs when we enter url in the browser

        //Switch off this Raw Response for frontend templates to work in view folder.

        /** @var \Magento\Framework\Controller\Result\Raw $result */
        /*
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents('Hello World');
        return $result;*/


        // Below statement will display content from the database
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
