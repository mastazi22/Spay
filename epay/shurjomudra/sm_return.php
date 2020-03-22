<?php
include ('../../includes/session_handler.php');
include("../../includes/configure.php");
$dataPOST = $_POST['smdata'];
if (!(strpos($dataPOST, '<txnID>') === false && strpos($dataPOST, '</txnID>') === false)) {
	$txnID = substr($dataPOST, (strpos($dataPOST, '<txnID>') + 7), (strpos($dataPOST, '</txnID>') - (strpos($dataPOST, '<txnID>') + 7)));
}
if (!(strpos($dataPOST, '<txnStatus>') === false && strpos($dataPOST, '</txnStatus>') === false)) {
	$txnStatus = substr($dataPOST, (strpos($dataPOST, '<txnStatus>') + 11), (strpos($dataPOST, '</txnStatus>') - (strpos($dataPOST, '<txnStatus>') + 11)));
}
if (!(strpos($dataPOST, '<mudraID>') === false && strpos($dataPOST, '</mudraID>') === false)) {
	$mudraID = substr($dataPOST, (strpos($dataPOST, '<mudraID>') + 9), (strpos($dataPOST, '</mudraID>') - (strpos($dataPOST, '<mudraID>') + 9)));
}
//echo $txnID."==".$txnStatus."==".$txnAmount."==".$mudraID;exit;
$date = new DateTime();
$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
$payment_sql = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT gw_return_id, bank_status, amount FROM sp_epay WHERE order_id='".$mudraID."'");
$payment_result = mysqli_fetch_object($payment_sql);

$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
if($payment_result->bank_status == 'SUCCESS' and $payment_result->gw_return_id == '000') {
	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] = $txnID;	
	$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
	$_SESSION['order_details_response']['txnAmount'] = $payment_result->amount;
	$_SESSION['order_details_response']['spCode'] = "000";
	$_SESSION['order_details_response']['spCodeDes'] = "Successful";
	$bank_data = $dataPOST;
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_time='".$date->format('Y-m-d H:i:s')."', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epa;res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_SESSION['order_details_response']['spCode']."', epay_status_text='".$_SESSION['order_details_response']['spCodeDes']."' WHERE tc_txid='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_shurjomudra_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
}
else {
	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] = $txnID;	
	$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
	$_SESSION['order_details_response']['txnAmount'] = $payment_result->amount;
	$_SESSION['order_details_response']['spCode'] = "001";
	$_SESSION['order_details_response']['spCodeDes'] = "Failed";
	$bank_data = $dataPOST;
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_time='".$date->format('Y-m-d H:i:s')."', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epa;res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_SESSION['order_details_response']['spCode']."', epay_status_text='".$_SESSION['order_details_response']['spCodeDes']."' WHERE tc_txid='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_shurjomudra_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
}
header("Location: ".$db->local_return_url);
exit;
?>
