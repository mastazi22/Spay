<?php
	error_reporting(E_ALL);
	function dbbl_verification($ip,$bank_tx_id) 
	{
		
		$node_request_data = array(     
			'action' => 'verify',	
			'trans_id' => $bank_tx_id,	
			'ip' => $ip
		);
	
		
		$queryString =  http_build_query($node_request_data);   
		try 
		{
			$ch = curl_init();                                              
			//$host = "http://210.4.73.118:8081?$queryString";
			$host = "http://node.shurjopay.com?$queryString";
			curl_setopt($ch, CURLOPT_URL ,$host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$outputArray=array();
			$curl_response_data = curl_exec($ch);
			$outputArray = explode("\n",$curl_response_data);
			curl_close($ch);		

		}
		catch(Exception $e) 
        {
 			echo 'Message: ' .$e->getMessage();
		}

		$paymentStatusRaw 	 = explode(":",$outputArray[0]);
		$bankReturnAmountRaw = explode(":",$outputArray[8]);
		$cardNumberRaw 	  	 = explode(":",$outputArray[6]);
		$cardHolderNameRaw	 = explode(":",$outputArray[9]);

		$paymentStatus   	= trim(end($paymentStatusRaw));
		$bankReturnAmount 	= trim(end($bankReturnAmountRaw));
		$cardNumber 		= trim(end($cardNumberRaw));
		$cardHolderName 	= trim(end($cardHolderNameRaw));
		
		
		
		$response_data = array(
			//'txid'  		   => $data['txid'],
			//'method' 		   => $data['method'],
			'status' 		   => $paymentStatus,
			'bankReturnAmount' => $bankReturnAmount,
			'cardNumber'	   => $cardNumber,
			'cardHolderName'   => $cardHolderName
		);
		return $response_data;		
	}
	
	
	function notify_anser($data) {
	
		$request_data = array(     
			'txID' => $data['txID'],  
			'bankTxID' => $data['bankTxID'],      
			'bankTxStatus' => $data['bankTxStatus'],
			'txnAmount' => $data['txnAmount'],
			'spCode' => $data['spCode'],
			'spCodeDes' => $data['spCodeDes'],
			'paymentOption' => $data['paymentOption']

		);
		//print_r($node_request_data);exit();
		$queryString =  http_build_query($request_data);   
		$ch = curl_init();                                              		
		$host = "http://103.48.16.225:8080/api/update_payment_history";
		curl_setopt($ch, CURLOPT_URL ,$host);
		curl_setopt($ch,CURLOPT_POST, 1); //0 for a get request
		curl_setopt($ch,CURLOPT_POSTFIELDS,$queryString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response_data = curl_exec($ch);
		curl_close($ch);
		return $response_data;		
		
	}

	

	
	
	

?>
