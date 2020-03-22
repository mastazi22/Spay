<?php

	$page = $_SERVER['PHP_SELF'];
	$sec = "300";
	function random_color_part() {
	    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

	function random_color() {
	    return '#'.random_color_part() . random_color_part() . random_color_part();
	}
	$colorArray = array();
	for($i=0;$i<=23;$i++) 
	{
		$colorArray['hour'.$i] = random_color();
	}

?>
<html>
    <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	<title>ShurjoPay DBBL Verifycation Dashboard</title>
  <script tyle="text/javascript" src="js/jquery-1.7.1.min.js" charset="utf-8"></script>
	<script tyle="text/javascript" src="js/datepicker/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>  
	<link rel="stylesheet" media="screen" href="css/style.css" media="all" />
    </head>
	
    <body>
  <table style='width:100%;'>
  <tr>
  <th><h3>ShurjoPay DBBL Verifycation Dashboard</h3></th>
  </tr>
  </table>  

<?php
	require_once '../includes/configure.php';
	$query = "SELECT *
		FROM `sp_epay`  
		WHERE 
			clientip != '127.0.0.1'
			AND gw_return_id IS NULL
			AND return_code IS NULL
			AND gateway = 'dbbl'
			AND remarks != ''
			#AND intime > '2018-02-13'
			#AND Txid like '%JBD%'
			#AND bank_status = 'SUCCESS' OR bank_status = 'OverLimit'
			#AND smUid != 'NULL'
		ORDER BY intime DESC LIMIT 0,150";

		$res = mysqli_query($GLOBALS["___mysqli_sm"],$query);			
		$temp='';

	while ($data = mysqli_fetch_object($res)) 
	{

		$hour = 'hour'.intval(date('H',strtotime($data->gw_time)));
		$id=$data->smUid;

		if ($data->method == 'visa' or $data->method == 'master_card') 
		{
			$bin = "<a href='http://bins.pro/search?action=searchbins&bins=".substr($data->card_number,3,6)."' target=_blank><input type='button' value='".$data->card_number."'></a>"; 
		} 
		else 
		{
			$bin =$data->card_number;
		}
			
		if ($data->bank_status == 'SUCCESS') 
		{
			$temp.="<tr bgcolor='".$colorArray[$hour]."'><td>".$data->txid."</td><td>".$data->intime."</td><td>".$data->card_holder_name."</td><td>".$bin."</td><td>".$data->method."</td><td><b>".$data->amount."</b></td><td>".$data->bank_status."</td><td><a href='http://www.iplocationfinder.com/".$data->clientip."' target=_blank><input type='button' value='".$data->clientip."'></a></td><td><a href='userdetails.php?userid=".$id."' target=_blank><input type='button' value='".$id."'></a></td></tr>";
		} else {
			$temp.="<tr><td><font color = 'Red'>".$data->txid."</font></td><td><font color = 'Red'>".$data->intime."</font></td><td><font color = 'Red'>".$data->remarks."</font></td><td><font color = 'Red'>".$bin."</font></td><td><font color = 'Red'>".$data->method."</font></td><td><font color = 'Red'> <b>".$data->amount."<b></font></td><td><font color = 'Red'>".$data->bank_status."</font></td><td><font color = 'Red'><a href='http://www.iplocationfinder.com/".$data->clientip."' target=_blank><input type='button' value='".$data->clientip."'></a></font></td><td><font color = 'Red'><a href='verify-form.php?txid=".$data->txid."' target=_blank>Verify</a></font></td></tr>";
		}
	}

	echo "<br/><br/>Checked At:<b>". date('d-M-Y - h:ia')."</b><br/><br/>";
	echo "<table style='width:100%;'>";
	echo "<tr><th>txid</th><th>Tx time</th><th>Remarks</th><th>Card No</th><th>Type</th><th>Amount</th><th>Status</th><th>IP</th><th>Action</th></tr>";
	echo $temp;
	echo "</table>";

?>
</body>
</html>


