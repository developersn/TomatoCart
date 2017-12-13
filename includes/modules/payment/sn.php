<?php


class osC_Payment_sn extends osC_Payment {
	var $_title, $_code = 'sn', $_status = false, $_sort_order, $_order_id;
	function osC_Payment_sn() {
		global $osC_Database, $osC_Language, $osC_ShoppingCart;
		$this->_title = $osC_Language->get('پرداخت آنلاین');
		$this->_method_title = $osC_Language->get('پرداخت آنلاین');
		$this->_status = (MODULE_PAYMENT_sn_STATUS == '1') ? true : false;
		$this->_sort_order = MODULE_PAYMENT_sn_SORT_ORDER;
		$this->form_action_url = 'sn.php';
		if ($this->_status === true) {
			if ((int) MODULE_PAYMENT_sn_ORDER_STATUS_ID > 0) {
				$this->order_status = MODULE_PAYMENT_sn_ORDER_STATUS_ID;
			}
			if ((int) MODULE_PAYMENT_sn_ZONE > 0) {
				$check_flag = false;
				$Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
				$Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
				$Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_sn_ZONE);
				$Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getBillingAddress('country_id'));
				$Qcheck->execute();
				while ($Qcheck->next()) {
					if ($Qcheck->valueInt('zone_id') < 1) {
						$check_flag = true;
						break;
					}
					elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getBillingAddress('zone_id')) {
						$check_flag = true;
						break;
					}
				}
				if ($check_flag === false) {
					$this->_status = false;
				}
			}
		}
	}
	function selection() {
		return array('id' => $this->_code, 'module' => $this->_method_title);
	}
	function pre_confirmation_check() {
		return false;
	}
	function confirmation() {
		global $osC_Language, $osC_CreditCard;
		$this->_order_id = osC_Order :: insert(ORDERS_STATUS_PREPARING);
		$confirmation = array('title' => $this->_method_title, 'fields' => array(array('title' => $osC_Language->get('پرداخت با کلیه کارت های عضو شتاب'))));
		return $confirmation;
	}
	function process_button() {
// Security
@session_start();
$sec = uniqid();
$md = md5($sec.'vm');
// Security
		global $osC_Currencies, $osC_ShoppingCart, $osC_Language, $osC_Database;
		$currency = MODULE_PAYMENT_sn_CURRENCY;
		$amount = round($osC_Currencies->formatRaw($osC_ShoppingCart->getTotal(), $currency), 2);
		$order = $this->_order_id;
		$orderId = $order;
		$callbackUrl = osc_href_link(FILENAME_CHECKOUT, 'process&order='.$orderId.'&md='.$md.'&sec='.$sec.'', 'SSL', null, null, true);
		$user = new osC_Customer();
	
	
	if(MODULE_PAYMENT_sn_webservice==1){
$data_string = json_encode(array(
'pin'=> MODULE_PAYMENT_sn_PIN,
'price'=> ceil(($amount/10)),
'callback'=> $callbackUrl ,
'order_id'=> $orderId,
'ip'=> $_SERVER['REMOTE_ADDR'],
'email'=>$user->_data['email_address'],
'name'=>$user->_data['first_name'].' '.$user->_data['last_name'],
'callback_type'=>2
));
}else
{$data_string = json_encode(array(
'pin'=> MODULE_PAYMENT_sn_PIN,
'price'=> ceil(($amount/10)),
'callback'=> $callbackUrl ,
'order_id'=> $orderId,
'ip'=> $_SERVER['REMOTE_ADDR'],
'callback_type'=>2
));}
	
$ch = curl_init('https://developerapi.net/api/v1/request');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Content-Length: ' . strlen($data_string))
);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
$result = curl_exec($ch);
curl_close($ch);

		

$json = json_decode($result,true);


						 $res=$json['result'];
                 
	                 switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;

						     case -9:
						    $msg = "خطای ناشناخته";
						    break;

						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;

						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;


						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						    
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						    
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						    
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						    
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						    
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						    
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						    
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						    
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						 
						    
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						    
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						    
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
						    
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;

						    default:
						       $msg = $json['msg'];
						}







