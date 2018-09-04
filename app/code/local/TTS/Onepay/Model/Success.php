<?php

class TTS_Onepay_Model_Success extends Mage_Core_Model_Abstract
{
 public function _construct()
    {
        parent::_construct();
        $this->_init('onepay/onepay');
		
		
    }
 public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('vpc_OrderInfo', $incrementId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return Mage_Sales_Model_Order
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }
}