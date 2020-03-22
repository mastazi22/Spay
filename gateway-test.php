<html>
<body>
<?php 
switch ($_SERVER["SERVER_NAME"]) {
	case 'dev.shurjomukhi.com' :
		$return_url = "http://dev.shurjomukhi.com/shurjorajjo/shurjopay/gateway-test-return.php";
		$post_url = "http://dev.shurjomukhi.com/shurjorajjo/shurjopay/sp-data.php";
		break;
		// Development site (e.g. localhost) configuration
	case 'localhost' :
		$return_url = "http://localhost/shurjopaysr/gateway-test-return.php";
		$post_url = "http://localhost/shurjopaysr/sp-data.php";
		break;
		// Live site configuration
	default :
		$return_url = "https://".$_SERVER["SERVER_NAME"]."/gateway-test-return.php";
		$post_url = "https://shurjopay.com/sp-data.php";
		break;
}
$ip = isset ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
if(isset($_POST['amount'])){
	$amount = $_POST['amount'];
}
else{
	$amount = 10;
}
$xml='<?xml version="1.0" encoding="utf-8"?>
<shurjoPay><merchantName>spaytest</merchantName>
<merchantPass>123456</merchantPass>
<userIP>'.$ip.'</userIP>
<uniqID>NOK1234546789</uniqID>
<totalAmount>'.$amount.'</totalAmount>
<paymentOption>shurjopay</paymentOption>
<returnURL>'.$return_url.'/</returnURL></shurjoPay>';
?>
<h1>Test Page For shurjoPay</h1>
<h2>CreateOrder</h2>
<form method="post" action="<?php echo $post_url; ?>">
	<textarea name="spdata" rows="15" style="width:100%"><?php echo $xml; ?></textarea>
	<br/>
	<input type="submit"/>
</form>

</body>
</html>
