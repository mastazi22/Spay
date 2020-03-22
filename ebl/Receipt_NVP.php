<?php
session_start();

include "api_lib.php";
include "configuration.php";
include "connection.php";
include("../includes/configure.php");
include("../includes/session_handler.php");
include("../includes/ebl.php");

error_reporting(E_ALL);

$errorMessage = "";
$errorCode = "";
$gatewayCode = "";
$result = "";

$responseArray = array();

$resultInd = (isset($_GET["resultIndicator"])) ? $_GET["resultIndicator"] : "";
$successInd = $_SESSION['successIndicator'];


$orderID = $_SESSION['orderID'];

$merchantObj = new Merchant($configArray);

$parserObj = new Parser($merchantObj);

$requestUrl = $parserObj->FormRequestUrl($merchantObj);

$request_assoc_array = array("apiOperation" => "RETRIEVE_ORDER",
    "order.id" => $orderID
);


	// creating log
	//$current = time().'_'.$orderID."\n";
	//$fp = fopen('return_response.txt', 'a');
	//fwrite($fp, $current);
	//fclose($fp);

$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
$response = $parserObj->SendTransaction($merchantObj, $request);

$new_api_lib = new api_lib;
$parsed_array = $new_api_lib->parse_from_nvp($response);


	// creating log
	$current = date('Y-m-d H:i:s').'_'.$orderID.'_'.json_encode($parsed_array)."\n";
	$fp = fopen('return_response_new.txt', 'a');
	fwrite($fp, $current);
	fclose($fp);


