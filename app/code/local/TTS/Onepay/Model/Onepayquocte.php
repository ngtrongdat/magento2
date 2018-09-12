<?php

class TTS_Onepay_Model_Onepayquocte extends Mage_Payment_Model_Method_Abstract
{
   protected $_code  = 'onepayquocte';
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
				return Mage::getUrl('onepay/standard/redirect', array('_secure' => true,'onepay'=> '1'));
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
					$arrayvalue['vpc_ReturnURL']=Mage::getUrl('onepay/standard/successquocte');
					$arrayvalue['vpc_Version']=$this->getConfigData('vpc_Version');
					$arrayvalue['vpc_Command']=$this->getConfigData('vpc_Command');
					//$arrayvalue['vpc_Currency']=$this->getConfigData('vpc_Currency');
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
            $result = "Giao dịch thành công";
            break;
        case "?" :
            $result = "Không xác định được trạng thái giao dịch";
            break;
        case "1" :
            $result = "Hệ thống của ngân hàng từ chối";
            break;
        case "2" :
            $result = "Ngân hàng từ chối giao dịch";
            break;
        case "3" :
            $result = "Ngân hàng không phản hồi";
            break;
        case "4" :
            $result = "Thẻ hết hạn";
            break;
        case "5" :
            $result = "Không đủ hạn mức để thanh toán";
            break;
        case "6" :
            $result = "Không kết nối được với ngân hàng";
            break;
        case "7" :
            $result = "Hệ thống thanh toán lỗi";
            break;
        case "8" :
            $result = "Không hỗ trợ kiểu giao dịch này";
            break;
        case "9" :
            $result = "Ngân hàng từ chối giao dịch (Không liên hẹe ngân hàng)";
            break;
		case "99" :
            $result = "Khách hàng hủy";
            break;
        case "A" :
            $result = "Giao dịch bị ngưng";
            break;
        case "C" :
            $result = "Đã hủy giao dịch";
            break;
        case "D" :
            $result = "Giao dịch đã nhận và đang được xử lý";
            break;
        case "F" :
            $result = "Xác thực 3D Secure lỗi";
            break;
		case "B" :
            $result = "Xác thực 3D Secure lỗi";
            break;
        case "I" :
            $result = "Xác thực mã bảo mật của thẻ lỗi";
            break;
        case "L" :
            $result = "Giao dịch đang bị khóa";
            break;
        case "N" :
            $result = "Chủ thẻ chưa bật xác thực thẻ";
            break;
        case "P" :
            $result = "Giao dịch đã nhận và đang được xử lý";
            break;
        case "R" :
            $result = "Giao dịch chưa được xử lý - vượt quá giới hạn hoặc số lần thử";
            break;
        case "S" :
            $result = "Lặp SessionID";
            break;
        case "T" :
            $result = "Xác thực địa chỉ lỗi";
            break;
        case "U" :
            $result = "Mã bảo mật lỗi";
            break;
        case "V" :
            $result = "Xác thực địa chỉ và mã bảo mật lỗi";
            break;
		case "Z" :
            $result = "Giao dịch bị chặn bởi OnePAY ODF";
            break;
        default  :
            //$result = "Unable to be determined";
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