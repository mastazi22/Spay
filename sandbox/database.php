<?php

//$database=mysql_connect('localhost','root','');
//if (!$database)
// {
// die('Could not connect: ' . mysql_error());
//}
require_once 'includes/configure.php';
//mysql_select_db('sandbox',$database) or die();

function checkUser($name, $password) {

	$sql = "select * from sp_merchants where username='{$name}' and password='{$password}'";
	$sql1 = mysqli_query($GLOBALS["___mysqli_sm"], $sql);
	$sql2 = mysql_fetch_row($sql1);
	$error_msg = '';

	if (empty ($sql2)) {
		$err = "select * from sp_bankinfo where id='199'";
		$err1 = mysqli_query($GLOBALS["___mysqli_sm"], $err);
		$error_code = mysql_fetch_row($err1);
		$response['error_msg'] = $error_code[5];
		$response['validation'] = 'no';
		$_SESSION['ORDER_DETAILS']['userID'] = "";
		return $response;
	} 
	else {
		if ($sql2[4] == 'no') {
			$err = "select * from sp_bankinfo where id='200'";
			$err1 = mysqli_query($GLOBALS["___mysqli_sm"], $err);
			$error_code = mysql_fetch_row($err1);
			$response['error_msg'] = $error_code[5];
			$response['validation'] = 'no';
			$_SESSION['ORDER_DETAILS']['userID'] = "";
			return $response;
		} 
		else {
			$_SESSION['ORDER_DETAILS']['userID'] = $sql2[0];
			$_SESSION['ORDER_DETAILS']['loggedIN'] = true;
			$response['error_msg'] = '';
			$response['validation'] = 'yes';

			return $response;

		}
	}
}
?>

