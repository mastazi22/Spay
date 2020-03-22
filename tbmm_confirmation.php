<?php

	print_r($_POST); die;

	//include ("includes/configure.php");
	//include ('includes/session_handler.php');
	//include ("includes/header_bkash.php");
	
	//$orderID 	= $_SESSION['ORDER_DETAILS']['order_id'];
	//$amount	 	= $_SESSION['ORDER_DETAILS']['txnAmount'];
	/*
	$marchantID	= 'SHURJOMUKHI';
	$returnURL	= 'https://shurjopay.com/tbmm_confirmation.php';
	$host 		= 'https://ibanking.tblbd.com/TestCheckout/Checkout_Payment.aspx';
	$checkURL 	= 'https://ibanking.tblbd.com/TestCheckout/Checkout_Payment_Verify.asmx?WSDL';

	$getOrderId		= isset($_POST['orderid']) ? $_POST['orderid'] : FALSE;
	$getPaidAmount	= isset($_POST['amount']) ? $_POST['amount'] : FALSE;
	$getRefId		= isset($_POST['refid']) ? $_POST['refid'] : FALSE;
	$getStatus		= isset($_POST['status']) ? $_POST['status'] : 0;
	$getBank		= isset($_POST['bank']) ? $_POST['bank'] : FALSE;
	if($getOrderId != FALSE && $getPaidAmount != FALSE && $getRefId != FALSE && $getStatus != 0 && $getBank != FALSE) {
		try {
			$TBLC_VerificationClient = new SoapClient($checkURL);
			$TBLC_VerificationParams = array(
				"OrderID" => $getOrderId,
				"RefID"   => $getPaidAmount,
				"Amount"  => $getRefId
			);
			$TBLC_VerificationResult = $TBLC_VerificationClient
				->Transaction_Verify($TBLC_VerificationParams)
				->Transaction_VerifyResult;

			if($TBLC_VerificationResult == '1') {
				echo 'ok';
			} else {
				echo 'Invalid';
			}
			die;
		}
		catch(Exception $e) {
			echo 'Connection error.';
			die;
		}
		
	} else {
		$ch = curl_init($host);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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
					<input type="hidden" name="OrderID" value="<?php //echo $orderID; ?>">
					<input type="hidden" name="Amount" value="<?php //echo $amount; ?>">
					<input type="hidden" name="MerchantID" value="<?php //echo $marchantID; ?>">
					<input type="hidden" name="PaymentSuccessUrl" value="<?php //echo $returnURL; ?>">
				</form>
				<script>
					$('#submit').submit();
				</script>
			</body>
		</html>
	<?php
		} else {
			die('Trust Bank Moble Money Server Down.');
		}
	}
	
	*/
?>