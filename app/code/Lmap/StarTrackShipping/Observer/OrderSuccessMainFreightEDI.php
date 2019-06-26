<?php

namespace Lmap\StarTrackShipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Lmap\StarTrackShipping\Helper\ArrayToXML;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;



class OrderSuccessMainFreightEDI implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModel;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Lmap\StarTrackShipping\Helper\ArrayToXML
     */
    private $arraytoXML;

    protected $getSourceItemsBySku;

    /**
     * OrderSuccessMainFreightEDI constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Lmap\StarTrackShipping\Helper\ArrayToXML $arraytoXML
        //\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku
    )
    {
        $this->orderModel = $orderModel;
        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->orderSender = $orderSender;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->arraytoXML = $arraytoXML;
        //$this->getSourceItemsBySku = $getSourceItemsBySku;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $orderlast = $this->checkoutSession->getLastRealOrder();
        $orderlastId = $orderlast->getId();
        $orderlastOrigId=$orderlast->getRealOrderId();
        // OR
        $order = $observer->getEvent()->getOrder();
        $orderdata = $order ->getData();
        # This is the order id in our side. OrderId is internal magento orderid
        $orderIds = $observer->getEvent()->getOrderIds();
        $payment = $order->getPayment();
        $orderItems =$order->getAllItems();
        //$sku = $orderItems[0]['sku'];
        //$sourceItems = $this->getSourceItemsBySku->execute($orderItems[0]['sku']);
        //$source = $sourceItems->getSourceCode();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $addressInformation = $objectManager->create('Magento\Checkout\Api\Data\ShippingInformationInterface');
        //$addressInformation = $objectManager->create('Magento\Quote\Api\Data\AddressInterface');
        $extAttributes = $addressInformation->getExtensionAttributes();
        $suburb = $extAttributes->getSuburb();



        $transactionLastId = $payment->getLastTransId();
        // orderIncrementId is id sent to the customer
        $orderIncrementId = 'zenkai-'.$transactionLastId.'-'.$orderdata['increment_id'];
        $DebtorID = $order->getCustomerId(); // Person or Licensee custID
        $CustRef = $transactionLastId;
        # Customer and delivery detail
        $DebtorName = $order->getCustomerName();
        $DebtorAdd1 = $order->getShippingAddress()->getStreet();
        $DebtorAdd2 = $order->getShippingAddress()->getStreet();
        $DebtorSuburb = $order->getShippingAddress()->getSuburb();
        $DebtorPostCode = $order->getShippingAddress()->getPostcode();
        $DebtorCity = $order->getShippingAddress()->getCity();
        $DebtorState = $order->getShippingAddress()->getRegion();
        $DebtorCountry = $order->getShippingAddress()->getCountryId();
        $SpecialInstructions = "Special Instructions";
        $WhsID = '22';
        $OrderLineTotal = count($order->getAllItems());
        //$Carrier = $order->getShippingMethod();
        $Carrier = 'MAINFREIGHT';
        $CustomerID = '935744';

        // Preparing an associative array to be used for creating xml formatted order
        $ORDER_ARRAY = [
            "SOH"=>['OrderID'=>$orderIncrementId,'DebtorID'=>$DebtorID,'CustRef'=>$CustRef,'DebtorName'=>$DebtorName,
                'DebtorAdd1'=>$DebtorAdd1,'DebtorAdd2'=>$DebtorAdd2,'DebtorSuburb'=>$DebtorSuburb,
                'DebtorPostCode'=>$DebtorPostCode,'DebtorCity'=>$DebtorCity,'DebtorState'=>$DebtorState,
                'DebtorCountry'=>$DebtorCountry,'SpecialInstructions'=>$SpecialInstructions,'WhsID'=>$WhsID,
                'OrderLineTotal'=>$OrderLineTotal,'Carrier'=>$Carrier,'CustomerID'=>$CustomerID
            ],
            "SOL"=>[
                'Line'=>[]
            ]
        ];
        // Declare empty associative Line Array
        $Line = ['OrderID'=>[],'LineNo'=>[],'WhsStockCode'=>[],'WhsStockDesc'=>[],'Quantity'=>[],'CostPrice'=>[],'Weight'=>[],'Volume'=>[]];

        $lineNumber = 1;
        foreach ($orderItems as $key => $item)
        {
            $itemData = $item->getData();
            $Line['OrderID']=$orderIncrementId;
            $Line['LineNo']=$lineNumber;
            $Line['WhsStockCode']=$itemData['sku'];
            $Line['WhsStockDesc']=$itemData['name'];
            $Line['Quantity']=$itemData['qty_invoiced'];
            $Line['CostPrice']=$itemData['base_cost'];
            $Line['Weight']=$itemData['weight'];
            $Line['Volume']=[];
            $ORDER_ARRAY['SOL']['Line'][$lineNumber-1]=$Line;
            $lineNumber+=1;
        }

        $xmlOrder = $this->arraytoXML->toXML($ORDER_ARRAY,'order');
        $this->logger->debug("xml order " . var_export($xmlOrder,true));

        // Sending confirmation email to customer after successful order payment
        $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
        //$orderemail = $this->orderModel->create()->load($orderlastOrigId);
        $this->logger->debug("order " .$orderIds[0].' emailed');
        $this->orderSender->send($order, true);

        /*
        try {
            $this->checkoutSession->loadCustomerQuote();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Load customer quote error'));
        }
        */

    }
}