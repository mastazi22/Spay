<?php
/**
 * Transaction info recieved from DBBL ECOM server
 *
 * @author    Sanjeewa Jayasinghe <sanjeewaj@interblocks.com>
 * @copyright Interblocks - http://www.interblocks.com
 *
 * Source adopted by Shurjomukhi developers from DBBL sample
 * @author: 
 * 	1. Sahedul Hasan <sahedul.hasan@shurjomukhi.com.bd>
 * 	2. Shouro Chowndhury <shouro.chowdhury@shurjomukhi.com.bd>
 * 	3. Imtiaz Rahi <imtiaz.rahi@shurjomukhi.com.bd>
 */

include("../../includes/configure.php");
include ('../../includes/session_handler.php');


function openssl_decrypt_data($post){
		switch($_SERVER["SERVER_NAME"]) {
			
			case 'localhost':
			  	define('PUBLIC_KEY', file_get_contents('C:\svn\sp_key\public.pem'));
				define('PRIVATE_KEY', file_get_contents('C:\svn\sp_key\private.key'));
			break;
		  	default:  
			  	define('PUBLIC_KEY', file_get_contents('/etc/sp_key/public.pem'));
				define('PRIVATE_KEY', file_get_contents('/etc/sp_key/private.key'));
			break;
		}
		
		$m = json_decode(base64_decode($post), true);
		
		$data = get_original_data($m['msg'], $m['key'], PRIVATE_KEY);
		$msg_data = json_decode($data, true);		
		return $msg_data['output'];
	}
function get_original_data($msg, $key, $private_key) {

		if ($msg == '') {
			return false;
		} 
		else {
			$privateKey = openssl_get_privatekey($private_key);
			$result = openssl_open(base64_decode($msg), $decryptedData, base64_decode($key), $privateKey);
			return $decryptedData;
		}
	}
	
$post_vars=openssl_decrypt_data($_POST['output']);

$final_result = explode(':',$post_vars[0]);
$final_result_ps = explode(':',$post_vars[1]);
$final_result_code = explode(':',$post_vars[2]);
$final_result_rrn = explode(':',$post_vars[3]);
$final_result_4 = explode(':',$post_vars[4]);
$product_price=$post_vars[14];

$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
if(trim($final_result[1])=="FAILED"){
	dbblFailed($order_id, $final_result,$final_result_ps,$final_result_code,$final_result_rrn,$final_result_4);
}
else{
	dbblApproved($order_id, $final_result,$final_result_ps,$final_result_code,$final_result_rrn,$final_result_4);
}
header("Location: ".$db->local_return_url);
exit;

function dbblApproved($order_id, $final_result,$final_result_ps,$final_result_code,$final_result_rrn,$final_result_4) {
   echo "dbbl Approved";
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
echo trim($final_result_code[1])."<br/>";
echo trim($final_result[1])."<br/>";
echo $product_price."<br/>";	
echo $_SESSION['ORDER_DETAILS']['txnAmount']*100;	
exit;	
	// check if retrun array data contain RESULT_CODE == 000 then update the status to OK. 
	// else we assume its failed transaction
	if (trim($final_result_code[1])=='000' and trim($final_result[1])=='OK' and ($_SESSION['ORDER_DETAILS']['txnAmount']*100)==$product_price) {
		$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
	} 
	else {
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
	}
	$data['data']=$final_result[0].":".$final_result[1].";".$final_result_ps[0].":".$final_result_ps[1].";".$final_result_code[0].":".$final_result_code[1].";".$final_result_rrn[0].":".$final_result_rrn[1].";".$final_result_4[0].":".$final_result_4[1];

	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] = $order_id;
	
	
	$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='dbbl' and return_code='".trim($final_result_code[1])."'");
	$result = mysqli_fetch_object($sql_query);

	$_SESSION['order_details_response']['spCode'] = $result->return_code;
	$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;

	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$_SESSION['order_details_response']['spCode']."', gw_return_msg='".$_SESSION['order_details_response']['spCodeDes']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='".$_SESSION['order_details_response']['bankTxStatus']."', bank_response='".$data['data']."' WHERE order_id='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_SESSION['order_details_response']['spCode']."', epay_status_text='".$_SESSION['order_details_response']['spCodeDes']."' WHERE tc_txid='".$order_id."'");
	//$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_dbbl_transactions SET transaction_id='".$order_id."', posted_data='".$data['data']."', returned_array_data='".$data['data']."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

	return true;
}
function dbblFailed($order_id, $final_result,$final_result_ps,$final_result_code,$final_result_rrn,$final_result_4) {
echo "dbbl Failed";
	$date = new DateTime();
	$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
	
		echo trim($final_result_code[1])."<br/>";
echo trim($final_result[1])."<br/>";
echo $product_price."<br/>";	
echo $_SESSION['ORDER_DETAILS']['txnAmount']*100;	
exit;	
	// check if retrun array data contain RESULT_CODE == 000 then update the status to OK. 
	// else we assume its failed transaction
	if (trim($final_result_code[1])=='000' and trim($final_result[1])=='OK' and ($_SESSION['ORDER_DETAILS']['txnAmount']*100)==$product_price) {
		$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
	} 
	else {
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
	}
	$data['data']=$final_result[0].":".$final_result[1].";".$final_result_ps[0].":".$final_result_ps[1].";".$final_result_code[0].":".$final_result_code[1].";".$final_result_rrn[0].":".$final_result_rrn[1].";".$final_result_4[0].":".$final_result_4[1];

	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] = $order_id;
		
	$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='dbbl' and return_code='".trim($final_result_code[1])."'");
	$result = mysqli_fetch_object($sql_query);

	$_SESSION['order_details_response']['spCode'] = $result->return_code;
	$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;

	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$_SESSION['order_details_response']['spCode']."', gw_return_msg='".$_SESSION['order_details_response']['spCodeDes']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$data['data']."' WHERE order_id='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_SESSION['order_details_response']['spCode']."', epay_status_text='".$_SESSION['order_details_response']['spCodeDes']."' WHERE tc_txid='".$order_id."'");
	//$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_dbbl_transactions SET transaction_id='".$order_id."', posted_data='".$data['data']."', returned_array_data='".$data['data']."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

	return true;
}
