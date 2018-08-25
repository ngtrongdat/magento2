<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-08-07 22:10:30
 * @@Modify Date: 2015-10-06 17:06:33
 * @@Function:
 */
class Magiccart_Alothemes_Model_System_Config_Source_Col
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1,   'label'=>Mage::helper('adminhtml')->__('1 item per column')),
            array('value' => 2,   'label'=>Mage::helper('adminhtml')->__('2 items per column')),
            array('value' => 3,   'label'=>Mage::helper('adminhtml')->__('3 items per column')),
            array('value' => 4,   'label'=>Mage::helper('adminhtml')->__('4 items per column')),
            array('value' => 6,   'label'=>Mage::helper('adminhtml')->__('6 items per column'))
        );
    }
}
