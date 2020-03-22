<?php
require_once 'paypointApi.php';
$page = $_SERVER['REQUEST_URI'];
$sec = "300";
$pPApi = new paypointApi();
$userID = ($_GET['userid']);
$txn_data = $pPApi->getPaypointUserOrders($userID);

?>
<html>
    <head>
    	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
		<title>ShurjoPay Dashboard</title>
  		<link rel="stylesheet" media="screen" href="css/style.css" media="all" />
    </head>
	
    <body>
  		<table style='width:100%;'>
  			<tr><th><h3>ShurjoPay Dashboard</h3></th> </tr>
  		</table>  

<?php

$row    = $pPApi->getPaypointUserInfo($userID);

	echo "<div style='text-align:center;width:35%;float:left;'>";
	echo "<h1>User Details</h1>";
	echo "<table border='1px' cellpadding='3px' style='width:100%';>";

	echo "<tr><th>UserId</th><td>:</td><td>";  
    echo $row->userid."</td></tr>";  
    echo "<tr><th>Username</th><td>:</td><td>";  
    echo $row->username."</td></tr>";   
    echo "<tr><th>Name</th><td>:</td><td>";  
    echo $row->firstName." ".$row->lastName."</td></tr>";
    echo "<tr><th>CellNo</th><td>:</td><td>";  
    echo $row->mobile."</td></tr>";   
    echo "<tr><th>Email</th><td>:</td><td>";  
    echo $row->emailAddress."</td></tr>"; 
    echo "<tr><th>Website</th><td>:</td><td>";  
    echo $row->SignupSource."</td></tr>";     
	echo "<tr><th>Account Activated</th><td>:</td><td>";  
    echo $row->isUserActive."</td></tr>";   	
	echo "<tr><th>Signup Date</th><td>:</td><td>";  
    echo date('d-M-Y H:i:s',strtotime($row->signupDate))."</td></tr>";  
	
	echo "<tr><th>Account Blocked</th><td>:</td><td>";  
    echo $row->isAccountClosed."</td></tr>";   
    echo "<tr><th>Close Date</th><td>:</td><td>";  
    echo $row->ClosedDate."</td></tr>"; 
    echo "<tr><th>Comments</th><td>:</td><td>";  
    echo $row->comments."</td></tr>"; 
	echo "<tr><th>Action</th><td>:</td><td>"; 
	echo "<a target='_blank' class='button' href='phone_list.php?userid=".$row->userid."'>Top Up List</a>";
	if($row->isAccountClosed!='Yes' Or $row->isUserActive!='No')
	{
		echo "<a target='_blank' class='button' href='block_user.php?userid=".$row->userid."'>Block User</a>";	  
		echo "</td></tr>";
	}
 
	echo "</table>";
	echo "</div>";
?>

<div style='text-align:center;float:left;margin-left:10px;display:inline-block;width:64%;'>
<h1>Transaction Done last 30 Days</h1>
<?php	
	$txn_data = $pPApi->getPaypointUserOrders($userID);	
?>
	<table style='100%;' border="1.5px">
		<tr>
			<tr><th>Tx Time</th><th>Spay Id</th><th>Status</th><th>Amount</th><!--<th>Method</th><th>Gateway</th> --><th>Card Owner</th><th>Card Number</th></tr>
		</tr>
		<?php foreach($txn_data as $key=>$row): ?>
			<tr  style='border-bottom: 1px solid red;'>
				<td style="font-size: 8px;"><?php echo $row->gw_time;?></td>
				<td><?php echo $row->spay_id;?></td>
				<td><?php echo $row->bank_status;?></td>
				<td><?php echo $row->amount;?></td>
				<td><?php echo $row->card_holder_name;?></td>
				<td><?php echo $row->card_number;?></td>
			</tr>	
		<?php endforeach;?>	
	</table>
	</div>
</br></br></br>
</body>
</html>
