<?php
class Qcash {

	public function __construct() {
		//
	}
	public function qcashApproved($order_id) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));

		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] = $_POST['OrderID'];
		
		if ($_POST['OrderStatus'] == "APPROVED"  and isset($_POST['ApprovalCode']) and trim($_POST['ApprovalCode'])!="" and $_POST['ResponseCode']=="000" or $_POST['ResponseCode']=="001") {
			$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='qcash' and return_code='".$_POST['ResponseCode']."'");
			$result = mysqli_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = "000";
			$_SESSION['order_details_response']['spCodeDes'] = "Successful";
		} 
		else {
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='qcash' and return_code='".$_POST['ResponseCode']."'");
			$result = mysqli_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = $result->return_code;
			$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
		}
		
		$_SESSION['order_details_response']['txnAmount'] = $_POST['Amount'] / 100;

		

		$bank_data = "OrderID|".$_POST['OrderID']."||TransactionType|".$_POST['TransactionType']."||Currency|".$_POST['Currency']."||Amount|".$_POST['Amount']."||ResponseCode|".$_POST['ResponseCode']."||ResponseDescription|".$_POST['ResponseDescription']."||OrderStatus|".$_POST['OrderStatus']."||ApprovalCode|".$_POST['ApprovalCode'];

		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$_POST['ResponseCode']."', gw_return_msg='".$_POST['ResponseDescription']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='SUCCESS', bank_response='".$_POST['xmlmsg']."' WHERE order_id='".$order_id."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_POST['ResponseCode']."', epay_status_text='".$_POST['ResponseDescription']."' WHERE tc_txid='".$order_id."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_qcash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

		return true;
	}
	public function qcashCanceled() {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
		$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		
		$DecryptedReceipt = $_POST['xmlmsg'];
		
		if(!(strpos($DecryptedReceipt, '<OrderID>') === false && strpos($DecryptedReceipt, '</OrderID>') === false)){
			$_SESSION['order_details_response']['bankTxID'] = $orderid = substr($DecryptedReceipt, (strpos($DecryptedReceipt, '<OrderID>')+9), (strpos($DecryptedReceipt, '</OrderID>') - (strpos($DecryptedReceipt, '<OrderID>')+9)));
		}
		if(!(strpos($DecryptedReceipt, '<PurchaseAmount>') === false && strpos($DecryptedReceipt, '</PurchaseAmount>') === false)){
			$_SESSION['order_details_response']['txnAmount'] = $amount = (substr($DecryptedReceipt, (strpos($DecryptedReceipt, '<PurchaseAmount>')+15), (strpos($DecryptedReceipt, '</PurchaseAmount>') - (strpos($DecryptedReceipt, '<PurchaseAmount>')+15))))/100;
		}
		if(!(strpos($DecryptedReceipt, '<OrderStatus>') === false && strpos($DecryptedReceipt, '</OrderStatus>') === false)){
			$order_status = substr($DecryptedReceipt, (strpos($DecryptedReceipt, '<OrderStatus>')+13), (strpos($DecryptedReceipt, '</OrderStatus>') - (strpos($DecryptedReceipt, '<OrderStatus>')+13)));
			if($order_status=="CANCELED"){
				$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			}
		}
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='qcash' and return_code='002'");	
		$result= mysqli_fetch_object($sql_query);
		
		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='', gw_return_msg='', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$_POST['xmlmsg']."' WHERE order_id='".$order_id."'"); 
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='', epay_status_text='' WHERE tc_txid='".$order_id."'");
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_qcash_transactions SET transaction_id='".$orderid."', posted_data='".$_POST['xmlmsg']."', transaction_time='".$date->format('Y-m-d H:i:s')."'"); 
		
		return true;		
	}
	public function qcashDeclined() {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
		$orderid = $_SESSION['ORDER_DETAILS']['order_id'];
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		
		$_SESSION['order_details_response']['bankTxID'] = $_POST['OrderID'];
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		$_SESSION['order_details_response']['txnAmount'] = $_POST['Amount']/100;
			
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='qcash' and return_code='".$_POST['ResponseCode']."'");	
		$result= mysqli_fetch_object($sql_query);
		
		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
		
		$bank_data = "OrderID|".$_POST['OrderID']."||TransactionType|".$_POST['TransactionType']."||Currency|".$_POST['Currency']."||Amount|".$_POST['Amount']."||ResponseCode|".$_POST['ResponseCode']."||ResponseDescription|".$_POST['ResponseDescription']."||OrderStatus|".$_POST['OrderStatus']."||ApprovalCode|".$_POST['ApprovalCode'];
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$_POST['ResponseCode']."', gw_return_msg='".$_POST['ResponseDescription']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$_POST['xmlmsg']."' WHERE order_id='".$orderid."'"); 
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$_POST['ResponseCode']."', epay_status_text='".$_POST['ResponseDescription']."' WHERE tc_txid='".$orderid."'");
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_qcash_transactions SET transaction_id='".$orderid."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'"); 
		
		return true;	
	}
}
?>

