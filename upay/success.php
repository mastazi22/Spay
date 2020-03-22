<?php
	include ('../includes/session_handler.php');
	include ("../includes/configure.php");	
	include("token.php");
	
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone('Asia/Dhaka'));

	$tokenReq = new TokenRequest();
	// Parse Url
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);	
	
	$orderID = $query['merchant_order_number'];
	$BankTxRefId = $query['tnx_reference_number'];

	$_SESSION['order_details_response']['bankTxID'] = $BankTxRefId;
	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
	// Go for verification call
	$VerifyResponse = $tokenReq->verify($orderID);	
	$ResponseArrayObject = json_decode($VerifyResponse);	
	
	$getRefId = $ResponseArrayObject->data->order->tnx_reference_number;
	$getOrderStatusCode = '000';//$ResponseArrayObject->data->order->status_code;
	$gwReturnStatusTxt = $ResponseArrayObject->data->order->status;
	
	if( $gwReturnStatusTxt == 'paid')
	{
		$_SESSION['order_details_response']['spCode'] = "000";
		$_SESSION['order_details_response']['spCodeDes'] = "Successful";
		$_SESSION['order_details_response']['bankTxStatus'] = $bankTxStatus = "SUCCESS";
		
	}
	else 
	{
		$_SESSION['order_details_response']['spCode'] = "001";
		$_SESSION['order_details_response']['spCodeDes'] = "Verification Failed";
		$_SESSION['order_details_response']['bankTxStatus'] = $bankTxStatus = "FAILED";		
	}

	

	// Update database		
	

	mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$getOrderStatusCode."', gw_return_msg='".ucfirst($gwReturnStatusTxt)."', bank_tx_id = '".$getRefId."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$getOrderStatusCode."', bank_status='".$bankTxStatus."', bank_response='".$VerifyResponse."' WHERE order_id='".$orderID."'");
	mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$gwReturnStatusTxt."', epay_status_text='".$gwReturnStatusTxt."' WHERE tc_txid='".$orderID."'");
	
	// Redirect to merchant

	header("Location: ".$db->local_return_url);	
	exit;

?>