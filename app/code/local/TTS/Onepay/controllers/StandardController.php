<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Paypal Standard Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TTS_Onepay_StandardController extends Mage_Core_Controller_Front_Action
{
  
    public function redirectAction()
    {
    	
    	$session = Mage::getSingleton('checkout/session');
    	if( $this->getRequest()->getParam('onepay')!=1){
        $url = Mage::getModel('onepay/onepay')->getUrlOnepay($session->getLastRealOrderId());
    	}else{
    	 $url = Mage::getModel('onepay/onepayquocte')->getUrlOnepay($session->getLastRealOrderId());	
    	}
		
        $this->_redirectUrl($url);
    }

    /**
     * When a customer cancel payment from paypal.
     */
 
    public function  successAction()
    {		
    $SECURE_SECRET = Mage::getStoreConfig('payment/onepay/hash_code',Mage::app()->getStore());
    $vpc_Txn_Secure_Hash = $_GET ["vpc_SecureHash"];
    unset ( $_GET ["vpc_SecureHash"] );

$errorExists = false;

if (strlen ( $SECURE_SECRET ) > 0 && $_GET ["vpc_TxnResponseCode"] != "7" && $_GET ["vpc_TxnResponseCode"] != "No Value Returned") {

    $stringHashData = "";

	foreach ( $_GET as $key => $value ) {

        if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
		    $stringHashData .= $key . "=" . $value . "&";
		}
	}

    $stringHashData = rtrim($stringHashData, "&");	
	

	if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)))) {
	
		$hashValidated = "CORRECT";
	} else {

		$hashValidated = "INVALID HASH";
	}
} else {

	$hashValidated = "INVALID HASH";
}

    	$data['vpc_OrderInfo']= $this->null2unknown ( $_GET ["vpc_OrderInfo"] );
    	$data['vpc_SecureHash'] = $hashValidated;
    	$data['vpc_TxnResponseCode'] = $this->null2unknown($_GET ["vpc_TxnResponseCode"]);
    	$data['vpc_TransactionNo'] = $this->null2unknown($_GET ["vpc_TransactionNo"]);
    	$data['vpc_Merchant'] = $this->null2unknown($_GET ["vpc_Merchant"]);
    	$data['vpc_Message'] = $this->null2unknown($_GET ["vpc_Message"]);
    	$data['vpc_MerchTxnRef'] = $this->null2unknown($_GET ["vpc_MerchTxnRef"]);
     	$model = Mage::getModel('onepay/success')	;
		$model->setData($data);
			
		try {
		$model->save();
		} catch (Exception $e) {}
        Mage::getSingleton('checkout/session')->addSuccess( Mage::getModel('onepay/onepay')->transStatus($hashValidated,$data['vpc_TxnResponseCode']));
		////////////////////// UPDATE ORDER STATUS ////////////////////////////
		if ($hashValidated == "CORRECT" && $data['vpc_TxnResponseCode'] == "0") {
			//$transStatus = "Giao dịch thành công";	
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'processing' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'processing' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			$order = Mage::getModel('sales/order')->loadByIncrementId($data['vpc_OrderInfo']);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order->save();
		} 
		elseif ($hashValidated == "CORRECT" && $data['vpc_TxnResponseCode'] != "0") {
			//$transStatus = "Giao dịch thất bại";
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'payment_fail' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'payment_fail' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			$order = Mage::getModel('sales/order')->loadByIncrementId($data['vpc_OrderInfo']);			
			//$order->setState("payment_fail", true);
			$order->setStatus("payment_fail"); 
			$order->save();
		} elseif ($hashValidated == "INVALID HASH") {
			//$transStatus = "Giao dịch Pendding";
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'pending' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'pending' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
		}
		/////////////////////////////////////////////////
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
   public function  successquocteAction()
    {		
    $SECURE_SECRET = Mage::getStoreConfig('payment/onepayquocte/hash_code',Mage::app()->getStore());
    $vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
	$vpc_MerchTxnRef = $_GET["vpc_MerchTxnRef"];
	$vpc_AcqResponseCode = $_GET["vpc_AcqResponseCode"];
	unset($_GET["vpc_SecureHash"]);
    $errorExists = false;

if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {

    ksort($_GET);
  
    $md5HashData = "";
  
    foreach ($_GET as $key => $value) {

        if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
		    $md5HashData .= $key . "=" . $value . "&";
		}
    }

    $md5HashData = rtrim($md5HashData, "&");


	if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)))) {
    
        $hashValidated = "CORRECT";
    } else {
       
        $hashValidated = "INVALID HASH";
    }
} else {
    
    $hashValidated = "INVALID HASH";
}

    	$data['vpc_OrderInfo']= $this->null2unknown ( $_GET ["vpc_OrderInfo"] );
    	$data['vpc_SecureHash'] = $hashValidated;
    	$data['vpc_TxnResponseCode'] = $this->null2unknown($_GET ["vpc_TxnResponseCode"]);
    	$data['vpc_TransactionNo'] = $this->null2unknown($_GET ["vpc_TransactionNo"]);
    	$data['vpc_Merchant'] = $this->null2unknown($_GET ["vpc_Merchant"]);
    	$data['vpc_Message'] = $this->null2unknown($_GET ["vpc_Message"]);
    	$data['vpc_MerchTxnRef'] = $this->null2unknown($_GET ["vpc_MerchTxnRef"]);
     	$model = Mage::getModel('onepay/success')	;
		$model->setData($data);
			
		try {
		$model->save();
		} catch (Exception $e) {}
        Mage::getSingleton('checkout/session')->addSuccess( Mage::getModel('onepay/onepayquocte')->transStatus($hashValidated,$data['vpc_TxnResponseCode']));
		////////////////////// UPDATE ORDER STATUS ////////////////////////////
		if ($hashValidated == "CORRECT" && $data['vpc_TxnResponseCode'] == "0") {
			//$transStatus = "Giao dịch thành công";	
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'complete' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'complete' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			$order = Mage::getModel('sales/order')->loadByIncrementId($data['vpc_OrderInfo']);			
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order->save();
		} 
		elseif ($hashValidated == "CORRECT" && $data['vpc_TxnResponseCode'] != "0") {
			//$transStatus = "Giao dịch thất bại";
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'processing' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'processing' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			$order = Mage::getModel('sales/order')->loadByIncrementId($data['vpc_OrderInfo']);			
			//$order->setState("payment_fail", true);
			$order->setStatus("payment_fail"); 
			$order->save();
		} elseif ($hashValidated == "INVALID HASH") {
			//$transStatus = "Giao dịch Pendding";
			// fetch write database connection that is used in Mage_Core module
			//$write = Mage::getSingleton('core/resource')->getConnection('core_write');		
			// now $write is an instance of Zend_Db_Adapter_Abstract
			//$write->query("UPDATE `sales_flat_order` SET `status` = 'pending' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
			//$write->query("UPDATE `sales_flat_order_grid` SET `status` = 'pending' WHERE `increment_id` =".$data['vpc_OrderInfo'] );
		}
		/////////////////////////////////////////////////
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
public function null2unknown($data) {
	if ($data == "") {
		return "No Value Returned";
	} else {
		return $data;
	}
}
}
