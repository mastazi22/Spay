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
	$request_token=bkash_Get_Token($CRED);
	$idtoken=$request_token['id_token'];
	
	$_SESSION['token']=$idtoken;
	
	echo $idtoken;
	

	function bkash_Get_Token($CRED){
		
	$post_token=array(
	       'app_key'=>$CRED->body->app_key,
		   'app_secret'=>$CRED->body->app_secret,
	);	
	
	//$url=curl_init('https://checkout.sandbox.bka.sh/v1.0.0-beta/checkout/token/grant');
	$url=curl_init($CRED->checkout_link.'/checkout/token/grant');
		
	$posttoken = json_encode($post_token);
	$header    = array(
        'Content-Type:application/json',				
		'password:'.$CRED->headers->password,
		'username:'.$CRED->headers->username);

		curl_setopt($url,CURLOPT_HTTPHEADER, $header);
		curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($url,CURLOPT_POSTFIELDS, $posttoken);
		curl_setopt($url,CURLOPT_SSL_VERIFYPEER, false);				 
		curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
		$resultdata=curl_exec($url);
		//var_dump($resultdata);exit();
		curl_close($url);
		return json_decode($resultdata, true);
	}

?>