if(!empty($json['result']) AND $json['result'] == 1)
{
// Set Session
$_SESSION[$sec] = [
	'price'=>$amount ,
	'order_id'=>$invoice_id ,
	'au'=>$json['au'] ,
];
	  $au = $json['au'];
		$osC_Database->simpleQuery("insert into `" . DB_TABLE_PREFIX . "online_transactions`
		(orders_id,receipt_id,transaction_method,transaction_date,transaction_amount,transaction_id) values
		                    ('$orderId','$au','sn','','$amount','')
				  ");
					//
		 $process_button_string = osc_draw_hidden_field('MID', MODULE_PAYMENT_sn_PIN).
		  osc_draw_hidden_field('form', $json['form']).
                               osc_draw_hidden_field('OrderId', $orderId).
                               osc_draw_hidden_field('CallBack', $callbackUrl).
                               osc_draw_hidden_field('Amount', $amount/10);

      return $process_button_string;
      
	    ;}else{
	      osC_Order :: remove($this->_order_id);
	    echo '<div style="font-size:11px; color:#cc0000; width:500; border:1px solid #cc0000; padding:5px; background:#ffffcc;">' ."خطایی در اتصال رخ داده است". " . " . $msg . '</div><div style="display:none">';
	    
;}
	}
	function get_error() {
		global $osC_Language;
		return $error;
	}
	function process() {
	      global   $osC_Database,$osC_Customer, $osC_Currencies, $osC_ShoppingCart, $_POST, $_GET, $osC_Language, $messageStack;
$order_id=$_GET['order_id'];
	  $find_ord_id = $osC_Database->query('select Receipt_id from :table_online_transactions where orders_id = :tbl_ord_id');
          $find_ord_id->bindTable(':table_online_transactions', DB_TABLE_PREFIX . "online_transactions");
          $find_ord_id->bindValue(':tbl_ord_id', $order_id);
          $find_ord_id->execute();
		  $rcp_id=$find_ord_id->value('Receipt_id') ;
// Security
$sec=$_GET['sec'];
$mdback = md5($sec.'vm');
$mdurl=$_GET['md'];
// Security
if(!empty($_GET['sec']) or !empty($_GET['md']))
{
 $amount = round($osC_Currencies->formatRaw($osC_ShoppingCart->getTotal(), $currency), 2);
if($mdback == $mdurl)
	{
$bank_return = $_POST + $_GET ;
$data_string = json_encode(array (
'pin' => MODULE_PAYMENT_sn_PIN,
'price' => ceil(($amount/10)),
'order_id' => $order_id,
'au' => $rcp_id,
'bank_return' =>$bank_return,
));

$ch = curl_init('https://developerapi.net/api/v1/verify');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Content-Length: ' . strlen($data_string))
);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
$result = curl_exec($ch);
curl_close($ch);
$json = json_decode($result,true);

$res=$json['result'];
       switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;

						     case -9:
						    $msg = "خطای ناشناخته";
						    break;

						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;

						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;


						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						    
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						    
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						    
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						    
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						    
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						    
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						    
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						    
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						 
						    
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						    
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						    
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
						    
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;

						    default:
						       $msg = $json['msg'];
						}




                    if($json['result'] == 1)
					{
$osC_Database->simpleQuery("update `" . DB_TABLE_PREFIX . "online_transactions` set transaction_id = '".$rcp_id."',transaction_date = '" . date("YmdHis") . "' where 1 and ( orders_id = '".$order_id."' )");
					//
						$Qtransaction = $osC_Database->query('insert into :table_orders_transactions_history (orders_id, transaction_code, transaction_return_value, transaction_return_status, date_added) values (:orders_id, :transaction_code, :transaction_return_value, :transaction_return_status, now())');
						$Qtransaction->bindTable(':table_orders_transactions_history', TABLE_ORDERS_TRANSACTIONS_HISTORY);
						$Qtransaction->bindInt(':orders_id', $order_id);
						$Qtransaction->bindInt(':transaction_code', 1);
						$Qtransaction->bindValue(':transaction_return_value', $rcp_id);
						$Qtransaction->bindInt(':transaction_return_status', 1);
						$Qtransaction->execute();
						//
						$this->_order_id = osC_Order :: insert();
						$comments = $osC_Language->get('payment_sn_method_authority') . '[' . $rcp_id . ']';
						osC_Order :: process($this->_order_id, $this->order_status, $comments);
	;}else{
		
		osC_Order :: remove($this->_order_id);
	
        $messageStack->add_session('checkout', $msg, 'error');
		
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout&view=paymentInformationForm', 'SSL', null, null, true));		
		
		;}
;}else{
		
		osC_Order :: remove($this->_order_id);
	
        $messageStack->add_session('checkout', "خطای امنیتی رخ داده است", 'error');
		
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout&view=paymentInformationForm', 'SSL', null, null, true));		
		
		;}
		;}else{
		
		osC_Order :: remove($this->_order_id);
	
        $messageStack->add_session('checkout', "مشکلی در پرداخت رخ داده است", 'error');
		
        osc_redirect(osc_href_link(FILENAME_CHECKOUT, 'checkout&view=paymentInformationForm', 'SSL', null, null, true));		
		
		;}
		;}
}

?>