<?php
	include ("includes/session_handler.php");
	include("includes/configure.php");	
	include ("includes/return.php");
	
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
	// Main body
	
	$TxId  = $_GET['txid'];				
	$MerchantId = 'BISC';
	$KeyCode = '6f069c5c-57ad-49c7-840a-1e956673712a';
	$SetWaitTime = 30;// In minutes
	$SecurityKey = $_GET['security_key'];

	if(empty($SecurityKey) && $SecurityKey != '0ptP5Zk3ai0gbO5uCo0Sff2fYQo40nUO') {

		die(json_encode(
			array(
				'status'=>'Unauthorized Request'				
			)
		));
		
	}

	
	if(RequestValidation($TxId) == TRUE) 
	{	
		if($OrderId = GetOrderId($TxId)) 
		{			
			$CheckStatus = CheckStatus($OrderId,$MerchantId,$KeyCode); 	
			if($CheckStatus->StatusText == 'REQ') 
			{
				//echo "REQ";				
				$to_time = strtotime($CheckStatus->OrderDateTime); 
				$from_time = strtotime(date('Y-m-d H:i:s')); 
				$WaitTime = round(abs($to_time - $from_time) / 60);
				
				if( $WaitTime > $SetWaitTime ) 
				{
					$VerificationData = Verify($OrderId,$CheckStatus->RefID,$MerchantId);	
					//echo $VerificationData->StatusText;exit();
					//print_r($VerificationData);exit();
					if($VerificationData->StatusText == 'REQ') 
					{
						$StatusArray  = FormateData($TxId,$OrderId,$VerificationData,'CANCEL');						
						UpdateSp($StatusArray);                        						
						die(json_encode(
							array(
								'status'=>'CANCEL',
								'ref_id' => $VerificationData->RefID
							)
						));
						
					} 
					else if($VerificationData->StatusText == 'PAID' && $VerificationData->Status == '1' ) 
					{
						$StatusArray  = FormateData($TxId,$OrderId,$VerificationData,'SUCCESS');
						UpdateSp($StatusArray);                        							
						die(json_encode(
							array(
								'status'=>'SUCCESS',
								'ref_id' => $VerificationData->RefID,
								'payment_date' => $VerificationData->PaymentDateTime
							)
						));
					}
				} 
				else 
				{
			  	 // die(json_encode(array('status'=>'REQ')));						
				   die(json_encode(array('status'=>'REQ','req_time'=> $WaitTime )));
				}
				
			} 
			else if($CheckStatus->Status == '1' && $CheckStatus->StatusText == 'PAID') 
			{
				//echo "PAID";		
				if( $CheckStatus->Verified == 0 ) 
				{
					//echo "Not Verified";
					$VerificationData = Verify($OrderId,$CheckStatus->RefID,$MerchantId);					
					if( is_array($VerificationData) &&  $VerificationData != FALSE ) 
					{	
						$StatusArray  = FormateData($TxId,$OrderId,$VerificationData,'SUCCESS');							
					}
					
				} 
				else if($CheckStatus->Verified == 1) 
				{	
						//echo "Verified";
						$StatusArray  = FormateData($TxId,$OrderId,$CheckStatus,'SUCCESS');						
				}
				
				UpdateSp($StatusArray);
				//Notify($StatusArray);
				die(json_encode(
					array(
						'status'=>'SUCCESS',
						'ref_id' => $CheckStatus->RefID,
						'payment_date' => $CheckStatus->PaymentDateTime
					)
				));
				
			}
			else if($CheckStatus->StatusText == 'REJECTED') 
			{
				//echo "Rejected";
				$StatusArray  = FormateData($TxId,$OrderId,$CheckStatus,'REJECTED');	
				UpdateSp($StatusArray);	
				die(json_encode(
					array(
						'status'=>'REJECTED',
						'ref_id' => $CheckStatus->RefID,
						'payment_date' => $CheckStatus->PaymentDateTime
					)
				));
			}
			else 
			{
				die(json_encode(array('status'=>'NOT_FOUND','msg'=>'Null Response from Gateway')));	
			}
			
		} 
		else 
		{
			die(json_encode(array('status'=>'NOT_FOUND','msg'=>'Order ID not Found')));
		}
			
		
	} 
	else 
	{
		die(json_encode(array('status'=>'NOT_FOUND','msg'=>'Transaction ID not Found')));
	}
	
	
	/*
	*	@formating given data
	*	@Params array
	*	@Response array
	*/
	
	function FormateData($TxId,$OrderId,$DataArray,$Status) {
		
		$LogArray = array();
		$StatusArray = array();
		
		$bank_verification_data = json_encode($DataArray);
		$bank_ref_id = $DataArray->RefID;
		$bankTxStatus = $DataArray->StatusText;                  
		$getCardOrderStatus = $DataArray->CardOrderStatus?$DataArray->CardOrderStatus:'';
		$getPAN = isset($DataArray->PAN)?$DataArray->PAN:'';
		$getStatus = $DataArray->Status;
		$getStatusText = $DataArray->StatusText;                                                                                         
		$getPaymentType  = (string) $DataArray->PaymentType;             
		$gerOrderDateTime = $DataArray->OrderDateTime;
		$getPaymentDateTime = $DataArray->PaymentDateTime;
		
		if($Status == 'REQ') 
		{
			$LogArray = array(
				'bank_status' => 'REQ',                 
				'return_code' => '001',
				'is_verified' => 'no',
			);
		} 
		else if($Status == 'CANCEL') 
		{
			$LogArray = array(
				'bank_status' => 'CANCEL',                 
				'return_code' => '001',
				'is_verified' => 'no',
			);
			
		} 
		else if($Status == 'SUCCESS') 
		{
			$LogArray = array(
				'bank_status' => 'SUCCESS',                 
				'return_code' => '000',
				'is_verified' => 'yes',
			);			
		}
		else if($Status == 'REJECTED')
		{
			$LogArray = array(
				'bank_status' => 'REJECTED',                 
				'return_code' => '001',
				'is_verified' => 'no',
			);
		}
		$StatusArray  = array (
			'TxId' => $TxId,        
			'order_id' => $OrderId,
			'getPaymentType' => $getPaymentType,
			'method' => 'stbl',
			'bank_ref_id' => $bank_ref_id,           
			'getPAN' => json_encode($getPAN),      
			'bank_verification_data' => $bank_verification_data,
			'getStatus' => $getStatus,
			'getStatusText' => $getStatusText,      			
			'order_time'=> $gerOrderDateTime,
			'payment_time' => $getPaymentDateTime
		);  
		
		return array_merge($StatusArray,$LogArray);
	}
	
	
	/*
	*	@Update sP 
	*	@params status array 
	*	#Response boolean
	*/
	function UpdateSp($StatusArray) {
		//print_r($StatusArray);
		$BiscSpPayments = "UPDATE bisc_sp_payments SET is_verified = 'yes' WHERE tc_txid='".$StatusArray['order_id']."'";						
		//echo "\n";
		mysqli_query($GLOBALS["___mysqli_sm"],$BiscSpPayments);				
		$method = "stbl-".$StatusArray['getPaymentType'];
		//echo "\n";
		$SpEpay = "UPDATE sp_epay SET method='".$method."', gw_return_id='".$StatusArray['return_code']."', gw_return_msg='".ucfirst($StatusArray['bank_verification_data'])."', bank_tx_id = '".$StatusArray['bank_ref_id']."', gw_time='".date('Y-m-d H:i:s')."', return_code='".$StatusArray['return_code']."', card_number = '".$StatusArray['getPAN']."', bank_status='".$StatusArray['bank_status']."', bank_response='".$StatusArray['bank_verification_data']."' WHERE order_id='".$StatusArray['order_id']."'";
		mysqli_query($GLOBALS["___mysqli_sm"],$SpEpay);
		//echo "\n";
		$SpPayments = "UPDATE sp_payments SET epay_res_time='".date('Y-m-d H:i:s')."', epay_status='".$StatusArray['return_code']."', epay_status_text='".$StatusArray['bank_status']."' WHERE tc_txid='".$StatusArray['order_id']."'";
		mysqli_query($GLOBALS["___mysqli_sm"],$SpPayments);
	}
	
	/*
	*	@Request validation
	*	@params txid
	*	#Response boolean
	*/
	function RequestValidation($TxId) {
		if(isset($TxId) && $TxId != '') 
		{
			//return TRUE;
			//echo json_encode(array('status'=>true));
			return TRUE;
		} 
		else 
		{
			//return FALSE;
			//echo json_encode(array('status'=>false));
			return FALSE;
		}
	}
	
	
	/*
	*	@Get order id 
	*	@params txid
	*	#Response order id
	*/
	
	function GetOrderId($TxId) {
		//echo ("SELECT order_id FROM sp_epay WHERE txid='".$TxId."'");exit();
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT order_id FROM sp_epay WHERE txid='".$TxId."'");	
		$row = mysqli_fetch_object($sql_query);
		//var_dump($row);exit();
		return $row->order_id;
	}
	/*
	*	@Checkt status from TBL 
	*	@Order Id and merchant id
	*	#Response status xml
	*/
	function CheckStatus($OrderId,$MerchantId,$KeyCode) {

		
		 $opts = array(
        	'http' => array(
            'user_agent' => 'PHPSoapClient'
        	)
    	);
    	$context = stream_context_create($opts);

    	$soapClientOptions = array(
	        'stream_context' => $context,
	        'cache_wsdl' => WSDL_CACHE_NONE
	    );
				
		$TblPaymentInfoUrl = "https://ibanking.tblbd.com/checkout/Services/Payment_Info.asmx?WSDL";		
		try {			
			$SoapObject = new SoapClient($TblPaymentInfoUrl, $soapClientOptions);		
			$Params = array(
				'OrderID'    => $OrderId,			
				'MerchantID' => $MerchantId,
				'KeyCode'    => $KeyCode
			);

			$Response = $SoapObject->Get_Transaction_Ref($Params)->Get_Transaction_RefResult;
			$XmlMsg = base64_decode($Response);	
			$ResponseData = simplexml_load_string($XmlMsg);				
			return  json_decode(json_encode($ResponseData->TransactionInfo)); 
			//return $ResponseData->TransactionInfo;
			
		} catch (Exception $e) {
			$msg = 'Connection Error: ' . $e->getMessage();
			TLog(json_encode($msg));
			return FALSE;
		}
	}


	/*
	*	@Checkt status from TBL 
	*	@Order Id and merchant id
	*	#Response status xml
	*/
	function CheckStatus__($OrderId,$MerchantId,$KeyCode) {
				
		$TblPaymentInfoUrl = "https://ibanking.tblbd.com/checkout/Services/Payment_Info.asmx?WSDL";		
		try {			
			$SoapObject = new SoapClient($TblPaymentInfoUrl);		
			$Params = array(
				'OrderID'    => $OrderId,			
				'MerchantID' => $MerchantId,
				'KeyCode'    => $KeyCode
			);
			
			$Response = $SoapObject->Get_Transaction_Ref($Params)->Get_Transaction_RefResult;
			$XmlMsg = base64_decode($Response);	
			$ResponseData = simplexml_load_string($XmlMsg,'SimpleXMLElement', LIBXML_NOCDATA);	
			return $ResponseData->TransactionInfo;

		} catch (Exception $e) {
			$msg = 'Connection Error: ' . $e->getMessage();
			TLog(json_encode($msg));
			return FALSE;
		}
	}
	
	/*
	*	@Send notification to Merchant
	*	@params txid,bankPaymentDate,bank_ref_id,method
	*	#Response json object
	*
	*/
	function Notify($StatusArray) {
		print_r($StatusArray);
		echo "\n";
		echo "====NOTIFY====";	
		
	}
	
	/*
	*	@Send verification request to TBL
	*	@params order id , Ref id and merchant id
	*/
	function Verify($OrderID,$GetRefId,$GetMarchentID) {		
		$checkURL = "https://ibanking.tblbd.com/Checkout/Checkout_Payment_Verify.asmx?WSDL";
		try {
			$TBLC_XmlMsgClient = new SoapClient($checkURL);
			$TBLC_VerificationParams = array(
				'OrderID' => $OrderID,
				'RefID' => $GetRefId,						
				'MerchantID' => $GetMarchentID,
			);
			$TBLC_XmlMsgResult = $TBLC_XmlMsgClient
				->Transaction_Verify_Details($TBLC_VerificationParams)
				->Transaction_Verify_DetailsResult;
			$XmlMsg = base64_decode($TBLC_XmlMsgResult);	
			//$responseData = simplexml_load_string($XmlMsg);				
			$responseData = json_decode(json_encode(simplexml_load_string($XmlMsg)));				
			return $responseData;
			
		} catch (Exception $e) {
			//$response = FALSE;
			//$msg = 'Connection Error: ' . $e->getMessage();
			return FALSE;
		}	
			
	}
	
	/*
	*	@Write log to file in json format
	*	@params Msg
	*	#Response boolean
	*/
	function TLog($Msg) {
		echo $Msg;
		// Write log to file
	}
?>

