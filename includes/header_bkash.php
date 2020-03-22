<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
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
<a name="top"></a>
<div id="banklogo" style="float: left; width: 33%; padding-left: 2%; display: inline; margin-left: 0px; position: relative; margin-top: 10px;"><img src="./img/shurjoPay.png" alt="shurjoPay" height="50"></div>
<div id="bkashlogo" style="width: 30%; float: left; display: inline; margin-top: 10px; text-align: center;"><img src="./img/bkash-sp.jpg" alt="shurjoPay" height="50"></div>
<div id="merchantlogo" style="width: 30%; padding-right: 2%; margin-top: 10px;"><img src="./img/merchant_logo/<?php echo $logo->merchant_logo; ?>" alt="<?php echo $logo->merchant_domain; ?>"></div>
</div>
