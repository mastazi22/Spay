<?php

	session_start();	
	require_once '../includes/configure.php';
	require_once('dbbl-verification-engine.php');	
	
	$txid = $_REQUEST['txid'];
	
	// Get details of the transaction
	$query = "SELECT amount,bank_status,clientip,bank_tx_id FROM `sp_epay`  WHERE txid = '$txid'";
	$res = mysqli_query($GLOBALS["___mysqli_sm"],$query);
	$epay_data = mysqli_fetch_object($res);
	$res = '';
	
	
	
	//var_dump($epay_data);exit();
	if(isset($_REQUEST['bank_tx_id']) && !empty($_REQUEST['bank_tx_id'])) {
		$data = (object) dbbl_verification( $_REQUEST['clientip'],$_REQUEST['bank_tx_id']);		
		if($data->status == 'OK') {
			
			$query = "UPDATE sp_epay 
			set gw_return_id = '000', 
			gw_return_msg = 'Approved',
			return_code = '000', 
			bank_status = 'SUCCESS', 
			card_holder_name = '$data->cardNumber', 
			card_number = '$data->cardHolderName' 
			WHERE  txid = '$txid' ";
			
			$res = mysqli_query($GLOBALS["___mysqli_sm"],$query);
		
			// Notify Anser 
			$notify_data = array(     
				'txID' => $txid,  
				'bankTxID' => $epay_data->bank_tx_id,      
				'bankTxStatus' => 'SUCCESS',
				'txnAmount' => $epay_data->amount,
				'spCode' => '000',
				'spCodeDes' => 'Success',
				'paymentOption' => 'dbbl'
			);	
			
			
		} else if ($data->status == 'FAILED') {
			
			$query = "UPDATE sp_epay 
			set gw_return_id = 'NULL', 
			gw_return_msg = 'NULL',
			return_code = '001', 
			bank_status = 'FAILED', 
			card_holder_name = '$data->cardNumber', 
			card_number = '$data->cardHolderName' 
			WHERE  txid = '$txid' ";
			
			$res = mysqli_query($GLOBALS["___mysqli_sm"],$query);


			// Notify Anser 
			$notify_data = array(     
				'txID' => $txid,  
				'bankTxID' => $epay_data->bank_tx_id,      
				'bankTxStatus' => 'FAILED',
				'txnAmount' => $epay_data->amount,
				'spCode' => '001',
				'spCodeDes' => 'Failed',
				'paymentOption' => 'dbbl'
			);

			
		} else if( $data->status == 'TIMEOUT') {

			$query = "UPDATE sp_epay 
			set gw_return_id = 'NULL', 
			gw_return_msg = 'NULL',
			return_code = '001', 
			bank_status = 'TIMEOUT', 
			card_holder_name = '$data->cardNumber', 
			card_number = '$data->cardHolderName' 
			WHERE  txid = '$txid' ";
			
			$res = mysqli_query($GLOBALS["___mysqli_sm"],$query);

			// Notify Anser 
			$notify_data = array(     
				'txID' => $txid,  
				'bankTxID' => $epay_data->bank_tx_id,      
				'bankTxStatus' => 'TIMEOUT',
				'txnAmount' => $epay_data->amount,
				'spCode' => '001',
				'spCodeDes' => 'Failed',
				'paymentOption' => 'dbbl'
			);

		} 


		// Send notification anser
		if(strstr($txid,'VDP'))
		{
			$res = notify_anser($notify_data);
			$_SESSION['anser_update'] = $res;
			
		}
		
		header('Location: '.$_SERVER['REQUEST_URI']);
		
		
	}
	
?>


<html>
    <head>
		<title>ShurjoPay Dashboard</title>
		<link rel="stylesheet" media="screen" href="css/style.css" media="all" />
    </head>
	
    <body>
		<table style='width:50%;'>
			<tr><th><h3>DBBL Verification</h3></th></tr>		
		</table> 
		<table style="width:50%">
			<form action="" method="post">
			<input type="hidden" name="bank_tx_id" value="<?php echo $epay_data->bank_tx_id; ?>">
			<input type="hidden" name="clientip" value="<?php echo $epay_data->clientip; ?>">			
			<tr><td>Status::</td><td><?php echo $epay_data->bank_status; ?></td></tr>
			<tr><td>Bank Tx ID::</td><td><?php echo $epay_data->bank_tx_id; ?></td></tr>
			<?php if(strstr($txid,'VDP')):?>
			<tr><td>Anser Response::</td><td><?php echo $_SESSION['anser_update']." ".$res;  unset($_SESSION['anser_update']);?></td></tr>
			<?php endif;?>
			<tr><td><input type="submit" value="verify"></td><td><a href="#" onClick="window.close();">Close</a></td></tr>
			</form>
		</table>
	</body>
</html>	
