<?php
session_start();
include ('payment_engine/libs/inc/DB.php');
include ('payment_engine/dbconfig.php');

print_r($_REQUEST);
die;
//echo "1";
$statusCode = $_REQUEST['paymentStatus'];   
$mtbTxId = $_REQUEST['mtbTransactionNo'];   
$mtbErrorCode = $_REQUEST['errorLevel'];

$txnCheckResponse = "|";

$DB = new DB();
//session_start();

$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
$bank_response = serialize($_REQUEST);
$gw_time = date("Y-m-d h:i:s");

$status = "FAILED";
$gri = "202";
$rmsg = "FAIL";
$rc = "202";
#set default sessions to fail
$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
$_SESSION['order_details_response']['spCode'] = "096";
$_SESSION['order_details_response']['spCodeDes'] = "Transaction FAILED";
if($statusCode=="S")
{
	$response = checkMTBTransaction($mtbTxId);
	if($response==true) {
		$status = "SUCCESS";
		$gri = "000";
		$rmsg = "Approved";
		$rc = "000";

		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
		$_SESSION['order_details_response']['txnAmount'] = floor($xml->amount);
		$_SESSION['order_details_response']['spCode'] = "000";
		$_SESSION['order_details_response']['spCodeDes'] = "Approved";		
	}
}


$update_array = array(
			"bank_response"=>$bank_response . "|CHK-RES=" . htmlentities($response) ,
			"gw_time"=>$gw_time,
			"bank_tx_id"=>$mtbTxId,
			"bank_status"=>$status,
			"gw_return_id"=>$gri,
			"gw_return_msg"=>$rmsg,
			"return_code"=>$rc
		     );
$updateTable = $DB->update("sp_epay", $update_array, $where);




function checkMTBTransaction($txid) {

	$url = "https://mbank.mutualtrustbank.com/MTBEcomService/MTBEcomService.asmx";
	#credentials
	$params = array(
		"userName"=>"shurjomukhiMtbAdmin",
		"password"=>"12345@542#21487",
		"merchantTransId"=>$txid
	);
	#soap request
	$params = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			  <soap:Body>
			    <CheckTransaction xmlns="http://mutualtrustbank.com/">
			      <userName>Shurjomukhi</userName>
			      <password>@2!04s#d932</password>
			      <merchantTransId>'.$txid.'</merchantTransId>
			    </CheckTransaction>
			  </soap:Body>
			</soap:Envelope>';

	#soap header
	$headers = array(
		'Host: 124.109.104.62',
		'Content-Type: text/xml; charset=utf-8',
		'Content-Length: '. strlen($params),
		'SOAPAction: "http://mutualtrustbank.com/CheckTransaction"'
	);

	#send request to MTB
	$handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($handle, CURLOPT_POST, true);
	curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec($handle);
	$txnCheckResponse = $response;
	if(strstr($response, '<status>S</status>')) {
		return true;
	} else {
		return $response;
	}
}

header("Location:return_url.php");
