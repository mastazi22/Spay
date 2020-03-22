<?php
    include ("../includes/configure.php");
    include ('../includes/session_handler.php');
    include('configuration.php');
    $CRED = json_decode(CRED);

    if(!isset($_SESSION['ORDER_DETAILS']['order_id']) || empty($_SESSION['ORDER_DETAILS']['order_id']) )
    {
    	header("Location: https://shurjopay.com/halt.php");
        exit();
    } 
	
	$paymentID = $_GET['paymentID'];
	// save payment id
	$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET remarks ='".$paymentID."' WHERE order_id='".$order_id."'");			
	//$url=curl_init('https://checkout.sandbox.bka.sh/v1.0.0-beta/checkout/payment/execute/'.$paymentID);
	$url=curl_init($CRED->checkout_link.'/checkout/payment/execute/'.$paymentID);
	$header=array(
		'Content-Type:application/json',
		'authorization:'.$_SESSION['token'],		
		'x-app-key:'.$CRED->body->app_key);
	curl_setopt($url,CURLOPT_HTTPHEADER, $header);
	curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($url,CURLOPT_SSL_VERIFYPEER, false);
	$resultdatax=curl_exec($url);
	$_SESSION['executeResponse'] = $resultdatax;
	curl_close($url);		
	echo $resultdatax;  
?>