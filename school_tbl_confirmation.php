<?php
	include ('includes/session_handler.php');
	include ("includes/configure.php");	
	
	$orderID 	= $_SESSION['ORDER_DETAILS']['order_id'];
	$amount	 	= $_SESSION['ORDER_DETAILS']['txnAmount'];
	$studentID  = $_SESSION['ORDER_DETAILS']['studentID'];       
	$marchantID	= 'BISC';        
	$returnURL	= 'https://shurjopay.com/school_tbl_return.php';
	//$host 		= 'https://ibanking.tblbd.com/TestCheckout/Checkout_Payment.aspx';
	// For SSL certificate expired	
	$host = 'https://ibanking.tblbd.com/Checkout/Checkout_Payment.aspx';
	$ch = curl_init($host);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
	curl_close($ch);
	if($httpcode >= 200 && $httpcode < 300) {
	?>
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<title>Trust Bank Limited</title>
				<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
			</head>
			<body>		
				<form id="submit" action="<?php echo $host; ?>" method="POST">
					<input type="hidden" name="OrderID" value="<?php echo $orderID; ?>">
					<input type="hidden" name="FullName" value="<?php echo $studentID; ?>">
					<input type="hidden" name="Amount" value="<?php echo $amount; ?>">
					<input type="hidden" name="MerchantID" value="<?php echo $marchantID; ?>">
					<input type="hidden" name="PaymentSuccessUrl" value="<?php echo $returnURL; ?>">
				</form>
				<script>
					$('#submit').submit();
				</script>
			</body>
		</html>
	<?php
	} else {
		die('Trust Payment Server Down.');
	}
?>

