<?php
session_start();
include "api_lib.php";
include "configuration.php";
include "connection.php";
include("../includes/configure.php");
include("../includes/session_handler.php");
include("../includes/ebl.php");
error_reporting(E_ALL);
$errorMessage = "Faie";
$errorCode = "Erron your transasction";
$gatewayCode = "1212";
$result = "Successfull your Transaction";
$responseArray = array();
session_start();
//$var1 = $_POST['text1'];
$var1 = $_SESSION['orderID'];
$_SESSION['uniq_id'] = "";
//echo "$var1";


?>


<?php

$merchantObj = new Merchant($configArray);

$parserObj = new Parser($merchantObj);

$requestUrl = $parserObj->FormRequestUrl($merchantObj);

$request_assoc_array = array("apiOperation" => "RETRIEVE_ORDER",
    "order.id" => $var1
);

$request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);
$response = $parserObj->SendTransaction($merchantObj, $request);

$new_api_lib = new api_lib;
$parsed_array = $new_api_lib->parse_from_nvp($response);

/*echo "<pre>";
print_r($response);

echo "<pre>";
print_r($parsed_array);

echo $parsed_array['sourceOfFunds.provided.card.number'];

echo $parsed_array['response.acquirerCode'];

echo $parsed_array['response.gatewayCode'];
	echo   $parsed_array['id'];*/

if ($parsed_array['status'] == 'CAPTURED' && $parsed_array['result'] == 'SUCCESS') {

    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Asia/Dhaka"));
    $order_id = $_SESSION['ORDER_DETAILS']['order_id'];

    $epay_txid = $parsed_array['id'];
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");

    $ebl = new Ebl();
    $ebl->eblApproved($order_id, $parsed_array, $response);

    header("Location: " . $db->local_return_url);

} else {
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='" . $order_id . "'");
    echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
}
?>
   

   
  	
