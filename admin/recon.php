<html>
<head>
<link href="./css/layout.css" rel="stylesheet" type="text/css">
<link href="./css/text.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
if(isset($_POST['sprecon'])){
include("../includes/configure.php");
include("../xml-parser.php");
session_start();
$xml2arr = xml2array($_POST['sprecon']);

$merchantName = $xml2arr['SP']['Request']['Order']['merchantName'];
$merchantPass = $xml2arr['SP']['Request']['Order']['merchantPass'];
$returnURL = $xml2arr['SP']['Request']['Order']['returnURL'];
$uniqid = $xml2arr['SP']['Request']['Order']['uniqid'];
$refID = $xml2arr['SP']['Request']['Order']['refID'];
$amount = $xml2arr['SP']['Request']['Order']['amount'];
$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "SELECT * from sp_merchants WHERE username='".$merchantName."' and password='".$merchantPass."' and isactive='yes'");
$result= mysql_fetch_object($sql_query);	
if($result){
	$sql_query=mysqli_query($GLOBALS["___mysqli_sm"], "UPDATE sp_epay SET Reconciliation='open' WHERE uid='".$result->id."' and txid='".$uniqid."' and bank_tx_id='".$refID."'");
	$post_data = '<?xml version="1.0" encoding="utf-8"?>
					<SP>
					  <Response>
					     <uniqid>'.$uniqid.'</uniqid>
					     <amount>'.$amount.'</amount>
					     <refID>'.$refID.'</refID>
					     <Message>Your refID is marked for reconciliation</Message>
					  </Response>
					 </SP>';
?>
<form method="post" action="<?php echo $returnURL; ?>" id="frm_submit">
	<input type="hidden" name="spdata" value='<?php echo $post_data; ?>'>
</form>
<script>
document.getElementById('frm_submit').submit();
</script>
<?php
	}
}
else{
	echo "<div style='color:#ff0000;text-align:center;font-size:25px;font-weight:bold;'>You are not allowed to access this page!!!!!!!</div>";
}
?>
</body>
</html>