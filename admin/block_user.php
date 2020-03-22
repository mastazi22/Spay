<?php
require_once 'paypointApi.php';
$page = $_SERVER['REQUEST_URI'];
$sec = "300";
$pPApi = new paypointApi();
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

<form action='block_user.php' method='POST' />
<?php
//require_once('DB.class.php');
//$logindb=new SMDB('shurjologin');
if(isset($_GET['userid']) and $_GET['userid']!="")
{
$userID=($_GET['userid']);
}
else if(isset($_POST['userid']) and $_POST['userid']!="")
{
$userID=$_POST['userid'];
// $sql="UPDATE usersinfo set comments='".mysql_real_escape_string($_POST['comments'])."',isAccountClosed='Yes',isUserActive='No',ClosedDate='".date('Y-m-d h:i:s')."' where userid='".mysql_real_escape_string($userID)."'";
// $logindb->query($sql);
$pPApi->putPaypointUserBlock($userID,$_POST['comments']);
header('Location: userdetails.php?userid='.$userID);
}
/*
$userID=mysql_real_escape_string($userID);
$sql= "SELECT * FROM usersinfo WHERE userid= '".$userID."'";
$res = $logindb->query($sql);  
$result=$res->fetchAll();
$row=$result[0];
*/
$row    = $pPApi->getPaypointUserInfo($userID);
echo "<div style='text-align:center;width:33%;float:left;margin-left:35%;'>";
echo "<h1>User Details</h1>";
echo "<table border='1px' cellpadding='3px' style='width:100%';>";

	echo "<tr><th>UserId</th><td>:</td><td>"; 
  echo "<input name='userid' type='hidden' value='".$row->userid."'>";	
    echo $row->userid."</td></tr>";  
    echo "<tr><th>Username</th><td>:</td><td>";  
    echo $row->username."</td></tr>";   
    echo "<tr><th>Name</th><td>:</td><td>";  
    echo $row->firstName." ".$row->lastName."</td></tr>";
    echo "<tr><th>CellNo</th><td>:</td><td>";  
    echo $row->mobile."</td></tr>";   
    echo "<tr><th>Email</th><td>:</td><td>";  
    echo $row->emailAddress."</td></tr>"; 
   
    echo "<tr><th>Comments</th><td>:</td><td>";  
    echo "<textarea name='comments'></textarea></td></tr>"; 
	if($row->isAccountClosed!='Yes' Or $row->isUserActive!='No')
	{
	  echo "<tr><th>Action</th><td>:</td><td>"; 
echo "<input name='submit' class='button' type='submit' value='Block User'";	  
   echo "</td></tr>";
	}
 
 
echo "</table>";
echo "</div>";
?>
</form>
</br></br></br>
</body>
</html>