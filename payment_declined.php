<?php
include ("includes/session_handler.php");
include("includes/configure.php");

if(isset($_GET['cancel']) && $_GET['cancel'] == 'ok')
{

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
        $_SESSION['order_details_response']['txID'] =  $_SESSION['ORDER_DETAILS']['uniqID'];
        $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
        $_SESSION['order_details_response']['bankTxID'] = isset($_REQUEST['trx_id'])?$_REQUEST['trx_id']:'';
        $_SESSION['order_details_response']['bankTxStatus'] = "Declined";
        $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

        $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='001'");
        $result = mysqli_fetch_object($sql_query);

        $_SESSION['order_details_response']['spCode'] = '001';
        $_SESSION['order_details_response']['spCodeDes'] = 'Declined';

        
        $bank_data = 'NULL';
        $sql_query1 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET gw_return_id='001', gw_return_msg='".$result->return_status."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$result->return_code."', bank_status='FAIL', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");
        $sql_query2 = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");
        $sql_query3 = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
        header("Location: ".$db->local_return_url); exit();
} 
else
{
	echo "<div style='width:100%;text-align:center;'><h1>This page requires parameters to access.</h1></div>";
	die();
}