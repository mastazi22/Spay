<?php
session_start();
if ($_SESSION['valid_user'] != 'yes') {
    header('Location: sp-data.php');
}
if($_POST['back']){
	header('Location: sp-data.php?back=1');
}
?>
<html>
    <head>
        <link href="./css/layout.css" rel="stylesheet" type="text/css">
        <link href="./css/text.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
        include("includes/configure.php");

        $cardNumber = $_POST['cardNumber'];
        $cvcNumber = $_POST['cvcNumber'];
        $expMonth = $_POST['expMonth'];
        $expYear = $_POST['expYear'];
        $cardHolderName = $_POST['cardHolderName'];
        $cardType = $_SESSION['ORDER_DETAILS']['cardType'];
        ?>



        <div id="container">
            <div id="header">
                <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
                <div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
            </div>
            <div id="main_container">
<?php
$errorCode = $_SESSION['ORDER_DETAILS']['errCode'];
$tx_id = $_SESSION['ORDER_DETAILS']['uniqID'];
$bank_tx_id = $_SESSION['ORDER_DETAILS']['txnRefNum'];
$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Dhaka'));
$current_time = $date->format("Y-m-d H:i:s");
$query_sql = "SELECT *  from sp_bankinfo where id='$errorCode' ";


$res = mysqli_query($GLOBALS["___mysqli_sm"], $query_sql);
$data = mysql_fetch_object($res);
if (mysql_num_rows($res) > 0) {
    $gw_return_id = $data->tx_code;
    $gw_return_msg = $data->tx_status;
    $gateway = $data->gw_name;
    $return_code = $data->return_code;
    $return_msg = $data->return_status;
	$txnAmount=$_SESSION['ORDER_DETAILS']['txnAmount'];
    $remarks = "TX ID : " . $tx_id . "<br>Return Code : " . $return_code . "<br>Return Msg : " . $return_msg . "<br>Bank Ref. Num :  " . $bank_tx_id;
    $sql_query = mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET  method='" . $cardType . "' , bank_tx_id='" . $bank_tx_id . "', gw_time='" . $current_time . "', gw_return_id='" . $gw_return_id . "', gw_return_msg='" . $gw_return_msg . "', gateway='" . $gateway . "' , return_code='" . $return_code . "',remarks='" . $remarks . "' where txid='" . $tx_id . "'");
}
if($return_code=="000"){
	$bk_status = "SUCCESS";
}
else{
	$bk_status = "FAIL";
}
$post_data = '&lt;?xml version="1.0" encoding="utf-8"?&gt;
	            &lt;spResponse&gt;&lt;txID&gt;'.$tx_id.'&lt;/txID&gt;
	            &lt;bankTxID&gt;'.$bank_tx_id.'&lt;/bankTxID&gt;
	            &lt;bankTxStatus&gt;'.$bk_status.'&lt;/bankTxStatus&gt;
	            &lt;txnAmount&gt;'.$txnAmount.'&lt;/txnAmount&gt;
	            &lt;spCode&gt;'.$return_code.'&lt;/spCode&gt;
	            &lt;spCodeDes&gt;'.$return_msg.'&lt;/spCodeDes&gt;
	            &lt;paymentOption&gt;'.$cardType.'&lt;/paymentOption&gt;&lt;/spResponse&gt;';
	    
?>
                <form method="post" action="<?php echo $_SESSION['ORDER_DETAILS']['returnURL']; ?>" id="frm_submit">
                    <input type="hidden" name="spdata" value='<?php echo $post_data; ?>'>
                </form>
                <script>
                    document.getElementById('frm_submit').submit();
                </script>


            </div>
            <div id="footer"></div>
        </div>
        <div id="shadow"></div>


        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="-1">

    </body>
</html>