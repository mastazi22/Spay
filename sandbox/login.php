<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
session_start();
//$_SESSION['ORDER_DETAILS']['loggedIN'].'sfsdfds';
require_once 'database.php';
require_once 'includes/configure.php';
echo "1";
if (isset ($_REQUEST['name'])) {
	$error_msg = '';
	$name = $_REQUEST['name'];
	$password = $_REQUEST['password'];

	$response = checkUser($name, $password);

	if ($response['validation'] === 'yes') {
?>
<script language="javascript">
window.location.href='navigation.php';
</script>
<?php

	}

}
?>

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
                  <div id="logo-img"><img src="images/sand-box-logo.jpg" /></div>
                  <div id="logo_img_right"><div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay" width="150"></div></div>
		  <br />
		  </div>
		  <br />
		  <div style="clear:both;text-align:center;">Welcome to Shurjopay SandBox login.</div>
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
			<span class="login-box-options">
			  <input type="checkbox" name="1" value="1"> Remember Me <a href="#" style="margin-left:30px;">Forgot password?</a>
			</span>
		<br />
		<br />
		<input type="submit" name="submit" id="submit" value="Submit" src="images/btn.png" width="103" height="42" style="margin-left:90px;">
	</div>
</div>
    </form>
</body>
</html>

