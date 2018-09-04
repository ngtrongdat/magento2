<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('onepay')};
CREATE TABLE {$this->getTable('onepay')} (
  `vpc_OrderInfo` int(11) unsigned NOT NULL,
  `vpc_TxnResponseCode` varchar(255) NOT NULL default '',
  `vpc_SecureHash` varchar(255) NOT NULL default '',
  `vpc_TransactionNo` varchar(255) NOT NULL default '',
  `vpc_Message` varchar(255) NOT NULL default '',
  `vpc_Merchant` varchar(255) NOT NULL default '',
  `vpc_MerchTxnRef` varchar(255) NOT NULL default '',
  PRIMARY KEY (`vpc_OrderInfo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 