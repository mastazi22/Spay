<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
</head>
<body>
<div id="container">
<div id="header">
<?php
if(isset($_SESSION['ORDER_DETAILS']['userID'])){

	$sql = mysqli_query($GLOBALS["___mysqli_sm"],"select merchant_logo, merchant_domain from sp_merchants where id='{$_SESSION['ORDER_DETAILS']['userID']}'");
	$logo=mysqli_fetch_object($sql);
}
?>
<div id="banklogo"><img src="./img/shurjoPay.png" alt="shurjoPay" height="50"></div>
<div id="merchantlogo"><img height="50" src="./img/merchant_logo/<?php if(isset($logo->merchant_logo) ) { echo $logo->merchant_logo; } ?>"
 alt="<?php if(isset($logo->merchant_domain)) echo $logo->merchant_domain; ?>"></div>
</div>
