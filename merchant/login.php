<?php 
session_start();
//$_SESSION['ORDER_DETAILS']['loggedIN'].'sfsdfds';
require_once 'database.php';

$error_msg = "";
if (isset ($_REQUEST['name'])) {

	$error_msg = '';
	$name = $_REQUEST['name'];
	$password = $_REQUEST['password'];
	$response = checkUser($name, $password);	
	if($response['validation'] == 'yes'){
		header( 'Location: index.php' );
	}
	else{
		$error_msg = "Invalid Username/Password";
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>..::LOGIN::..</title>
<link href="css/login.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <form name="myForm" id="myForm" method="post" action="">
<div>
	<div id="login-box">
	      <div>
                 <h2><img src="images/shurjoPay.png" border="0" style="none;"></h2>
		  <br />
		  </div>
		  <br />
		  <div style="clear:both;text-align:center;">Welcome to shurjoPay login.</div>
		  <br />
		  <div><?php if(isset($_REQUEST['name'])) echo '<b>'. $response['error_msg'].'</b>';?> </div>
			<div id="login-box-name" style="margin-top:20px;">User Name:</div>
				<div id="login-box-field" style="margin-top:20px;">
				  <input name="name" class="form-login" title="Username" value="" size="30" maxlength="2048" />
				</div>
			<div id="login-box-name">Password:</div>
				<div id="login-box-field">
				  <input name="password" type="password" class="form-login" title="Password" value="" size="30" maxlength="2048" />
				</div>
			<br />
			<!--<span class="login-box-options">
			  <input type="checkbox" name="1" value="1"> Remember Me <a href="#" style="margin-left:30px;">Forgot password?</a>
			</span>-->
		<br />
		<br />
		<br><input type="submit" id="submit" value="Log in" style="margin-left:90px;">
        <a href="forgotpass.php">Forgot your password?</a>
	</div>
</div>
    </form>
</body>
</html>