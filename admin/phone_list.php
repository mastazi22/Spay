<?php
require_once 'paypointApi.php';
$pPApi = new paypointApi();
$page = $_SERVER['REQUEST_URI'];
$sec = "300";
?>
<html>
    <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	<title>ShurjoPay Dashboard</title>
  <link rel="stylesheet" media="screen" href="css/style.css" media="all" />
    </head>
	
    <body>
  <table style='width:100%;'>
  <tr>
  <th><h3>ShurjoPay Dashboard</h3></th>
  </tr>
  </table>  
<?php
//require_once('DB.class.php');
/*
if(isset($_POST['mobile_number']))
{
//$ppdb=new SMDB('paypoint');

$mobile_numbers=$_POST['mobile_number'];
$mobile_types=$_POST['mobile_type'];
$count=count($_POST['mobile_number']);
for($i=0;$i<$count;$i++)
{

if($mobile_types[$i]=='TT')
{
$q="INSERT INTO blacklist_tt VALUES (NULL , '".$mobile_numbers[$i]."', '1', '".date('Y-m-d h:i:s')."', 'Fraud Multi Txn')";
}
if($mobile_types[$i]=='AT')
{
$q="INSERT INTO blacklist_at VALUES (NULL , '".$mobile_numbers[$i]."', '1', '".date('Y-m-d h:i:s')."', 'Fraud Multi Txn')";
}

if($mobile_types[$i]=='BL')
{
$q="INSERT INTO blacklist_bl VALUES (NULL , '".$mobile_numbers[$i]."', '1', '".date('Y-m-d h:i:s')."', 'Fraud Multi Txn')";
}

if($mobile_types[$i]=='RB')
{
$q="INSERT INTO blacklist_rb VALUES (NULL , '".$mobile_numbers[$i]."', '1', '".date('Y-m-d h:i:s')."', 'Fraud Multi Txn')";
}

if($mobile_types[$i]=='GP')
{
$q="INSERT INTO blacklist_gp VALUES (NULL , '".$mobile_numbers[$i]."', '1', '".date('Y-m-d h:i:s')."', 'Fraud Multi Txn')";
}
$ppdb->query($q);

}
}
*/
if(isset($_POST['userid']))
{
	$userID=($_POST['userid']);
}
else
{
	$userID=($_GET['userid']);
}
?>
<form action='phone_list.php' method='POST'/>
<?php
	$row = $pPApi->getPaypointUserInfo($userID);	
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
	if($row->isAccountClosed!='Yes' Or $row->isUserActive!='No')
	{
	echo "<tr><th>Action</th><td>:</td><td>"; 
	echo "<a  class='button' href='block_user.php?userid=".$row->userid."'>Block User</a>";	  
   	echo "<a  class='button' href='phone_list.php?userid=".$row->userid."'>Top Up List</a></td></tr>";
	}
 
echo "</table>";
echo "</div>";
?>

<div style='text-align:center;float:left;margin-left:10px;display:inline-block;width:64%;'>
<h1>Mobile Topups By User :<?php echo $userID; ?></h1>
<?php
//$ppdb=new SMDB('paypoint');
//$sql1= "SELECT * FROM paypoint_users_orders WHERE user_id= '".$userID."' LIMIT 0,50";

//$res = $ppdb->query($sql1);  
//$txn_data=$res->fetchAll();
$txn_data = $pPApi->getPaypointUserOrders($userID);	
echo "<table style='width:100%;'>";
echo "<tr><th>Order Time</th><th>Spay Id</th><th>Amount</th><th>Status</th><th>Operator</th><th>Mobile Number</th></tr>";

foreach($txn_data as $tdata){


if (strpos($tdata->spay_id,'PPTAIRTEL') !== false) 
{
$type='AT';
$q="SELECT ar.tc_amount,ar.recharge_res_time,ar.tc_mobile,ba.status FROM airtel_recharge ar LEFT JOIN blacklist_at ba ON ba.blacklist=ar.tc_mobile where ar.spay_id='".$tdata->spay_id."'";

}
else if (strpos($tdata->spay_id,'PPT10160') !== false) 
{
$type='TT';
$q="SELECT ar.tc_amount,ar.topup_res_time,ar.tc_mobile,ba.status FROM teletalk_topup ar LEFT JOIN blacklist_tt ba ON ba.blacklist=ar.tc_mobile where ar.spay_id='".$tdata->spay_id."'";
}
else if (strpos($tdata->spay_id,'PPTBLINK') !== false) 
{

$type='BL';
$q="SELECT bt.tc_amount,bt.recharge_res_time,bt.tc_mobile,bl.status FROM banglalink_recharge bt LEFT JOIN blacklist_bl bl ON bl.blacklist=bt.tc_mobile where bt.spay_id='".$tdata->spay_id."'";
}
else if (strpos($tdata->spay_id,'PPTROBI') !== false) 
{
$type='RB';
$q="SELECT rt.tc_amount,rt.recharge_res_time,rt.tc_mobile,rl.status FROM robi_recharge rt LEFT JOIN blacklist_rb rl ON rl.blacklist=rt.tc_mobile where rt.spay_id='".$tdata->spay_id."'";
}
else if (strpos($tdata->spay_id,'PPTGRAMEEN') !== false) 
{
$type='GP';
$q="SELECT rt.tc_amount,rt.recharge_res_time,rt.tc_mobile,rl.status FROM grameen_recharge rt LEFT JOIN blacklist_gp rl ON rl.blacklist=rt.tc_mobile where rt.spay_id='".$tdata->spay_id."'";
}
else
{
$q='';
}

if($q!="")
{
$res=$ppdb->query($q);
$result=$res->fetchAll();

$mobile_number=$result[0]->tc_mobile;
$amount=$result[0]->tc_amount;
$status=$result[0]->status;

	echo "<tr>";
	echo "<td>";
	if($type=='TT'){echo $result[0]->topup_res_time; }else if($type=='BL'){echo $result[0]->recharge_res_time; } else {echo $result[0]->recharge_res_time;}
	
	echo "</td>";
	echo "<td>";
	echo $tdata->spay_id;
	echo "</td>";
	echo "<td>";
	echo $amount;
	echo "</td>";
	echo "<td>";
	if($status=='')
	{echo "Active";}
	else {	echo 'Black';}
	echo "</td>";
	echo "<td>";
	if($type=='TT'){echo "Teletalk"; }else if($type=='BL'){echo "Banglalink"; } else if($type=='RB'){echo "Robi"; }else if($type=='GP'){echo "Grameen";} else {echo "Airtel";}
	echo "</td>";
	echo "<td>";
	echo $mobile_number;
	if($status=='')
	{
	echo "<input type='hidden' name='mobile_number[]' value='".$mobile_number."'/>";
	echo "<input type='hidden' name='mobile_type[]' value='".$type."'/>";
	}
	echo "</td>";
	
		echo "</tr>";
		}
}
?>
	<tr><td><input type='hidden' name='userid' value='<?php echo $userID;?>'/><input type='submit' name='submit' class='button' value='Blacklist All'/></td><td></td><td></td><td></td><td></td><td></td></tr>
	</table>
	</div>
	</form>
</br></br></br>
</body>
</html>