function checkCardCountry($cardNumber)
{
    $card = substr($cardNumber, 0, 6);
    $curl = curl_init('https://binlist.net/json/' . $card);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    $result=json_decode($result);
    return $result;
}

	if ($parsed_array['status'] == 'CAPTURED' && $parsed_array['result'] == 'SUCCESS' && strcmp($resultInd, $successInd) == 0) 
	{
		$cardCountry = checkCardCountry($parsed_array['sourceOfFunds.provided.card.number']);
		
		// Values to fraud check 
		/*
		$ECI 		        = $parsed_array['transaction.3DSecure.acsEci'];
		$epay_txid 	        = $parsed_array['id'];
		$bank_order_id       = $parsed_array['transaction.order.id'];
		$res_transaction_id = $parsed_array['transaction.transaction.id'];
		$raw_data = json_encode($parsed_array);
		*/
		$raw_data = json_encode($parsed_array);
		$data = (array)json_decode($raw_data);
		$ECI 		        = $data['transaction[0].3DSecure.acsEci'];//$parsed_array['transaction.3DSecure.acsEci'];
		$epay_txid 	        = $parsed_array['id'];
		$bank_order_id      = $data['transaction[0].order.id'];//$parsed_array['transaction.order.id'];
		$res_transaction_id = $data['transaction[0].transaction.id'];//$parsed_array['transaction.transaction.id'];
		$acquirer_transactionId  = $data['transaction[0].transaction.acquirer.transactionId'];

		if ($cardCountry->country->alpha2 == "BD") 
		{
			
			if( $ECI=='7' || $ECI=='0' ) 
			//if( $parsed_array['sourceOfFunds.provided.card.number'] == '434978xxxxxx5885' ) 	
			//if( $parsed_array['sourceOfFunds.provided.card.number'] == '452017xxxxxx7002' ) 
			{
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_status='Block By Site Admin' ,return_code='999' WHERE order_id='" . $order_id . "'");				
				
					$request_assoc_array = array (
						"apiOperation" => "VOID",
						"transaction.targetTransactionId" => $res_transaction_id,//$epay_txid,
						"order.id" => $epay_txid,
						"transaction.id" => 'VOID-' .$res_transaction_id
					);
					
				$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
				$response = $parserObj->SendTransaction($merchantObj, $request);				
				$parsed_array_new = $new_api_lib->parse_from_nvp($response);				
				// creating log
				$current .= $ECI."|".$bank_order_id."|".$res_transaction_id."|".$response."\n";
				$fp = fopen('void_response.txt', 'a');
				fwrite($fp, $current);
				fclose($fp);			
				header("Location: https://shurjopay.com/block.php");
			}
			else 
			{
				$date = new DateTime();
				$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
				$order_id = $_SESSION['ORDER_DETAILS']['order_id'];

				
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");

				$ebl = new Ebl();
				$ebl->eblApproved($order_id, $parsed_array, $response);
				header("Location: " . $db->local_return_url);
			}
				
			
		} 
		else 
		{
			$datetimecurrent = date("Y-m-d");
			$eblcardlimit = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from ebl_bin_limit WHERE id=1");
			$resulteblcardlimit = mysql_fetch_object($eblcardlimit);
			$eblcardlimitspEpay = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT COUNT(`card_number`) as total_txn_in_day  from sp_epay WHERE card_number='" . $parsed_array['sourceOfFunds.provided.card.number'] . "' and bank_status='SUCCESS' and return_code='000' and gw_time LIKE '%$datetimecurrent%'");
			$resulteblcardlimitspEpay = mysql_fetch_object($eblcardlimitspEpay);
			
			if ( $resulteblcardlimitspEpay->total_txn_in_day >= $resulteblcardlimit->limit ) 
			{
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_status='Block By Site Admin' ,return_code='999' WHERE order_id='" . $order_id . "'");
			   // Void these transaction //Start
				/*
				$request_assoc_array = array (
					"apiOperation" => "VOID",
					"transaction.targetTransactionId" => $epay_txid,
					"order.id" => $epay_txid,
					"transaction.id" => $acquirer_transactionId//$res_transaction_id 
				);
				*/

				$request_assoc_array = array (
                                                "apiOperation" => "VOID",
                                                "transaction.targetTransactionId" => $res_transaction_id,//$epay_txid,
                                                "order.id" => $epay_txid,
                                                "transaction.id" => 'VOID-' .$res_transaction_id                
                                        );


				$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
				$response = $parserObj->SendTransaction($merchantObj, $request);				
				$parsed_array = $new_api_lib->parse_from_nvp($response);				
				// creating log
				$current .= $ECI."|".$bank_order_id."|".$res_transaction_id."|".$response."\n";
				$fp = fopen('void_response.txt', 'a');
				fwrite($fp, $current);
				fclose($fp);	
			
				header("Location: https://shurjopay.com/block.php");
			} 
			else if($ECI=='7' || $ECI=='0' || $ECI=='6' || $ECI=='1' ) 
			{	
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_status='Block By Site Admin' ,return_code='999' WHERE order_id='" . $order_id . "'");
			   // Void these transaction //Start
				/*
				$request_assoc_array = array (
					"apiOperation" => "VOID",
					"transaction.targetTransactionId" => $epay_txid,
						"order.id" => $epay_txid,
						"transaction.id" => $acquirer_transactionId//$res_transaction_id
					);
				*/
				$request_assoc_array = array (
                                                "apiOperation" => "VOID",
                                                "transaction.targetTransactionId" => $res_transaction_id,//$epay_txid,
                                                "order.id" => $epay_txid,
                                                "transaction.id" => 'VOID-' .$res_transaction_id                
                                        );

				$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
				$response = $parserObj->SendTransaction($merchantObj, $request);				
				$parsed_array = $new_api_lib->parse_from_nvp($response);				
				// creating log
				//$current .= $response."\n";
				$current .= $ECI."|".$bank_order_id."|".$res_transaction_id."|".$response."\n";
				$fp = fopen('void_response.txt', 'a');
				fwrite($fp, $current);
				fclose($fp);	
				header("Location: https://shurjopay.com/block.php");
			}
			else 
			{
				//we can't use function i faced some problem ................thats why code reapet.......push live code with out test
				$date = new DateTime();
				$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
				$order_id = $_SESSION['ORDER_DETAILS']['order_id'];

				$epay_txid = $parsed_array['id'];
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
				$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");

				$ebl = new Ebl();
				$ebl->eblApproved($order_id, $parsed_array, $response);
				header("Location: " . $db->local_return_url);
			}

		}


	}
	else 
	{
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='" . $order_id . "'");
		echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
	}
?>
   
  	
   

