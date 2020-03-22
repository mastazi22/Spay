<?php
	//error_reporting(E_ALL);
	include ("includes/session_handler.php");
	include("includes/configure.php");	
	include ("includes/return.php");
	include ("payment_engine/libs/PaymentStatusLatest.php");

	define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
	define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));


	// Get Merchant credentials
	$merchant_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_merchants WHERE username = 'robicash'");
	$merchant_data = mysqli_fetch_object($merchant_query);
	
	
	$_SESSION['ORDER_DETAILS']['eblID']  = $merchant_data->ebl_merchant_id;
	//'40670004';
	$_SESSION['ORDER_DETAILS']['eblPassword'] = $merchant_data->ebl_password;
	//'f032f966c37f56e10e8f43bf440468a7';
	
	include "ebl/api_lib.php";
	include "ebl/configuration.php";
	$cred['merchantObj'] = new Merchant($configArray);
	include "ebl/connection.php";



	$Object = new PaymentStatus();	
	$cred['CityBankMerchant'] = $merchant_data->mx_merchant_id;//'9101826816';

	$cred['BkashJson']  = '{"body": {"app_key":"6nsqe5lth7l2b4gf96bgrbh9ir",
		"app_secret":"84ohjtm4e7bp6v4qm66iftejf0mb14fhmb28ss3rb1hchonagd6"},
		"headers": {"username":"SHURJOMUKHI","password":"S4rM0rH1@132"},
		"checkout_link":"https://checkout.pay.bka.sh/v1.0.0-beta",
		"script_link":"https://scripts.pay.bka.sh/versions/1.0.000-beta/checkout/bKash-checkout.js"
		}';
	//$merchant_data->bkash_credentials;	

	$cred['TBLKeyCode']  = 'b28eaabc-1840-4b9b-b519-41d8accd31fd';
	$cred['TBLMerchantId']  = 'SHURJOMUKHI';			
	
	
	
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"]," SELECT b.tc_txid, b.bank_ref_id,s.amount, s.txid,s.bank_tx_id,s.order_id,s.intime,s.method,s.gateway,s.returnurl,s.bank_status,s.return_code FROM robi_sp_payments AS b, sp_epay AS s WHERE b.is_triggered =  'no' AND s.order_id = b.epay_order_id AND b.epay_res_time < ( NOW( ) - INTERVAL 5 MINUTE ) ");	

	//$sql_query = mysqli_query($GLOBALS["___mysqli_sm"]," SELECT s.amount, s.txid,s.bank_tx_id,s.order_id,s.intime,s.method,s.gateway,s.returnurl,s.bank_status,s.return_code FROM  sp_epay AS s WHERE s.intime >  ( NOW( ) - INTERVAL 15 MINUTE )  AND s.intime <  ( NOW( ) - INTERVAL 10 MINUTE ) AND s.txid like '%RCT%' ");	

	//EBL
	//$row = (object) array('gateway'=>'ebl','order_id' =>'SP5d2a25b63208b','txid' =>'RCT1563043253673','bank_tx_id'=>'RcYDDAM2Sm');

	// Citybank
	//$row = (object) array('gateway'=>'mx','order_id' =>'SP5d2eb63c4015b','txid' =>'RCT1563342395506','bank_tx_id'=>'9841742');

	// tbl
	//$row = (object) array('gateway'=>'tbl','order_id' =>'SP5d21e9b25b3c6','txid' =>'RCT1562503601832','bank_tx_id'=>'13415F3003C5C5');

	// upay
	//$row = (object) array('gateway'=>'upay','order_id' =>'SP5d2483328f7db','txid' =>'RCT1562673970039','bank_tx_id'=>'WA15626741149656');
	//echo mysqli_num_rows($sql_query);die;
	if(mysqli_num_rows($sql_query) > 0) 
	{
		while ($row = mysqli_fetch_object($sql_query)) 
		{
			if(isset($row->returnurl) && isset($row->bank_tx_id) )
	        	{
		       		$res = getPaymentStatus($row,$cred,$Object); 
		       		$spCode = $res['spCode'];
			        $spCodeDes = $res['spCodeDes'];	
	        		$log['time'] = $row->intime;
		        	$log['txid'] = $row->txid;
			        $log['gateway'] = $row->gateway;
			        $log['bank_ref_id'] = $row->bank_ref_id;
			        $log['spCode'] = $res['spCode'];
		        	$log['spCodeDes'] = $res['spCodeDes'];		        		        
			        saveLog('/var/tmp/robi_reports/schedulerLogtest-2019.log',$log);			        
	        	  	mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE robi_sp_payments SET is_triggered = 'yes',epay_status = '".$spCode."', epay_status_text = '".$spCodeDes."'  WHERE epay_order_id ='".$row->order_id."'");
			  		mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET return_code ='".$spCode."', bank_status = '".$spCodeDes."'  WHERE order_id='".$row->order_id."'");
			  		sendResponseToClient($row,$spCode,$spCodeDes);
	        	}	
		}
	}
	

	
		        

	function getPaymentStatus($row,$cred,$Object) 
	{
				

		if($row->gateway == 'mx' && !is_null($row->bank_tx_id) )
		{
			$sql = "SELECT OrderID,SessionID FROM sp_mx_transactions WHERE OrderID ='" . $row->bank_tx_id . "'";
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],$sql);
			$rowMx = mysqli_fetch_object($sql_query);			
			if(is_object($rowMx))
			{
				$OrderID = $rowMx->OrderID;
				$SessionID = $rowMx->SessionID;
				$status = $Object->mxOrderStatus($cred['CityBankMerchant'],$OrderID,$SessionID);	
			}
			//var_dump($status);
			if( isset($status) && $status == 'APPROVED' )
			{
				$response['spCode'] = '000';
		        $response['spCodeDes'] = 'SUCCESS';
			}
		}
		elseif($row->gateway == 'dbbl' && !is_null($row->bank_tx_id) )
		{
			
			$status = $Object->dbblOrderStatus($row->bank_tx_id);	
			//var_dump($status);
			if( $status == "OK" )
			{
				$response['spCode'] = '000';
		        $response['spCodeDes'] = 'SUCCESS';
			}
		}
		elseif($row->gateway == 'bkash' && !is_null($row->bank_tx_id) )
		{
			$status = $Object->bKashOrderStatus($row->bank_tx_id);	
			//var_dump($status);
			if( $status == '0000' )
			{
				$response['spCode'] = '000';
		        $response['spCodeDes'] = 'SUCCESS';
			}	
		}
		elseif($row->gateway == 'bkash_api' && !is_null($row->bank_tx_id) )
		{
			$status = $Object->bKashApiOrderStatus($row->bank_tx_id,$cred['BkashJson']);				
			//var_dump($status);
			if( $status == "Completed" )
			{
				$response['spCode'] = '000';
		        $response['spCodeDes'] = 'SUCCESS';
			}	
		}
		elseif($row->gateway == 'tbl' && !is_null($row->order_id))
		{
			$status = $Object->tblOrderStatus($row->order_id,$cred['TBLMerchantId'],$cred['TBLKeyCode']);
			if( $status == "PAID" )
			{
				$response['spCode'] = '000';
		        $response['spCodeDes'] = 'SUCCESS';
			}	
		}
		elseif($row->gateway == 'upay' && !is_null($row->order_id))
		{
			$status = $Object->upayOrderStatus($row->order_id);
			//var_dump($status);
			if( $status == "paid" )
			{
				$response['spCode'] = '000';
		        	$response['spCodeDes'] = 'SUCCESS';
			}	
		}
		elseif($row->gateway == 'ebl' && !is_null($row->bank_tx_id))
		{
			$status = $Object->eblOrderStatus($row->bank_tx_id,$cred['merchantObj']);			
			//var_dump($status);
			if( $status == "SUCCESS" )
			{
				$response['spCode'] = '000';
		        	$response['spCodeDes'] = 'SUCCESS';
			}	
		}
		else
		{
			$response['spCode'] = $row->return_code;
		    $response['spCodeDes'] = $row->bank_status;
		}
		
		return $response;
		
	}
	

	function saveLog($logFile='log.txt',$data)
	{
		// Creating log
		try
		{
			$fp = fopen($logFile, 'a');
			$response = time().'|'.json_encode($data)."\n"; 
			fwrite($fp, $response);
			fclose($fp);
		}
		catch(Exception $e) 
		{
  			echo 'Message: ' .$e->getMessage();
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
		try 
		{     
			$ch  = curl_init();		
			curl_setopt($ch,CURLOPT_URL,$row->returnurl);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$spReturnData);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);					
			curl_close ($ch);
		}
		catch (Exception $e) 
		{
			echo $msg = 'shurjoPay scheduler Error: ' . $e->getMessage();			
			//return FALSE;
		}	
	}
?>
