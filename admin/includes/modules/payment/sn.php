ï»¿<?php

  class osC_Payment_sn extends osC_Payment_Admin {

 var $_title;
 var $_code = 'sn';
 var $_author_name = 'sn';
 var $_author_www = 'http://';
 var $_status = false;




    function osC_Payment_sn() {
      global $osC_Language;

      $this->_title = $osC_Language->get('Ù¾Ø±Ø¯Ø§Ø®Øª Ø¢Ù†Ù„Ø§ÛŒÙ†');
      $this->_description = $osC_Language->get('پرداخت با کلیه کارت های عضو شتاب');
      $this->_method_title = $osC_Language->get('پرداخت آنلاین');
      $this->_status = (defined('MODULE_PAYMENT_sn_STATUS') && (MODULE_PAYMENT_sn_STATUS == '1') ? true : false);
      $this->_sort_order = (defined('MODULE_PAYMENT_sn_SORT_ORDER') ? MODULE_PAYMENT_sn_SORT_ORDER : null);
    }



    function isInstalled() {
      return (bool)defined('MODULE_PAYMENT_sn_STATUS');
    }



    function install() {
      global $osC_Database;

      parent::install();
        //
    $osC_Database->simpleQuery("CREATE TABLE IF NOT EXISTS `" . DB_TABLE_PREFIX . "online_transactions` (
    `id` int(10) unsigned NOT NULL auto_increment, 
    `orders_id` int(11) default NULL, 
    `receipt_id` varchar(100) default NULL, 
    `transaction_method` varchar(255) default NULL, 
    `transaction_date` datetime default NULL, 
    `transaction_amount` decimal(15,2) unsigned default NULL,  
    `transaction_id` varchar(255) default NULL,
    PRIMARY KEY (`id`)
    )ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    //

     $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('ÙØ¹Ø§Ù„Ø³Ø§Ø²ÛŒ Ù¾Ø±Ø¯Ø§Ø®Øª Ø§ÛŒÙ†ØªØ±Ù†ØªÛŒ sn', 'MODULE_PAYMENT_sn_STATUS', '-1', 'Ù¾Ø±Ø¯Ø§Ø®Øª Ø§ÛŒÙ†ØªØ±Ù†ØªÛŒ Ø§Ø² Ø·Ø±ÛŒÙ‚ Ø¯Ø±ÙˆØ§Ø²Ù‡ Ø¯Ø±Ú¯Ø§Ù‡ Ù¾Ø±Ø¯Ø§Ø®Øª Ø¢Ù†Ù„Ø§ÛŒÙ†  ÙØ¹Ø§Ù„ Ú¯Ø±Ø¯Ø¯ØŸ', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
     $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('ÙˆØ¨ Ø³Ø±ÙˆÛŒØ³', 'MODULE_PAYMENT_sn_webservice', '1', 'Ø¯Ø± ØµÙˆØ±Øª ÙØ¹Ø§Ù„ Ø¨ÙˆØ¯Ù† ØŒ Ø§Ø·Ù„Ø§Ø¹Ø§Øª Ø®Ø±ÛŒØ¯Ø§Ø± Ø¯Ø± Ù¾Ù†Ù„ Ú©Ø§Ø±Ø¨Ø±ÛŒ Ø«Ø¨Øª Ø®ÙˆØ§Ù‡Ø¯ Ø´Ø¯', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API ÙØ±ÙˆØ´Ù†Ø¯Ù‡', 'MODULE_PAYMENT_sn_PIN', '', 'API ÙØ±ÙˆØ´Ù†Ø¯Ù‡ Ø§ÛŒÙ†ØªØ±Ù†ØªÛŒ', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('ÙˆØ§Ø­Ø¯ Ù¾ÙˆÙ„ Ø¯Ø±ÙˆØ§Ø²Ù‡ Ù¾Ø±Ø¯Ø§Ø®Øª', 'MODULE_PAYMENT_sn_CURRENCY', 'IRR', 'ÙˆØ§Ø­Ø¯ Ù¾ÙˆÙ„ Ø¯Ø±ÙˆØ§Ø²Ù‡ Ù¾Ø±Ø¯Ø§Ø®Øª(Ø¨Ø± Ø±ÙˆÛŒ Ø±ÛŒØ§Ù„ ØªÙ†Ø¸ÛŒÙ… Ú¯Ø±Ø¯Ø¯)', '6', '0', 'osc_cfg_set_boolean_value(array(\'Selected Currency\',\'IRR\'))', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ØªØ±ØªÛŒØ¨ Ù†Ù…Ø§ÛŒØ´', 'MODULE_PAYMENT_sn_SORT_ORDER', '0', 'ØªØ±ØªÛŒØ¨ Ù†Ù…Ø§ÛŒØ´ ØµÙØ­Ù‡ Ù¾Ø±Ø¯Ø§Ø®Øª ØŒ Ù…Ù‚Ø§Ø¯ÛŒØ± Ú©Ù…ØªØ± Ø¨Ø§Ù„Ø§ØªØ± Ù‚Ø±Ø§Ø± Ù…ÛŒ Ú¯ÛŒØ±Ù†Ø¯.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Ù…Ù†Ø·Ù‚Ù‡ Ù¾Ø±Ø¯Ø§Ø®Øª', 'MODULE_PAYMENT_sn_ZONE', '0', 'Ø§Ú¯Ø± Ù…Ù†Ø·Ù‚Ù‡ Ø§Ù†ØªØ®Ø§Ø¨ Ú¯Ø±Ø¯Ø¯ ØŒ Ø§ÛŒÙ† Ø±ÙˆØ´ Ù¾Ø±Ø¯Ø§Ø®Øª ÙÙ‚Ø· Ø¨Ø±Ø§ÛŒ Ø¢Ù† Ù…Ù†Ø·Ù‚Ù‡ ÙØ¹Ø§Ù„ Ù…ÛŒ Ø¨Ø§Ø´Ø¯.', '6', '0', 'osc_cfg_use_get_zone_class_title', 'osc_cfg_set_zone_classes_pull_down_menu', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('ØªÙ†Ø¸ÛŒÙ… ÙˆØ¶Ø¹ÛŒØª Ø³ÙØ§Ø±Ø´', 'MODULE_PAYMENT_sn_ORDER_STATUS_ID', '0', 'ÙˆØ¶Ø¹ÛŒØª Ø³ÙØ§Ø±Ø´Ø§ØªÛŒ Ú©Ù‡ Ø§Ø² Ø§ÛŒÙ† Ø·Ø±ÛŒÙ‚ Ù¾Ø±Ø¯Ø§Ø®Øª Ù…ÛŒ Ú¯Ø±Ø¯Ù†Ø¯.', '6', '0', 'osc_cfg_set_order_statuses_pull_down_menu', 'osc_cfg_use_get_order_status_title', now())");
    }



    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_PAYMENT_sn_STATUS',
                             'MODULE_PAYMENT_sn_PIN',
                             'MODULE_PAYMENT_sn_webservice',
                             'MODULE_PAYMENT_sn_CURRENCY',
                             'MODULE_PAYMENT_sn_ZONE',
                             'MODULE_PAYMENT_sn_ORDER_STATUS_ID',
                             'MODULE_PAYMENT_sn_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>