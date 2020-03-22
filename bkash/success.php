<?php
    include ("../includes/configure.php");
    include ('../includes/session_handler.php');

    if(!isset($_SESSION['ORDER_DETAILS']['order_id']) || empty($_SESSION['ORDER_DETAILS']['order_id']) )
    {
      header("Location: https://shurjopay.com/halt.php");
        exit();
    } 

    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('Asia/Dhaka'));

    $order_id  = $_SESSION['ORDER_DETAILS']['order_id'];   
    $resultdatax = $_SESSION['executeResponse']; 
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_response='".$resultdatax."' WHERE order_id='".$order_id."'");      
    $response = $_SESSION['executeResponse'];


  if($response)
  {
    $order_id = $_SESSION['ORDER_DETAILS']['order_id'];
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * FROM sp_epay WHERE order_id='".$order_id."'");
    $result = mysqli_fetch_object($sql_query);

    $responseData = json_decode($response);


        /** Already Exists checking  **/
    if($result->bank_tx_id == $responseData->trxID && $result->bank_status == 'SUCCESS')
    {

      $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
      $_SESSION['order_details_response']['bankTxID'] = $responseData->trxID;
      $_SESSION['order_details_response']['bankTxStatus'] = "FAIL";
      $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];
      $_SESSION['order_details_response']['spCode'] = '001';
      $_SESSION['order_details_response']['spCodeDes'] = "The transaction id has already been used.";

      header("Location: ".$db->local_return_url); exit();
    }

    /***************/

    if(isset($responseData->transactionStatus) and $responseData->transactionStatus=='Completed' and $responseData->amount==$_SESSION['ORDER_DETAILS']['txnAmount'] and $responseData->amount==$result->amount)
    {

      $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];      
      $_SESSION['order_details_response']['bankTxID'] = $responseData->trxID;
      $_SESSION['order_details_response']['bankTxStatus'] = "SUCCESS";
      $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];      
      $_SESSION['PSS'] = true;

      $sql2_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='000'");
      $result2 = mysqli_fetch_object($sql2_query);

      $_SESSION['order_details_response']['spCode'] = $result2->return_code;
      $_SESSION['order_details_response']['spCodeDes'] = $result2->return_status;//?$result2->return_status:'Success';

      
      $bank_data = json_encode($response);
      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id = '".$_SESSION['order_details_response']['bankTxID']."', gw_return_id='".$_SESSION['order_details_response']['spCode']."', gw_return_msg='".$_SESSION['order_details_response']['spCodeDes']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$_SESSION['order_details_response']['spCode']."', bank_status='".$_SESSION['order_details_response']['bankTxStatus']."', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");

      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result2->return_code."', epay_status_text='".$result2->return_status."' WHERE tc_txid='".$order_id."'");

      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");

      header("Location: ".$db->local_return_url); exit();

    }
    else
    {
      $_SESSION['order_details_response']['txID'] = $_SESSION['ORDER_DETAILS']['uniqID'];
      $_SESSION['order_details_response']['bankTxID'] = $responseData->trxID;
      $_SESSION['order_details_response']['bankTxStatus'] = $responseData->errorMessage;//"FAIL";
      $_SESSION['order_details_response']['txnAmount'] = $_SESSION['ORDER_DETAILS']['txnAmount'];

      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_bankinfo WHERE gw_name='bkash' and return_code='001'");
      $result = mysqli_fetch_object($sql_query);

      $_SESSION['order_details_response']['spCode'] = $result->return_code;
      $_SESSION['order_details_response']['spCodeDes'] = $result->return_status;


      
      $bank_data = json_encode($response);
      
      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET bank_tx_id = '".$_SESSION['order_details_response']['bankTxID']."', gw_return_id='".$_SESSION['order_details_response']['spCode']."', gw_return_msg='".$_SESSION['order_details_response']['spCodeDes']."', gw_time='".$date->format('Y-m-d H:i:s')."', return_code='".$_SESSION['order_details_response']['spCode']."', bank_status='".$_SESSION['order_details_response']['bankTxStatus']."', bank_response='".$bank_data."' WHERE order_id='".$order_id."'");

      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_payments SET epay_res_time='".$date->format('Y-m-d H:i:s')."', epay_status='".$result->return_code."', epay_status_text='".$result->return_status."' WHERE tc_txid='".$order_id."'");

      $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "INSERT INTO sp_bkash_transactions SET transaction_id='".$order_id."', posted_data='".$bank_data."', transaction_time='".$date->format('Y-m-d H:i:s')."'");
     
      $_SESSION['bkash_message'] = 'Transaction ID Incorrect. Please Try Again. ';          

      header("Location: ".$db->local_return_url); exit();
    }
   
  }



?>