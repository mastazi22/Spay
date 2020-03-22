<?php session_start();
if( $_SESSION['ORDER_DETAILS']['loggedIN'] != true)
{
    header( 'Location: login.php' );
}
?>
<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
</head>
<body>

<div id="container">
  <div id="header">
  <div id="banklogo"><img src="./img/sand-box-logo.jpg" alt="Sand Box"></div>
<div id="merchantlogo"><img src="./img/sp_logo.jpg" alt="ShurjoPay"></div>
  </div>
  <div id="main_container">
      <noscript>
        &lt;h3&gt;Please enable JavaScript in your browser for the iPay&amp;reg; service&lt;/h3&gt;
    </noscript>	
   
    <h1>Test Page For shurjoPay</h1>
<h2>CreateOrder</h2>
<?php
if(isset($_POST['ip'])){ 
	$ip = $_POST['ip'];
}
else {
	$ip = '<End user IP goes here>';
}
if(isset($_POST['order_id'])){ 
	$order_id = $_POST['order_id'];
}
else {
	$order_id = '<uniqid with given code goes here>';
}
if(isset($_POST['amount'])){ 
	$amount = $_POST['amount'];
}
else {
	$amount = '<Enter amount here>';
}
if(isset($_POST['err_code'])){ 
	$err_code = $_POST['err_code'];
}
else {
	$err_code = '<error code goes here>';
}
if(isset($_POST['return_url'])){ 
	$return_url = $_POST['return_url'];
}
else {
	$return_url = '<return url goes here>';
}
$xml='<?xml version="1.0" encoding="utf-8"?>
<shurjoPay><merchantName>spaytest</merchantName>
<merchantPass>spaytest</merchantPass>
<userIP>'.$ip.'</userIP>
<uniqID>'.$order_id.'</uniqID>
<totalAmount>'.$amount.'</totalAmount>
<paymentOption>shurjopay</paymentOption>
<errCode>'.$err_code.'</errCode>
<returnURL>'.$return_url.'</returnURL></shurjoPay>';
?>
<form method="post" action="./sp-data.php">
	<textarea name="spdata" rows="15" style="width:100%"><?php echo $xml; ?></textarea>
	<br/>
	<input type="submit"/> <b><font color:red>You Can Change the Above Field as per Your Requirements</font></b>
</form>
    
    
   </div>
  <div id="footer"></div>
</div>
<div id="shadow"></div>


  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="-1">


</body>
</html>