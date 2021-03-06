<?php
	include ('includes/session_handler.php');
	include ("includes/configure.php");	
	
	$orderID 	= $_SESSION['ORDER_DETAILS']['order_id'];
	$amount	 	= $_SESSION['ORDER_DETAILS']['txnAmount'];
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone('Asia/Dhaka'));

	//$checkURL = "https://ibanking.tblbd.com/TestCheckout/Checkout_Payment_Verify.asmx?WSDL";
	$checkURL = "https://ibanking.tblbd.com/Checkout/Checkout_Payment_Verify.asmx?WSDL";
	$bank_data = "";
	$getRefId  = "";
 
	if($_POST['CheckoutXmlMsg']) {
		try {	
			$getData 					= simplexml_load_string(base64_decode($_POST['CheckoutXmlMsg']));
			$bank_data 					= json_encode($getData);
			$getRefId 					= (string) $getData->RefID;
			$getOrderId 				= (string) $getData->OrderID;
			$getAmount 					= (float) $getData->Amount;
			$getTotalAmount				= (float) $getData->TotalAmount;
			$getStatus 					= (string) $getData->Status;
			$getStatusText 				= (string) $getData->StatusText;
			$getPAN 					= (string) $getData->PAN;
			$getMarchentID 				= (string) $getData->MarchentID;
			$getOrderDateTime			= (string) $getData->OrderDateTime;
			$getPaymentDateTime			= (string) $getData->PaymentDateTime;
			$getServiceCharge			= (string) $getData->ServiceCharge;
			$getCardResponseCode		= (string) $getData->CardResponseCode;
			$getCardResponseDescription = (string) $getData->CardResponseDescription;
			$getCardOrderStatus			= (string) $getData->CardOrderStatus;
			$getPaymentType				= (string) $getData->PaymentType;
			// $getAmount return amount excluding serveice charge that why Total Amount is use instead
			//if($getOrderId == $orderID && $amount <= $getAmount) {
			 if($getOrderId == $orderID && $amount <=  $getTotalAmount) {
				try {
					$TBLC_XmlMsgClient = new SoapClient($checkURL);
					$TBLC_VerificationParams = array(
						'OrderID' => $getOrderId,
						'RefID' => $getRefId,
						//'Amount' => $getAmount,
						'MerchantID' => $getMarchentID,
					);
				
					/*
					$TBLC_VerificationResult = $TBLC_VerificationClient
						->Transaction_Verify($TBLC_VerificationParams)
						->Transaction_VerifyResult;
					*/	
					$TBLC_XmlMsgResult = $TBLC_XmlMsgClient
						->Transaction_Verify_Details($TBLC_VerificationParams)
						->Transaction_Verify_DetailsResult;

					$XmlMsg = base64_decode($TBLC_XmlMsgResult);	
					$responseData = simplexml_load_string($XmlMsg);	
					
					$bank_verification_data = json_encode($responseData);
					
					if($responseData->Status == '1' && $responseData->StatusText == 'PAID' ) {					
						
					//if($TBLC_XmlMsgResult == '1'){						
					
						$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='tbl' and return_code='000'");
						$bankInfo = mysqli_fetch_object($sql_query);
						
						$_SESSION['order_details_response']['spCode'] = "000";
						$_SESSION['order_details_response']['spCodeDes'] = "Successful-tbl";
						
						$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
						$_SESSION['order_details_response']['bankTxID'] = $getOrderId;
						$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
						
						$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
						// Updating payment type
						$method = "tbl-".$getPaymentType;
						mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET method='".$method."', gw_return_id='000', gw_return_msg='".ucfirst($getCardOrderStatus)."', bank_tx_id = '".$getRefId."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='000', card_number = '".$getPAN."', bank_status='SUCCESS', bank_response='".$bank_verification_data."' WHERE order_id='".$orderID."'");
						mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$getStatus."', epay_status_text='".$getStatusText."' WHERE tc_txid='".$orderID."'");
						$response = TRUE;
					} else {
						$response = FALSE;
						$msg = 'Invalid Transaction';
					}
				} catch (Exception $e) {
					$response = FALSE;
					$msg = 'Connection Error: ' . $e->getMessage();
				}
			} else {
				$response = FALSE;
				$msg = 'Invalid Requested information.';
			}
		} catch (Exception $e) {
			$response = FALSE;
			$msg = 'XML Parsing Error. ' . $e->getMessage();
		}
	} else {
		$response = FALSE;
		$msg = 'Invalid Request.';
	}
	if($response == FALSE) {
		$_SESSION['order_details_response']['txID']			= $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] 	= $getRefId;
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		$_SESSION['order_details_response']['txnAmount'] 	= $_SESSION['ORDER_DETAILS']['txnAmount'];
		$_SESSION['order_details_response']['spCode'] 		= '001';
		$_SESSION['order_details_response']['spCodeDes'] 	= 'Bank Transaction Failed.-tbl';
		mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='001', gw_return_msg='Declined', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='0', bank_status='FAIL', bank_response='".$bank_data. ". Message: " .$msg."' WHERE order_id='".$orderID."'");
		mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='0', epay_status_text='FAILED' WHERE tc_txid='".$orderID."'");
	}
	header("Location: ".$db->local_return_url);	
	exit;	
