<?php
require_once '../includes/configure.php';

function checkUser($name, $password) {

	$pass = md5($password);
	$sql = "select * from sp_merchants where username='{$name}' and password='$pass'";	
	$sql1 = mysqli_query($GLOBALS["___mysqli_sm"],$sql);
	$sql2 = mysqli_fetch_row($sql1);
	

	$error_msg = '';

	if (empty ($sql2)) {
		$err = "select *from sp_bankinfo where id='199'";
		$err1 = mysqli_query($GLOBALS["___mysqli_sm"],$err);
		$error_code = mysqli_fetch_row($err1);
		$response['error_msg'] = $error_code[5];
		$response['validation'] = 'no';
		$_SESSION['ORDER_DETAILS']['userID'] = "";
		return $response;

	} 
	else {
		if ($sql2[6] == 'no') {
			$err = "select *from sp_bankinfo where id='200'";
			$err1 = mysqli_query($GLOBALS["___mysqli_sm"],$err);
			$error_code = mysqli_fetch_row($err1);
			$response['error_msg'] = $error_code[5];
			$response['validation'] = 'no';
			$_SESSION['ORDER_DETAILS']['userID'] = "";
			return $response;
		} 
		else {
			$_SESSION['ORDER_DETAILS']['userID'] = $sql2[0];
			$_SESSION['ORDER_DETAILS']['userName'] = $sql2[1];
			$_SESSION['ORDER_DETAILS']['loggedIN'] = true;
			$_SESSION['ORDER_DETAILS']['Ucode'] = $sql2[6];
			$response['error_msg'] = '';
			$response['validation'] = 'yes';

			return $response;

		}
	}
}


