<?php
if(isset($_POST['forgotpassword'])){
	$frm = "support@paypoint.com.bd";
	$sub = "Password change request of shurjoPay";
	$ip = isset ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	$body = "Dear Concern,<br><br>".$_POST['name']." is requesting for changing password of admin panel. Please check necessary steps if it is valid request from brac bank. <br>The requested IP is ::".$ip."<br><br>Regards,<br>paypoint.com.bd";
	$to = "zahid@shurjomukhi.com.bd,ahm.masum@shurjomukhi.com.bd,support@paypoint.com.bd";
	//$to = "zahid@shurjomukhi.com.bd,ahm.masum@shurjomukhi.com.bd";
	$headers = "From: ".strip_tags($frm)."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	if(mail($to, $sub, $body, $headers)){
		echo "Your request has been sent to authority.";
	}
	else{
		echo "Problem to send email. Please try later.";
	}
}
else{
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
                 <h2><img src="images/shurjoPay.png" border="0" style="none;"></h2>
		  <br />
		  </div>
		  <br />
		  <br />
			<div id="login-box-name" style="margin-top:20px;">User Name:</div>
				<div id="login-box-field" style="margin-top:20px;">
				  <input name="name" class="form-login" title="Username" value="" size="30" maxlength="2048" />
				</div>
			<br />
			<!--<span class="login-box-options">
			  <input type="checkbox" name="1" value="1"> Remember Me <a href="#" style="margin-left:30px;">Forgot password?</a>
			</span>-->
		<br />
		<br />
		<br><input type="submit" id="forgotpassword" name="forgotpassword" value="Submit" style="margin-left:90px;">
	</div>
</div>
    </form>
</body>
</html>
<?php
}
?>

