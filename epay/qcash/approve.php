<?php
include("../../includes/configure.php");
include ("../../includes/session_handler.php");
include ("../../includes/qcash.php");

$date = new DateTime();
$date->setTimezone(new DateTimeZone("Asia/Dhaka"));

$order_id = $_SESSION['ORDER_DETAILS']['order_id'];

if (isset($_GET['ORDERID']) and $_GET['ORDERID']!=""){
	$epay_txid = $_GET['ORDERID'];
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_txid='".$epay_txid."' WHERE tc_txid='".$order_id."'");
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_tx_id='".$epay_txid."' WHERE order_id='".$order_id."'"); 
?>
	<form method="post" name="qcash" action=<?php echo "'https://mpi.itcbd.com:18288/index.jsp?ORDERID=" . $_GET['ORDERID'] . "&SESSIONID=" . $_GET['SESSIONID'] . "'"; ?> >
	</form>
	<script>
		function qcashSubmit(){
			 document.forms["qcash"].submit();
		}
		qcashSubmit();
	</script>
<?php
}
else if (!isset($_GET['ORDERID'])){
	$qcash = new Qcash();
	$qcash->qcashApproved($order_id);	
	
	header("Location: ".$db->local_return_url);
}
else{
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_payments SET epay_status='999', epay_status_text='Unable to process the transaction. epay_txid not generated' WHERE tc_txid='".$order_id."'");
	echo '<div style="color:#ff0000;padding: 10px;">We are unable to provide the requested service as payment processor (bank) is unavailable. Please try again by clicking "Confirm and place the order". If you face this problem again please contact us or try later. We are sorry for the inconvenience</div>';
}
?>
