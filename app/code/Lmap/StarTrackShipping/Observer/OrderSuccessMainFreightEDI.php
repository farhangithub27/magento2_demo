<?php

namespace Lmap\StarTrackShipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Lmap\StarTrackShipping\Helper\ArrayToXML;


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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Lmap\StarTrackShipping\Helper\ArrayToXML
     */
    private $arraytoXML;

    /**
     * OrderSuccessMainFreightEDI constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Lmap\StarTrackShipping\Helper\ArrayToXML $arraytoXML
    )
    {
        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->arraytoXML = $arraytoXML;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $Quote = $this->checkoutSession->getQuote();
        $customerQoute = $this->checkoutSession->getCustomerQoute();
        //$order = $this->checkoutSession->getLastRealOrder();
        // OR
        $order = $observer->getEvent()->getOrder();
        # This is the order id in our side
        $OrderID = $observer->getEvent()->getOrderIds();
        $DebtorID = $order->getCustomerId(); // Person or Licensee custID
        $CustRef = // str(db_postage_consignment.transactionid)
        # Customer and delivery detail
        $DebtorName = $order->getCustomerName();
        $DebtorAdd1 = $order->getShippingAddress()->getStreet();
        $DebtorAdd2 = $order->getShippingAddress()->getStreet();
        $DebtorSuburb = $order->getShippingAddress()->getStreet();
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

        /*
        $orderstore  =  $order->getStore();
        $orderdata = $order ->getData();
        $shippingAddressId = $orderdata['shipping_address_id'];
        $orderGrandTotal=$order->getGrandTotal();
        $orderInvoiceCollection = $order->getInvoiceCollection();
        $orderShippingMethod =$order->getShippingMethod();
        $orderweight = $order->getWeight();
        $orderOrigData = $order->getOrigData();
        */
        // Preparing an associative array to be used for creating xml formatted order
        $ORDER = [
            "SOH"=>['OrderID'=>$OrderID[0],'DebtorID'=>$DebtorID,'CustRef'=>$CustRef,'DebtorName'=>$DebtorName,
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
        $orderItems =$order->getAllItems();
        $linenumber = 1;
        foreach ($orderItems as $key => $item)
        {
            $itemData = $item->getData();
            $Line['OrderID']=$itemData['order_id'];
            $Line['LineNo']=$linenumber;
            $Line['WhsStockCode']=$itemData['sku'];
            $Line['WhsStockDesc']=$itemData['name'];
            $Line['Quantity']=$itemData['qty_invoiced'];
            $Line['CostPrice']=$itemData['base_cost'];
            $Line['Weight']=$itemData['weight'];
            $Line['Volume']=[];
            $ORDER['SOL']['Line'][$linenumber-1]=$Line;
            $linenumber+=1;
        }

        $xmlOrder = $this->arraytoXML->toXML($ORDER,'order');
        $this->logger->debug("xml order " . var_export($xmlOrder,true));

        // Sending confirmation email to customer after successful order payment
        $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
        $this->orderSender->send($order, true);


        $this->logger->debug("ordersavebefore: ");
        // Debugger won't fireup if debug marker is on try line.
        try {
            $this->checkoutSession->loadCustomerQuote();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Load customer quote error'));
        }


    }
}