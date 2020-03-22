<?php
include("includes/configure.php");

$result = mysqli_query($GLOBALS["___mysqli_sm"],"SELECT * from sp_epay WHERE 	gateway='qcash' AND bank_status='SUCCESS'");
$tb="<!DOCTYPE html>
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
    <th>txid</th>
	<th>shurjoPay Amount</th>
    <th>Bank response Amount</th> 
	
    <th>Card Number</th>
	<th>Method</th>
  </tr>";
  
  echo $tb;
 

$data="</thead>
  <tbody>";
  echo $data;

while ($row = mysqli_fetch_assoc($result)) {

if($row['gateway']=='qcash' && $row['bank_status']=='SUCCESS')
{
 $data=convertJson($row['bank_response']);
   $i=0;
  if(@$data[ResponseCode]=='000')
  {
  }else{
   $amount=$data['PurchaseAmount']/100;
     $foo="<tr>
    <td>{$row['txid']}</td>
	<td>{$row['amount']}</td>
    <td>{$amount}</td> 
	
    <td>{$data['PAN']}</td>
	<td>{$data['Brand']}</td>
  </tr>";

  echo $foo;
  

  }
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

function convertJson($data)
{
$xmlResponse = simplexml_load_string($data);
$json = json_encode($xmlResponse);
$array = json_decode($json,TRUE);
return $array;

}



?>