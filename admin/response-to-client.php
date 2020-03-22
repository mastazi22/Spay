<?php
	session_start();
	error_reporting(E_ALL);
	include("../includes/configure.php");
	require_once 'database.php';
	if(!$_SESSION['ORDER_DETAILS']['loggedINAdmin']) {    
	 	header('Location: login.php');
	}

	require_once('lib/ORM.php');
	$db = new ORM($GLOBALS["___mysqli_sm"]);
	// get transaction id && get payment details
	$txid = mysqli_real_escape_string($GLOBALS["___mysqli_sm"],$_GET['txid']);
	$result = $db->getPaymentDetails($txid);	
	if(!is_null($result))
	{
		if($result->bank_status == 'SUCCESS')
		{
			$spCode = '000';
			$spCodeDes = 'Successful';
		}
		
		spResponse($result->txid, $result->bank_tx_id, $result->bank_status, $result->amount, $spCode, $spCodeDes, $result->method, $result->returnurl);
		exit;
	}
	
	// sent response



	function spResponse($txID, $bankTxID, $bankTxStatus, $txnAmount, $spCode, $spCodeDes, $paymentOption, $returnURL)
	{


		$post_data = '<?xml version="1.0" encoding="utf-8"?>
		            <spResponse><txID>'.$txID.'</txID>
		            <bankTxID>'.$bankTxID.'</bankTxID>
		            <bankTxStatus>'.$bankTxStatus.'</bankTxStatus>
		            <txnAmount>'.$txnAmount.'</txnAmount>
		            <spCode>'.$spCode.'</spCode>
		            <spCodeDes>'.$spCodeDes.'</spCodeDes>
		            <paymentOption>'.$paymentOption.'</paymentOption></spResponse>';

		$data = json_encode(array ('spay_data' => $post_data));
		$post_data = base64_encode(get_encrypted_data($data, file_get_contents('/etc/sp_key/public.pem')));

		echo $return = "<form method='post' action='$returnURL' id='frm_submit'>
				<input type='hidden' name='spdata' value='$post_data'>
				</form>
			<script>
				document.getElementById('frm_submit').submit();
			</script>";
	}


	function get_encrypted_data($msg = '', $public_key = '') 
	{
		if ($msg == '')
			return false;
		$publicKeys[] = openssl_get_publickey($public_key);
		$res = openssl_seal($msg, $encryptedText, $encryptedKeys, $publicKeys);
		$data = json_encode(array ("msg" => base64_encode($encryptedText), 'key' => base64_encode($encryptedKeys[0])));
		return $data;
	}

?>	