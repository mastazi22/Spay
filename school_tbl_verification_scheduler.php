<?php

	include ("includes/session_handler.php");
	include("includes/configure.php");	
	include ("includes/return.php");

	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
	
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT b.tc_txid, b.bank_ref_id,s.amount, s.txid FROM bisc_sp_payments AS b, sp_epay AS s WHERE  b.is_triggered =  'no' AND s.order_id = b.tc_txid AND b.epay_res_time < ( NOW( ) - INTERVAL 30 MINUTE )");
	//$sp_epay_data = mysql_fetch_assoc($sql_query);
	//print_r($sp_epay_data);exit();
	

	while ($row = mysqli_fetch_object($sql_query)) {
		//print_r($row);
                $order_id   = $row->tc_txid;
                $amount = $row->amount;
                $bank_ref_id = $row->bank_ref_id;
                $txid = $row->txid;
	
		if(isset($txid) && !empty($txid)) {
                
		    if(verificationCheck($txid) == 'NO') {       
			verification_call($order_id,$bank_ref_id,$txid,$amount);
		    }        
	
	        }

	}
	


	function verificationCheck($txid) {
                
		$returnURLcK = 'https://bisc.shurjoems.com/payment_verification_check';
		$post_data_ck = array(
			'tx_id' => $txid,
			'security_key' => '0ptP5Zk3ai0gbO5uCo0Sff2fYQo40nUO'
		);                
		$ch_ck  = curl_init();          
		curl_setopt($ch_ck,CURLOPT_URL,$returnURLcK);
		curl_setopt($ch_ck,CURLOPT_POST, 1);
		curl_setopt($ch_ck,CURLOPT_POSTFIELDS,$post_data_ck);
		curl_setopt($ch_ck,CURLOPT_RETURNTRANSFER, true);               
		curl_setopt($ch_ck, CURLOPT_SSL_VERIFYPEER, false);
		$re_check = curl_exec($ch_ck);                  
		curl_close ($ch_ck);
		return $re_check;
	 }
	
	
	function verification_call($order_id,$bank_ref_id,$txid,$amount) {

		 $date = new DateTime();
	        $date->setTimezone(new DateTimeZone("Asia/Dhaka"));

	
		$getMarchentID = 'BISC';	
		$checkURL = "https://ibanking.tblbd.com/Checkout/Checkout_Payment_Verify.asmx?WSDL";
		
		$TBLC_XmlMsgClient = new SoapClient($checkURL);
		$TBLC_VerificationParams = array(
			'OrderID' => $order_id,
			'RefID' => $bank_ref_id,						
			'MerchantID' => $getMarchentID,
		);
	
		$TBLC_XmlMsgResult = $TBLC_XmlMsgClient
			->Transaction_Verify_Details($TBLC_VerificationParams)
			->Transaction_Verify_DetailsResult;
			
		$XmlMsg = base64_decode($TBLC_XmlMsgResult);	
		$responseData = simplexml_load_string($XmlMsg);	

		
		
		$bank_verification_data = json_encode($responseData);
		//var_dump($bank_verification_data);
		//echo "<br>";
		if($responseData->Status == '1' && $responseData->StatusText == 'PAID' ) {	

			$bank_ref_id = $responseData->RefID;
			$bankTxStatus = $responseData->StatusText;			
			$spCode = '000';
			$spCodeDes = 'Successful_';
			$payment_method =  'stbl';
			$bank_status = "SUCCESS";
			$getCardOrderStatus = $responseData->CardOrderStatus?$responseData->CardOrderStatus:'';
			$getPAN = $responseData->PAN?$responseData->PAN:'';
			$getStatus = $responseData->Status;
			$getStatusText = $responseData->StatusText;
			//$bankPaymentDate = date("Y-m-d", strtotime($responseData->OrderDateTime));			
			$bankPaymentDate = date("Y-m-d", strtotime($responseData->PaymentDateTime)); 
			$getPaymentType	 = (string) $responseData->PaymentType;	
				
		
			// Update verification log
			mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE bisc_sp_payments SET is_triggered = 'yes', is_verified = 'yes' WHERE tc_txid='".$order_id."'");
			// Updating payment type
			$method = "stbl-".$getPaymentType;
			mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET method='".$method."', gw_return_id='000', gw_return_msg='".ucfirst($getCardOrderStatus)."', bank_tx_id = '".$bank_ref_id."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='000', card_number = '".$getPAN."', bank_status='SUCCESS', bank_response='".$bank_verification_data."' WHERE order_id='".$order_id."'");
			mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$getStatus."', epay_status_text='".$getStatusText."' WHERE tc_txid='".$order_id."'");
				
			
			notifyBisc($txid,$bankPaymentDate,$bank_ref_id,$method);
		
		
		} else {

			mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE bisc_sp_payments SET is_triggered = 'yes' WHERE tc_txid='".$order_id."'");
		}
	
	}
	
	
	function notifyBisc($txid,$bankPaymentDate,$bank_ref_id,$method) {
		
		$returnURL = 'https://bisc.shurjoems.com/payment_verification';
		$post_data = array(
				'tx_id' => $txid,
				'bank_tx_id'   => $bank_ref_id,
				'status' => 'PAID',
				'security_key' => '0ptP5Zk3ai0gbO5uCo0Sff2fYQo40nUO',
				'paid_date' => $bankPaymentDate,
				'method'	=> $method
				
		);

		$ch  = curl_init();		
		curl_setopt($ch,CURLOPT_URL,$returnURL);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);	
		print_r($response);
		curl_close ($ch);
		
	}

?>	

