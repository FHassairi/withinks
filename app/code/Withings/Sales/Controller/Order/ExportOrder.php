<?php

namespace Withings\Sales\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Sales\Model\Order;

class ExportOrder extends Action 
{
    
    protected $context;
    protected $orderModel
    /**
     * @param Context $context
     * @param Order $orderModel
     */

    public function __construct(
       Context $context,
       Order $orderModel, 
      
) {
    
    $this->orderModel = $orderModel;
}

    /**
     * Customer order Export
     */
    public function getOrderData()
    {
    	$orderExportData=[];

	$orders = $this->orderModel->getCollection();

    foreach($orders as $orderData){
    	$orderExportData['customerEmail'] = $orderData->getCustomerEmail();
    	$orderExportData['custmerFirstName'] = $orderData->getCustomerFirstname();
    	$orderExportData['customerLastName'] = $orderData->getCustomerLastname();
        $orderExportData['shippingMethod'] = $orderData->getShippingMethod();
       
        $orderExportData['shippingAddress']['street'] = $orderData->getShippingAddress()->getStreet();
        $orderExportData['shippingAddress']['postcode'] = $orderData->getShippingAddress()->getPostCode();
        $orderExportData['shippingAddress']['city'] = $orderData->getShippingAddress()->getCity();
        $orderExportData['shippingAddress']['country'] = $orderData->getShippingAddress()->getCountry();
        $orderExportData['shippingAddress']['telephone'] = $orderData->getShippingAddress()->getTelephone();
        $orderExportData['total'] = $orderData->getTotal();
        $orderExportData['currency'] = $orderData->getCurrency();
        $items = $orderData->getItems();
        foreach ($items as $item) {
        	 $orderExportData['itemName'.$item->getId()] = $item->getName();
        	 $orderExportData['itemSkus'.$item->getId()] = $item->getSku();
        	 $orderExportData['itemUnitPrice'.$item->getId()] = $item->getUnitPrice();

        }
     }
     return $orderExportData;
   
    }

    public function execute(){
    	$orderData = $this->getOrderData();
    	$ch = curl_init("/pub/apiRest");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($orderData))));

        $result = curl_exec($ch);

        $identifiant = json_decode($result, 1);
    }
}