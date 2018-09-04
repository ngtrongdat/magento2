<?php

class TTS_Onepay_Model_Mysql4_Onepay_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onepay/onepay');
    }
}