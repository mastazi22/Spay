<?php

	require_once '../includes/configure.php';
	class shurjoPayApi 
	{

		private $token  = '6dz8TQPM5V4W';

		function shurjoPayPaymentDetails($token,$bank_tx_id) 
		{
			
			if($token  == $this->token) 
			{

				$bank_tx_id = mysqli_real_escape_string($GLOBALS["___mysqli_sm"], (string) $bank_tx_id);
				$bank_tx_id = trim($bank_tx_id);
				$query = "select * from sp_epay where bank_tx_id='".$bank_tx_id."' ORDER BY id DESC Limit 0, 1";
				$res   = mysqli_query($GLOBALS["___mysqli_sm"],$query);
				$data  = mysqli_fetch_object($res);				
				try {
				    header("Content-type: application/json; charset=utf-8");	
				    echo json_encode($data);	

				} catch(Exception $e) {	
				    echo $e->getMessage();
				}
			}
			else 
			{
				$response = array('StatusCode' => NULL ,'StatusTxt'=>'Unauthorized Access Denied');
				echo json_encode($response);			
			}

		}

		function shurjoPayPaymentDetailsByTxId($token,$tx_id) 
		{

			if($token  == $this->token) 
			{

				$tx_id = trim($tx_id);
				$query = "select * from sp_epay where txid='".$tx_id."' ORDER BY id DESC Limit 0, 1";
				$res   = mysqli_query($GLOBALS["___mysqli_sm"],$query);
				$data  = mysqli_fetch_object($res);			

				try {
				    header("Content-type: application/json; charset=utf-8");	
				    echo json_encode($data);	

				} catch(Exception $e) {	
				    echo $e->getMessage();
				}
			}
			else 
			{
				$response = array('StatusCode' => NULL ,'StatusTxt'=>'Unauthorized Access Denied');
				echo json_encode($response);			
			}
		}

	}

	$api = new shurjoPayApi();

	$token      = isset($_POST['token'])?$_POST['token']:'';
	$bank_tx_id = isset($_POST['bank_tx_id'])?$_POST['bank_tx_id']:''; 
	$tx_id      = isset($_POST['tx_id'])?$_POST['tx_id']:'';

	if(isset($bank_tx_id) && $bank_tx_id != '') 
	{
		$api->shurjoPayPaymentDetails($token,$bank_tx_id);
	}
	elseif(isset($tx_id) && $tx_id != '')
	{
		$api->shurjoPayPaymentDetailsByTxId($token,$tx_id);
	}


?>