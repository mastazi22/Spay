<?php
    include ("../includes/configure.php");
    include ('../includes/session_handler.php');
    include('configuration.php');

    if(!isset($_SESSION['ORDER_DETAILS']['order_id']) || empty($_SESSION['ORDER_DETAILS']['order_id']) )
    {
    	header("Location: https://shurjopay.com/halt.php");
        exit();
    } 
    
    $CRED = json_decode(CRED);
    

	$amount  = $_GET['amount'];
	$order_id  = $_SESSION['ORDER_DETAILS']['order_id'];
    $intent   = "sale";


	   $createpaybody=array(
	       'amount'=>$amount,
		   'currency'=>'BDT',
		   'intent'=>$intent,
		   'merchantInvoiceNumber'=>$order_id
		   );	
		
		//$url=curl_init('https://checkout.sandbox.bka.sh/v1.0.0-beta/checkout/payment/create');
		$url=curl_init($CRED->checkout_link.'/checkout/payment/create');
		
		$createpaybodyx=json_encode($createpaybody);
		$header=array(
		        'Content-Type:application/json',
				'authorization:'.$_SESSION['token'],				
				'x-app-key:'.$CRED->body->app_key);
		curl_setopt($url,CURLOPT_HTTPHEADER, $header);
		curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($url,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($url,CURLOPT_POSTFIELDS, $createpaybodyx);
		curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);				
		$resultdata=curl_exec($url);				
		curl_close($url);		
		echo $resultdata;

?>