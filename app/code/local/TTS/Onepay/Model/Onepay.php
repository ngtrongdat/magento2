<?php

class TTS_Onepay_Model_Onepay extends Mage_Payment_Model_Method_Abstract
{
   protected $_code  = 'onepay';
   protected $_formBlockType = 'onepay/form';
   protected $_infoBlockType = 'onepay/info';
 public function getTitle()
    {
        return $this->getConfigData('title');
    }
   public function get_icon()
    {
        return $this->getConfigData('icon');
    }
   public function get_ins()
    {
        return $this->getConfigData('vpc_instruction');
    }

  public function getOrderPlaceRedirectUrl()
		{			
				return Mage::getUrl('onepay/standard/redirect', array('_secure' => true));
		}
	public function getUrlOnepay($orderid){
		
		   $_order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
		   $_order->sendNewOrderEmail();
		   $getGrandTotal = $_order->getGrandTotal();
		   $getGrandTotalArr = explode(".", $getGrandTotal);
		   $getGrandTotalArr0 = $getGrandTotalArr[0];
		   $getGrandTotalArr1 = $getGrandTotalArr[1];
		   $getGrandTotalArr1 = substr ($getGrandTotalArr1 , 0 ,2 );
		   $amount_total = $getGrandTotalArr0.'.'.$getGrandTotalArr1;
		$arrayvalue =array();
					$arrayvalue['Title'] = "VPC 3-Party";
					$arrayvalue['AgainLink'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
					$arrayvalue['vpc_Merchant']=$this->getConfigData('vpc_Merchant');
					$arrayvalue['vpc_AccessCode']=$this->getConfigData('vpc_AccessCode');
					$arrayvalue['vpc_MerchTxnRef'] = date( 'YmdHis' ).rand();
					$arrayvalue['vpc_OrderInfo']= $orderid;
					$arrayvalue['vpc_Amount']= ($amount_total*100);
					$arrayvalue['vpc_ReturnURL']= Mage::getUrl('onepay/standard/success');
					$arrayvalue['vpc_Version']=$this->getConfigData('vpc_Version');
					$arrayvalue['vpc_Command']=$this->getConfigData('vpc_Command');
					$arrayvalue['vpc_Currency']=$this->getConfigData('vpc_Currency');
					$arrayvalue['vpc_Locale']=$this->getConfigData('vpc_Locale');
				    $arrayvalue['vpc_TicketNo']=$_SERVER ['REMOTE_ADDR'];
	
					//$arrayvalue['vpc_SHIP_Street01']= $_order->getShippingAddress()->getStreet1(); //
					//$arrayvalue['vpc_SHIP_Provice']=$_order->getShippingAddress()->getCity(); 
					//$arrayvalue['vpc_SHIP_City']=$_order->getShippingAddress()->getRegion(); 
					//$arrayvalue['vpc_SHIP_Country']=$_order->getShippingAddress()->getCountry_id(); 
					//$arrayvalue['vpc_Customer_Phone']=$_order->getShippingAddress()->getTelephone();//
					//$arrayvalue['vpc_Customer_Email']=$_order->getCustomerEmail();
				
				$SECURE_SECRET = $this->getConfigData('hash_code');
				
				$vpcURL = $this->getConfigData('virtualPaymentClientURL')."?";
				
				
				$stringHashData = "";
				
				ksort ($arrayvalue);
				
				
				$appendAmp = 0;
				
				foreach($arrayvalue as $key => $value) {
				
				  
				    if (strlen($value) > 0) {
				      
				        if ($appendAmp == 0) {
				            $vpcURL .= urlencode($key) . '=' . urlencode($value);
				            $appendAmp = 1;
				        } else {
				            $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
				        }
				     
				        if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
						    $stringHashData .= $key . "=" . $value . "&";
						}
				    }
				}
				
				$stringHashData = rtrim($stringHashData, "&");
				
				if (strlen($SECURE_SECRET) > 0) {
				
				    $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)));
				}
		return 	$vpcURL;		
	}	
	public function getResponseDescription($responseCode) {
	
		switch ($responseCode) {
			case "0" :
				//$result = "Giao dịch thành công - Approved";
				break;
			case "1" :
				$result = "Ngân hàng từ chối";
				break;
			case "3" :
				$result = "Merchant không tồn tại";
				break;
			case "4" :
				$result = "Mã truy cập không đúng";
				break;
			case "5" :
				$result = "Số tiền không hợp lệ";
				break;
			case "6" :
				$result = "Mã tiền tệ không hợp lệ";
				break;
			case "7" :
				$result = "Lỗi chưa xác định";
				break;
			case "8" :
				$result = "Số thẻ không đúng";
				break;
			case "9" :
				$result = "Tên trên thẻ không đúng";
				break;
			case "10" :
				$result = "Thẻ hết hạn";
				break;
			case "11" :
				$result = "Thẻ chưa được đăng ký dịch vụ ngân hàng trực tuyến";
				break;
			case "12" :
				$result = "Ngày trên thẻ không đúng";
				break;
			case "13" :
				$result = "Exist Amount";
				break;
			case "21" :
				$result = "Không đủ hạn mức";
				break;
			case "25" :
				$result = "Sai OTP";
				break;
			case "99" :
				$result = "Khách hàng hủy";
				break;
			default :
				//$result = "Failured";
		}
		return $result;
}
	public function transStatus($hashValidated,$txnResponseCode){
	$transStatus = "";
	if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
		$transStatus = "Giao dịch thành công";
	}elseif ($hashValidated=="CORRECT" && $txnResponseCode!="0"){
		$transStatus = "Giao dịch lỗi </br>".$this->getResponseDescription($txnResponseCode);
	}elseif ($hashValidated=="INVALID HASH"){
		$transStatus = "Giao dịch đang chờ";
	}
	return $transStatus;
	}
}