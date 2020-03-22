<?php
include ("includes/configure.php");
include ('includes/session_handler.php');

$mobile_sender = '0'.$_GET['sender'];

$trxid = $_REQUEST['trxid'];
$sql="select amount from sp_epay where order_id='".$trxid."'";
$result = mysqli_query($GLOBALS["___mysqli_sm"], $sql);
$epay = mysql_fetch_array($result);
//print_r($epay);
$send_amount=$epay[0];

$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Dhaka'));

$sql = "SELECT TextDecoded, ID FROM inbox where  TextDecoded LIKE '%$mobile_sender%' and TextDecoded LIKE '%$trxid%'and status!='1' and `SenderNumber` = '16216' and `SMSCNumber` LIKE '+88%'";

$result = mysqli_query($GLOBALS["___mysqli_sm"], $sql);
$num = mysql_num_rows($result);
if ($num > 0) {
	while ($row = mysql_fetch_array($result)) {
		$msg = $row['TextDecoded'];
		$pieces = explode(" ", $msg);
	
		$amount = substr($pieces[5],3,strlen($pieces[5])-3);
		$amount = str_replace(',', '', $amount);
		$mobile_number = $pieces[7];
		$trx_id = $pieces[15];
		$dte = $pieces[17];
		$time = $pieces[18];
		
		if ($amount >= $send_amount) {
			$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$trxid."' WHERE tc_txid='".$order_id."'");
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$trxid."' WHERE order_id='".$order_id."'"); 
			$inbox_result = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE inbox SET status  ='1' where TextDecoded LIKE '%$mobile_sender%'and TextDecoded LIKE '%$trxid%'");
			
			
			$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
			$_SESSION['order_details_response']['bankTxID'] = $trxid;
			$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
			$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
			
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='dmw' and return_code='000'");
			$result = mysql_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = $result->return_code;
			$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
	
			//$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";
			$bank_data = $msg;
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='".$result->return_code."', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='SUCCESS', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_dmw_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
			echo '1';
			exit;
		}
		else {
			$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$trxid."' WHERE tc_txid='".$order_id."'");
			$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$trxid."' WHERE order_id='".$order_id."'"); 
			$inbox_result = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE inbox SET status  ='0' where TextDecoded LIKE '%$mobile_sender%'and TextDecoded LIKE '%$trxid%'");
			
			
			$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
			$_SESSION['order_details_response']['bankTxID'] = $trxid;
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
			
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='dmw' and return_code='002'");
			$result = mysql_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = $result->return_code;
			$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
	
			//$bank_data = "OrderID|".$transactionID."||TransactionType|SUCCESS||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|SUCCESS||ApprovalCode|";
			$bank_data = $msg;
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='".$result->return_code."', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_dmw_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
			echo '1';
			exit;
		} // end nested else
	
	} // end while
	exit;

} // end if
else {
	$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_txid='".$trxid."' WHERE tc_txid='".$order_id."'");
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id='".$trxid."' WHERE order_id='".$order_id."'");
	
	
	$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
	$_SESSION['order_details_response']['bankTxID'] = $trxid;
	$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
	$_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
	
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='dmw' and return_code='001'");
	$result = mysql_fetch_object($sql_query);

	$_SESSION['order_details_response']['spCode'] = $result->return_code;
	$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;

	$bank_data = "OrderID|".$trxid."||TransactionType|FAIL||Currency|BDT||Amount|".$_SESSION['ORDER_DETAILS']['txnAmount']."||ResponseCode|".$result->return_code."||ResponseDescription|".$result->return_status."||OrderStatus|FAIL||ApprovalCode|";
	
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='".$result->return_code."', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
	$sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_dmw_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
	echo '2';
	exit;

}
?>
   


