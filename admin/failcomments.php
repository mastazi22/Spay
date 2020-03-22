<html>
    <head>
	<title>ShurjoPay Fail Comments</title>
  <link rel="stylesheet" media="screen" href="css/style.css" media="all" />
    </head>
	
    <body>
  <table style='width:100%;'>
  <tr>
  <th><h3>Tx fail Comments</h3></th>
  </tr>
  </table>  
<?php
require_once '../includes/configure.php';


if(isset($_REQUEST['txid']))
{

//$spdb=new SMDB('shurjopay');

$spay_id=$_REQUEST['txid'];
$smuid=$_REQUEST['smUid'];
$uname=$_REQUEST['uname'];
$mobile=$_REQUEST['mobile'];
if(isset($_REQUEST['failcomments'])) {
	$failcomments = $_REQUEST['failcomments'];
	$newauthor = $_REQUEST['author'];
	$q="INSERT INTO failcomments VALUES (NULL , '".$spay_id."', '".$smuid."','".$failcomments."', '".$newauthor."',NULL, NULL)";
	//$spdb->query($q);
	mysqli_query($GLOBALS["___mysqli_sm"],$q);
	} 
$sqlnewcomment = "SELECT * FROM failcomments where txid = '{$spay_id}' ORDER BY intime DESC limit 0,1";
$sqloldcomment = "SELECT * FROM failcomments where smuid = '{$smuid}' ORDER BY intime DESC";

//$res = $spdb->query($sqlnewcomment);  
//$result=$res->fetchAll();
$res = mysqli_query($GLOBALS["___mysqli_sm"],$sqlnewcomment);
$result =  mysqli_fetch_object($res);
	$oldcomments = '';
	$oldauthor = '';
	$oldtime = '';
	if($result !=NULL) 
    {
		$row=$result;
		$oldcomments = $row->comments;
		$oldauthor = $row->author;

		if ($row->intime != "") {
			$oldtime = date('d-M-Y H:i',strtotime($row->intime));
		}
	}	


//~ print_r($row);
//~ echo $sqlnewcomment;
//~ echo $sqloldcomment;
//$res2 = $spdb->query($sqloldcomment);  
//$data=$res2->fetchAll();
$res2 = mysqli_query($GLOBALS["___mysqli_sm"],$sqloldcomment);
$data =  mysqli_fetch_object($res);

}
?>
<div style="margin:auto;text-align:center;">
	<h3>Add/Edit Comment for Transaction : <?php echo $spay_id; ?></h3>
	<table style="margin:auto;text-align:center;">
		<tr>
			<th>User Name :</th>
			<td><?php echo $uname; ?></td>
		</tr>
		<tr>
			<th>Phone No :</th>
			<td><?php echo $mobile; ?></td>
		</tr>
		<tr><form method="get" action=''>
			<th>Comments :</th>
			<td><textarea name="failcomments" style="height:100px;border-radius:8px;padding:8px;"><?php echo $oldcomments; ?></textarea></td>
		</tr>
		<tr>
			<th>Comments By:</th>
			<td><input type="text" name="author" value='<?php echo $oldauthor; ?>' style="height:30px;border-radius:8px;padding:8px;text-transform:uppercase;"/>
			<input type="hidden" name="txid" value ="<?php echo $spay_id; ?>" />
			<input type="hidden" name="smUid" value ="<?php echo $smuid; ?>" />
			<input type="hidden" name="uname" value ="<?php echo $uname; ?>" />
			<input type="hidden" name="mobile" value ="<?php echo $mobile; ?>" />

			</td>
		</tr>
		<tr>
			<th>Commented on:</th>
			<td><?php echo $oldtime; ?></td>
		</tr>
		<tr>
			<th colspan=2 style="text-align:center;"><input type="submit" value="Update Comment" /></form></th>
		</tr>
	</table>
	<h3>Other Comments for User : <?php echo $uname; ?></h3>
	<table style="margin:auto;text-align:center;">
		<tr>
			<th>Id</th>
			<th>Txid</th>
			<th>Comments</th>
			<th>Author</th>
			<th>Timestamp</th>
		</tr>

		<?php 
		if($data !=NULL) {
			foreach($data as $cdata)
			{
				echo "<tr>
					<td>".$cdata->id."
					</td>
					<td>".$cdata->txid."
					</td>
					<td>".$cdata->comments."
					</td>
					<td>".$cdata->author."
					</td>
					<td>".date('d-M-Y H:i',strtotime($cdata->intime))."
					</td>
				</tr>";
			}
		}
		
		?>
	</table>
</div>
</br></br></br>
</body>
</html>
