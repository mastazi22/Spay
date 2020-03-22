<?php
	error_reporting(0);
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
	// Go for verification call
	$VerifyResponse = $tokenReq->verify($orderID);
	$ResponseArrayObject = json_decode($VerifyResponse);
	$getRefId = $ResponseArrayObject->data->order->tnx_reference_number;
	$getOrderStatusCode = '001';//$ResponseArrayObject->data->order->status_code;
	$gwReturnStatusTxt = $ResponseArrayObject->data->order->status;	
	// Update database
	$_SESSION['order_details_response']['txID']			= $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] 	= '';
	$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
	$_SESSION['order_details_response']['txnAmount'] 	= $_SESSION['ORDER_DETAILS']['txnAmount'];
	$_SESSION['order_details_response']['spCode'] 		= '001';
	$_SESSION['order_details_response']['spCodeDes'] 	= 'Bank Transaction Failed.';
	$msg = '';
	try
	{
		if (!mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$getOrderStatusCode."', gw_return_msg='".$gwReturnStatusTxt."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$getOrderStatusCode."', bank_status='FAIL', bank_response='".$VerifyResponse. ". Message: " .$msg."' WHERE order_id='".$orderID."'"))
		{
			echo("Error description: " . mysqli_error($GLOBALS["___mysqli_sm"]));
		}	
		if (!mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$gwReturnStatusTxt."', epay_status_text='FAILED' WHERE tc_txid='".$orderID."'"))
		{
			echo("Error description: " . mysqli_error($GLOBALS["___mysqli_sm"]));
		}	
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
	// Redirect to merchant
	header("Location: ".$db->local_return_url);	
	exit;
?>