<?php

require_once '../includes/configure.php';


$post = $_POST['search']['value'];

var_dump($_POST);
$sql_count = "select txid,order_id,amount,method,gateway,bank_tx_id,bank_status,intime from sp_epay";
$total_pages = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_sm"],$sql_count));

$length = $_POST['length']?$_POST['length']:10; 
$start = $_POST['start']?$_POST['start']:1;
$sql = "select txid,order_id,amount,method,gateway,bank_tx_id,bank_status,intime from sp_epay limit ".$start.",".$length;
$sql1=mysqli_query($GLOBALS["___mysqli_sm"],$sql); 

$data = array();
while ($row = mysqli_fetch_assoc($sql1)) {
	$r = array();
	$r[] = $row['txid'];
	$r[] = $row['order_id'];
	$r[] = $row['amount'];
	$r[] = $row['method'];
	$r[] = $row['gateway'];
	$r[] = $row['bank_tx_id'];	
	$r[] = $row['bank_status'];
	$r[] = $post;//$row['intime'];
	$data[] = $r;
}

// $data = array(

// 	array('TEST','TEST','TEST','TEST','TEST','TEST','TEST','TEST')
// );


$output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $total_pages,
            //"recordsFiltered" => 2,//$this->recharge->count_filtered($operator),
            "data" => $data,
        );

//output to json format
echo json_encode($output);
exit;

?>