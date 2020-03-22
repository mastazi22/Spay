<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form name="ipcheck" id="ipcheck" action="http://dev.shurjomukhi.com/shurjopay/ipallow.php" method="POST"> 
	<input type="submit" value="Submit" >
</form>
<a href="http://dev.shurjomukhi.com/shurjopay/ipallow.php">IP Test</a>
<?php 

echo $_SERVER ['HTTP_CLIENT_IP'].'=='.$_SERVER ['HTTP_X_FORWARDED_FOR'].'=='.$_SERVER ['REMOTE_ADDR'];

echo empty ( $_SERVER ['HTTP_CLIENT_IP'] ) ? (empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER ['REMOTE_ADDR'] : $_SERVER ['HTTP_X_FORWARDED_FOR']) : $_SERVER ['HTTP_CLIENT_IP'];
?>
</body>
</html>