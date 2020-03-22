
 <?php
 include("../includes/configure.php");
//include ("includes/session_handler.php");
//include("includes/login.php");
$sql = "SELECT * FROM sp_epay Where uid='64'";
$retval = mysqli_query($GLOBALS["___mysqli_sm"],  $sql);
if(! $retval )
{
  die('Could not get data: ' . mysql_error());
}
$data="<!DOCTYPE html>
<html>
<head>
<script src='https://code.jquery.com/jquery-2.2.3.js' integrity='sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4=' crossorigin='anonymous'></script>
 <link rel='stylesheet' type='text/css' href='//cdn.datatables.net/1.10.11/css/jquery.dataTables.css'>
<script type='text/javascript' charset='utf8' src='//cdn.datatables.net/1.10.11/js/jquery.dataTables.js'></script>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
</head> 
<table id='table_id' class='display' border='1' width='100%'>
 <thead>
  <tr>
    <th>Order Id</th>
    <th>Tx Id</th>
    <th>Amount</th>
    <th>Bank Status</th>
    <th>Payment Method</th>
    <th>Date Time</th>
    <th>Card Number</th>
  </tr>";
  echo $data;
  $data="</thead>
  <tbody>";
  echo $data;
  $amount=0;
while($row = mysql_fetch_assoc($retval)){
    //iterate over all the fields
        //generate output
        if($row['bank_status']=='SUCCESS' || $row['bank_status']=='FAIL' )
        {
          if($row['bank_status']=='SUCCESS')
          {
          	$amount=$amount+$row['amount'];
          }
        	$data="<tr>
          <td>{$row['order_id']}</td>
          <td>{$row['txid']}</td>
          <td>{$row['amount']}</td>
          <td>{$row['bank_status']}</td>
          <td>{$row['method']}</td>
          <td>{$row['intime']}</td>
          <td>{$row['card_number']}</td>
          </tr>";
          echo $data;
          
        }
       
    
}
 
$data="</tbody>
 </table>
</body>
<script type='text/javascript'>
  $(document).ready( function () {
    $('#table_id').DataTable();
} );
</script>
</html>";
echo $data;
 $total="<h1><tr><td><td align='right'>Total:<td align='center'>".$amount."</td></td></td></tr></h1>";
  echo $total;
mysql_close($conn);
?>