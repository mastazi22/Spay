<?php
$link = mysql_connect('localhost','smshurjopaySIU','yAcTSGSTNTNMP6Yx');
mysql_select_db('shurjopay');
//echo $_GET['password'];
if($_GET['username']=='dse' && $_GET['password']=='dse123123123')
{
	$txid = mysql_real_escape_string($_GET['txid']);
	$sql = "SELECT * FROM sp_epay WHERE txid='$txid'";
	//echo $sql;
	$result = mysqli_query($GLOBALS["___mysqli_sm"], $sql,$link);
	$info = mysql_fetch_array($result);
	if($info['bank_status']=='SUCCESS') {
		$response = array('status'=>true);
	} else {
		 $response = array('status'=>false); 
	}
} else {
	 $response = array('status'=>false); 
}

echo json_encode($response);
?>
