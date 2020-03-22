<?php

	$page = $_SERVER['PHP_SELF'];
	$sec = "60";

?>
<html>
<head>
	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	<title>ShurjoPay FAIL Dashboard</title>
	<script tyle="text/javascript" src="js/jquery-1.7.1.min.js" charset="utf-8"></script>
	<script tyle="text/javascript" src="js/datepicker/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>  
	<link rel="stylesheet" media="screen" href="css/style.css" media="all" />
</head>

<body>
	<table style='width:100%;'>
		<tr>
			<th><h3>ShurjoPay Dashboard For Failed Transactions</h3></th>
		</tr>
	</table>  

	<?php	
	require_once '../includes/configure.php';
	require_once 'paypointApi.php';
	$pPApi = new paypointApi();


	$query="SELECT `txid`, `intime`, `card_holder_name`,`card_number`,`method`, `amount`, `bank_status`, `clientip`, `smUid`  
	FROM `sp_epay` 
	WHERE `gw_return_id` != '000' and `gw_return_id` != '555' ORDER BY `intime` DESC LIMIT 100 ";
	$res=mysqli_query($GLOBALS["___mysqli_sm"],$query);	
	$temp='';
	while ($data = mysqli_fetch_object($res)) 
	{

		$pp_users = '';
		$uname = ''; 
		$phone = '';
		$comments = '';
		if(isset($data->smUid) && $data->smUid != '')
		{
			$pp_users = $pPApi->getPaypointUserInfo($data->smuid);	    
			$uname=$pp_users->firstName." ".$pp_users->lastName;
			$phone=$pp_users->mobile;
			$comments=$pp_users->comments;
		}




		$spay_id = $data->txid;
		$sqlnewcomment = "SELECT * FROM failcomments where txid = '{$spay_id}' ORDER BY intime DESC limit 0,1";	

		$res2 = mysqli_query($GLOBALS["___mysqli_sm"],$sqlnewcomment);
		$result2 = mysqli_fetch_object($res2);
		$oldcomments = '';
		if($result2 !=NULL) 
		{
			$row2 = $result2;
			$oldcomments = $row2->comments;	
		}
		

		$temp.="<tr>
		<td>
		<font color = 'Red'>".$data->txid."</font></td>
		<td><font color = 'Red'>".$data->intime."</font></td>
		<td><font color = 'Red'>".$data->method."</font></td>
		<td><font color = 'Red'> <b>".$data->amount."<b></font></td>
		<td><font color = 'Red'>".$data->bank_status."</font></td>
		<td><font color = 'Red'><a href='http://www.iplocationfinder.com/".$data->clientip."' target=_blank><input type='button' value='".$uname."'></a></font>".$comments."</td>
		<td>".$phone."</td>
		<td><font color = 'Red'><a href='userdetails.php?userid=".$data->smUid."' target=_blank><input type='button' value='".$data->smUid."'></a></font></td>
		<td>".$oldcomments."</td>
		<td><a href='failcomments.php?txid=".$data->txid."&smUid=".$data->smUid."&uname=".$uname."&mobile=".$phone."' target=_blank><input type='button' value='Add/Edit'></a></td>
		</tr>";
	}

	echo "<br/>";
	?>
	<?php

	echo "<table style='width:100%;'>";
	echo "<tr><th>txid</th><th>Tx time</th><th>Type</th><th>Amount</th><th>Status</th><th>Username</th><th>User Phone</th><th>UserId</th><th>Comments</th><th></th></tr>";
	echo $temp;
	echo "</table><br/><br/>";
	echo "<br/><br/>";
	?>
</body>
</html>
