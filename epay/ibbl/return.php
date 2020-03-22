<?php
include ('../../payment_engine/libs/inc/DB.php');
include ('../../payment_engine/dbconfig.php');
$DB = new DB();
session_start();
$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
//print_r($where);
$bank_response = serialize($_REQUEST);
$gw_time = date("Y-m-d h:i:s");
//echo $gw_time; exit;
$datafrombank = array("bank_response"=>$bank_response,"gw_time"=>$gw_time);
$updateTable = $DB->update("sp_epay", $datafrombank, $where);
$statusCode = $_REQUEST['responseCode'];   
$returntoken = $_REQUEST['token']; //Comes directly from IBBL so fltering may change the value

//print_r($_SESSION);
//echo $_SESSION['ORDER_DETAILS']['order_id'];

$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
$getInfo = $DB->select("sp_epay",$where);
$bank_tx_id=$getInfo[0]->bank_tx_id;
//echo $bank_tx_id."<br />".$returntoken;
//if ($bank_tx_id != $returntoken)
//{
	switch($_SERVER["SERVER_NAME"])
	{
		// Testing site (dev.shurjomukhi.com) configuration
	  	case 'dev.shurjomukhi.com':
			$url = 'https://ipaysafe-ws.islamibankbd.com:8998/services/CheckPaymentStatusService';
		break;
	  	// Development site (e.g. localhost) configuration
	  	case 'localhost':
			$url = 'http://localhost/debug/ibblcheck.php';
		break;
		default:
			$url = 'https://ipaysafe-ws.islamibankbd.com:8998/services/CheckPaymentStatusService';
		break;
	}
//	echo $url;
	$method = 'POST';
	$encodeValue=base64_encode("1bbl1P@ys@f3Cl13nt:1bbl1P@ys@f3Cl13ntP@ss");
	$auth='Basic '.$encodeValue;
	$headers = array(
		'Content-Type:application/xml',
		'clientId:IBB.MRCNT.99131204164840',
		'token:'.$returntoken,
		'merchantSecret:7f486103d1f0ed36276677e9d5a44f66c45ea065',
		'Authorization:'.$auth,
	);
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
   
    switch($method)
    {
		case 'POST':
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, null);
		break;
	}
	$response = curl_exec($handle);
	$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
//	echo $response;
	$xml = simplexml_load_string($response);
	//print_r($xml);
	if (($xml->errorCode == "0") and ($xml->paymentStatus == "SUCCESS"))
	{
		
		if (($_SESSION['ORDER_DETAILS']['txnAmount']) == floor($xml->amount))
		{
			//echo "ALL SET";
			
			$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
			$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
			$_SESSION['order_details_response']['txnAmount'] = floor($xml->amount);
			$_SESSION['order_details_response']['spCode'] = "000";
			$_SESSION['order_details_response']['spCodeDes'] = "Approved";
			//print_r($_SESSION['order_details_response']);
			$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
			$data = array("gw_return_id"=>"000","gw_return_msg"=>"Approved","return_code"=>"000","bank_status"=>"SUCCESS");
			$returntable = $DB->update("sp_epay",$data,$where);
			
		} else
		{
			//echo "Amount Mismatch";
			$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$_SESSION['order_details_response']['txnAmount'] = floor($xml->amount);
			$_SESSION['order_details_response']['spCode'] = "202";
			$_SESSION['order_details_response']['spCodeDes'] = "Amount Mismatch";
			$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
			$data = array("gw_return_id"=>"202","gw_return_msg"=>"Fail","return_code"=>"202","bank_status"=>"Amount Mismatch");
			$returntable = $DB->update("sp_epay",$data,$where);
		}
	} else
	{
		//echo "Bank transacton Failed";
			$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
			$_SESSION['order_details_response']['spCode'] = "096";
			$_SESSION['order_details_response']['spCodeDes'] = "Transaction FAILED";
			$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
			$data = array("gw_return_id"=>$xml->errorCode,"gw_return_msg"=>$xml->paymentStatus,"return_code"=>"096","bank_status"=>$xml->paymentStatus);
			$returntable = $DB->update("sp_epay",$data,$where);
	}
	
//} else
/*{
	//echo "Token Mismatch";
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
		$_SESSION['order_details_response']['spCode'] = "202";
		$_SESSION['order_details_response']['spCodeDes'] = "Token Mismatch";
		$where = array("order_id"=>$_SESSION['ORDER_DETAILS']['order_id']);
		$data = array("gw_return_id"=>$xml->errorCode,"gw_return_msg"=>$xml->paymentStatus,"return_code"=>"202","bank_status"=>"Token Mismatch");
		$returntable = $DB->update("sp_epay",$data,$where);
}
**/
//header("Location: http://".$_SERVER["SERVER_NAME"]."/return_url.php");
/*
echo "<pre>";
	print_r($_SESSION['order_details_response']);
echo "</pre>";
*/
header("Location:/return_url.php");
?>
