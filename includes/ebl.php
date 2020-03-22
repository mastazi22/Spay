<?php
class Ebl {

	public function __construct() {
		//
	}
	public function eblApproved($order_id,$data,$rawdata) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));

		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		$_SESSION['order_details_response']['bankTxID'] = $data['id'];
		
		
		if ($data['status'] == 'CAPTURED' && $data['result'] == 'SUCCESS') {
			$_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
			$_SESSION['order_details_response']['spCode'] = "000";
			$_SESSION['order_details_response']['spCodeDes'] = "Successful";
			$return_code="000";
		} 
		else {
			$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
			$_SESSION['order_details_response']['spCode'] = "002";
			$_SESSION['order_details_response']['spCodeDes'] = "FAIL";
			$return_code="002";
		}
		
		$_SESSION['order_details_response']['txnAmount'] = $data['amount'];
		$bank_data = "OrderID|".$data['id']."||TransactionType|".$data['sourceOfFunds.provided.card.brand']."||Currency|".$data['currency']."||Amount|".$data['amount']."||ResponseCode|"."000"."||ResponseDescription|".$data['result']."||OrderStatus|".$data['status']."||ApprovalCode|"."000";
        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='".$return_code."', gw_return_msg='".$data['status']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$return_code."', bank_status='SUCCESS',card_holder_name='".$data['sourceOfFunds.provided.card.nameOnCard']."',card_number='".$data['sourceOfFunds.provided.card.number']."', bank_response='".$rawdata."' WHERE order_id='".$order_id."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$return_code."', epay_status_text='".$data['status']."' WHERE tc_txid='".$order_id."'");
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_ebl_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

		return true;
	}
	public function eblCanceled($bankTxID) {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
		
		$order_id = $_SESSION['ORDER_DETAILS']['order_id'];
		$_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
		
		$_SESSION['order_details_response']['bankTxID'] = $bankTxID;
		$_SESSION['order_details_response']['txnAmount'] = "";
		$_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
		
		
		
		$_SESSION['order_details_response']['spCode'] = "002";
		$_SESSION['order_details_response']['spCodeDes'] =  "FAIL";
		
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET gw_return_id='', gw_return_msg='', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='002', bank_status='FAIL', bank_response='' WHERE order_id='".$order_id."'"); 
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='', epay_status_text='' WHERE tc_txid='".$order_id."'");
		$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"INSERT INTO sp_ebl_transactions SET transaction_id='".$order_id."', posted_data='', transaction_time='".$date->format('Y-m-d H:i:s')."'"); 
		
		return true;		
	}
	
}


