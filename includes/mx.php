<?php
class Mx {

	public function __construct() {
		//
	}
	public function mxApproved($order_id,$data,$rawdata) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));

		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] = $data['OrderID'];
		
		if ($data['OrderStatus'] == "APPROVED"  and isset($data['ApprovalCode']) and trim($data['ApprovalCode'])!="" and $data['ResponseCode']=="000" or $data['ResponseCode']=="001") {
			$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='mx' and return_code='".$data['ResponseCode']."'");
			$result = mysqli_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = "000";
			$_SESSION['order_details_response']['spCodeDes'] = "Successful";
			$return_code="000";
		} 
		else {
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='mx' and return_code='".$data['ResponseCode']."'");
			$result = mysqli_fetch_object($sql_query);
	
			$_SESSION['order_details_response']['spCode'] = $result->return_code;
			$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
			$return_code=$data['ResponseCode'];
		}
		
		$_SESSION['order_details_response']['txnAmount'] = $data['PurchaseAmount'] / 100;
		$bank_data = "OrderID|".$data['OrderID']."||TransactionType|".$data['TransactionType']."||Currency|".$data['Currency']."||Amount|".$data['PurchaseAmount']."||ResponseCode|".$data['ResponseCode']."||ResponseDescription|".$data['OrderStatusScr']."||OrderStatus|".$data['OrderStatus']."||ApprovalCode|".$data['ApprovalCode'];

		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$return_code."', gw_return_msg='".$data['OrderStatus']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$return_code."',card_holder_name='".$data['Name']."',card_number='".$data['PAN']."', bank_status='SUCCESS', bank_response='".$rawdata."' WHERE order_id='".$order_id."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$return_code."', epay_status_text='".$data['OrderStatus']."' WHERE tc_txid='".$order_id."'");
		//$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_mx_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_mx_transactions SET posted_data='".$bank_data."' WHERE transaction_id ='".$order_id."'");

		return true;
	}
	public function mxCanceled($data,$rawdata) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
		$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		
		
			$_SESSION['order_details_response']['bankTxID'] = $data['OrderID'];
			$_SESSION['order_details_response']['txnAmount'] = $data['PurchaseAmount'] / 100;
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='mx' and return_code='002'");	
		$result= mysqli_fetch_object($sql_query);
		
		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='', gw_return_msg='', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$rawdata."' WHERE order_id='".$order_id."'"); 
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='', epay_status_text='' WHERE tc_txid='".$order_id."'");
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_mx_transactions SET transaction_id='".$order_id."', posted_data='".$rawdata."', transaction_time='".$date->format('Y-m-d H:i:s')."'"); 
		
		return true;		
	}
	public function mxDeclined($data,$rawdata) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
		$orderid = $_SESSION['ORDER_DETAILS']['order_id'];
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		
		$_SESSION['order_details_response']['bankTxID'] = $data['OrderID'];
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		$_SESSION['order_details_response']['txnAmount'] = $data['PurchaseAmount']/100;
			
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_bankinfo WHERE gw_name='mx' and return_code='".$data['ResponseCode']."'");	
		$result= mysqli_fetch_object($sql_query);
		
		$_SESSION['order_details_response']['spCode'] = $result->return_code;
		$_SESSION['order_details_response']['spCodeDes'] = $result->return_status;
		
		$bank_data = "OrderID|".$data['OrderID']."||TransactionType|".$data['TransactionType']."||Currency|".$data['Currency']."||Amount|".$data['PurchaseAmount']."||ResponseCode|".$data['ResponseCode']."||ResponseDescription|".$data['ResponseDescription']."||OrderStatus|".$data['OrderStatus']."||ApprovalCode|".$data['ApprovalCode'];
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$data['ResponseCode']."', gw_return_msg='".$data['OrderStatus']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."',card_holder_name='".$data['Name']."',card_number='".$data['PAN']."', bank_status='FAIL', bank_response='".$rawdata."' WHERE order_id='".$orderid."'"); 
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$data['ResponseCode']."', epay_status_text='".$data['OrderStatus']."' WHERE tc_txid='".$orderid."'");
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_mx_transactions SET transaction_id='".$orderid."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'"); 
		
		return true;	
	}
}
?>

