<?php
/*
 * This is frontend controller
 * localhost/frontName/controllerName(it's a folder inside /Lmap/EquipmentStore/Controller/)/actionName
 * frontName is defined in this routes.xml file.
 */
namespace Lmap\EquipmentStore\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $result */
        //return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents('Hello Admins');
        return $result;
    }
}
