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

$date = new DateTime();
$date->setTimezone(new DateTimeZone("Asia/Dhaka"));
$order_id = $_SESSION['ORDER_DETAILS']['order_id'];

$epay_txid = $var1;
if($epay_txid !=="")
{
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_txid='" . $epay_txid . "' WHERE tc_txid='" . $order_id . "'");
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_tx_id='" . $epay_txid . "' WHERE order_id='" . $order_id . "'");
    $ebl = new Ebl();
    $ebl->eblCanceled($epay_txid);

    header("Location: " . $db->local_return_url);
}else{
    exit("Contact With shurjoPay Support Team");
}



?>
