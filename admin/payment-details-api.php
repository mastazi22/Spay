<?php
	session_start();
	include("../includes/configure.php");
	require_once 'database.php';
	if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin']) {    
	 	header('Location: login.php');
	}

	require_once('lib/ORM.php');
	require_once('lib/PaymentStatus.php');

	$db = new ORM($GLOBALS["___mysqli_sm"]);
	$status = new PaymentStatus();
	if(isset($_POST['order_id']) && !empty($_POST['order_id']))
	{
		$merchant_id = $_POST['uid'];
		$txid 	  = $_POST['txid'];
		$order_id = $_POST['order_id'];
		$gateway = !empty($_POST['gateway'])?$_POST['gateway']:$_POST['method'];
		
		// get merchant credentials
		$CRED = $db->getMerchantCredentials($merchant_id);	
		// get payment status
		if($_POST['gateway'] == 'mx') { 
			$city_data = $db->getCityBankSessionId($_POST['order_id']);
			$city_session_id = $city_data->SessionID;
			$city_order_id = $city_data->OrderID;
		}
		$result  = $status->getPaymentStatus($_POST['order_id'], $gateway, $_POST['bank_tx_id'], $city_session_id, $city_order_id, $CRED);

	 	echo json_encode($result);	
	}

	

?>