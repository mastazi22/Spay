<?php
	error_reporting(0);
	include ("includes/session_handler.php");
	include("includes/configure.php");	
	include ("includes/return.php");
	include ("payment_engine/libs/PaymentStatus.php");

	define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
	define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));
	
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"]," SELECT b.tc_txid, b.bank_ref_id,s.amount, s.txid,s.bank_tx_id,s.order_id,s.intime,s.method,s.gateway,s.returnurl FROM robi_sp_payments AS b, sp_epay AS s WHERE b.is_triggered =  'no' AND s.order_id = b.epay_order_id AND b.epay_res_time < ( NOW( ) - INTERVAL 5 MINUTE )");	

	if($sql_query) 
	{
		while ($row = mysqli_fetch_object($sql_query)) 
		{
			
	        $spCode = '';			
	        $spCodeDes = '';

	        if($row->returnurl)
	        {
	        	if($row->gateway == 'mx')
	        	{
	        		$status = getPaymentStatus($row,$spCode,$spCodeDes);   
		        	if($status == 'APPROVED') 
		        	{
		        		$spCode = '000';
		        		$spCodeDes = 'SUCCESS';
		        	}	
		        	else
		        	{
		        		$spCode = '001';
		        		$spCodeDes = 'FAIL';
		        	}
	        	}
	        	else
	        	{
	        		$spCode    = isset($row->return_code)?$row->return_code:'001';
					$spCodeDes = isset($row->bank_status)?$row->bank_status:'FAIL';	
	        	}	

	        	sendResponseToClient($row,$spCode,$spCodeDes);

	        	mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE robi_sp_payments SET is_triggered = 'yes',epay_status = '".$spCode."', epay_status_text = '".$spCodeDes."'  WHERE epay_order_id ='".$row->order_id."'");
				mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET return_code ='".$spCode."', bank_status = '".$spCodeDes."'  WHERE order_id='".$row->order_id."'");

	        }	

		}
	}
	

	function sendResponseToClient($row,$spCode,$spCodeDes)
	{
		$post_data = '<?xml version="1.0" encoding="utf-8"?>
			<spResponse><txID>'.$row->txid.'</txID>
			<bankTxID>'.$row->bank_tx_id.'</bankTxID>
			<bankTxStatus>'.$spCodeDes.'</bankTxStatus>
			<txnAmount>'.$row->amount.'</txnAmount>
			<spCode>'.$spCode.'</spCode>
			<spCodeDes>'.$spCodeDes.'</spCodeDes>
			<orderID>'.$row->order_id.'</orderID>
			<time>'.$row->intime.'</time>
			<paymentOption>'.$row->method.'</paymentOption></spResponse>';

		

		$returnXML = new ReturnXML();		
		$data = json_encode(array ('spay_data' => $post_data));
		$spReturnData['spdata'] = base64_encode($returnXML->get_encrypted_data($data, PUBLIC_KEY));        
		$ch  = curl_init();		
		curl_setopt($ch,CURLOPT_URL,$row->returnurl);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$spReturnData);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);					
		curl_close ($ch);
	}

	function getPaymentStatus($row,$spCode,$spCodeDes) 
	{

		if($row->gateway == 'mx')
		{
			$Merchant = '9101827699';// Robi mx merchant key
			$mxObject = new PaymentStatus();
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"]," SELECT OrderID,SessionID FROM sp_mx_transactions WHERE transaction_id='" . $row->txid . "'");
			$row = mysqli_fetch_object($sql_query);			
			$OrderID = $row->OrderID;
			$SessionID = $row->SessionID;
			return $mxObject->mxOrderStatus($Merchant,$OrderID,$SessionID);	
		}

		
	}
?>