<?php

	include("includes/configure.php");

	function jsonOutput($data) {
		header('Content-type: text/json');
		header('Content-type: application/json');
		return str_replace('\/', '/', json_encode($data));	
	}

	$response	= FALSE;
	$data		= NULL;

	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		$smUid		= (int) filter_var($_POST['smUid'], FILTER_SANITIZE_NUMBER_INT);
		$spayId		= (string) filter_var($_POST['spayId'], FILTER_SANITIZE_STRING);
		if(strlen($smUid) <= 12 && strlen($spayId) <= 30) {
			$smUid		= mysql_real_escape_string($smUid);
			$spayId		= mysql_real_escape_string($spayId);
			$sql = mysqli_query($GLOBALS["___mysqli_sm"], "SELECT
				`id`,
				`order_id`,
				`txid`,
				`amount`,
				`method`,
				`clientip`,
				`bank_tx_id`,
				`gw_return_id`,
				`bank_status`
			FROM `sp_epay` WHERE `txid` = '$spayId' AND `smUid` = $smUid");
			$row = mysql_num_rows($sql);
			if($row == 1) {
				$response	= TRUE;
				$data		= mysql_fetch_object($sql);
			} else {
				$response	= FALSE;
				$data		= "Sorry! Data not found.";
			}			
		} else {
			$response	= FALSE;
			$data		= "Sorry! Invalid Request.";
		}
	} else {
		$response	= FALSE;
		$data		= "Sorry! Authentication problem.";
	}

	$output = array(
		"response"	=> $response,
		"data"		=> $data
	);

	echo jsonOutput($output);

?>
