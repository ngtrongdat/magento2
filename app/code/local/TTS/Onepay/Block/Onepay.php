<?php
class TTS_Onepay_Block_Onepay extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOnepay()     
     { 
        if (!$this->hasData('onepay')) {
            $this->setData('onepay', Mage::registry('onepay'));
        }
        return $this->getData('onepay');
        
    }
}