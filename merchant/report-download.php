<?php

session_start();
if ($_SESSION['ORDER_DETAILS']['loggedIN'] != true) {
    header('Location: login.php');
}
require_once '../includes/configure.php';


$method_details = array(
    'bkash'=>array('bKash','bKash','2.00'),
    'dbbl_mobile'=>array('Rocket','DBBL','2.00'),
    'dbbl_nexus'=>array('Nexus','DBBL','2.50'),
    'ebl_master'=>array('Mastercard','EBL','2.50'),
    'ebl_visa'=>array('Visa','EBL','2.50'),
    'ibbl'=>array('iBanking','IBBL','2.00'),
    'mCash_iBank'=>array('Mcash','IBBL','2.00'),
    'mx'=>array('American Express','CBL','3.50'),
    'mx_master_card'=>array('Mastercard','CBL','2.50'),
    'mx_visa'=>array('Visa','CBL','2.50'),
    'tbl'=>array('TBL','TBL','2.50'),
    'tbl-ITCL'=>array('Visa','TBL ITCL','2.50'),
    'tbl-MB'=>array('t-cash','t-cash','2.00'),
    'bkash_api'=>array('bKash','bKash','2.00'),
    'upay'=>array('Upay','Upay','2.00')
);

$file_location = '/tmp/';
if (PHP_OS == 'WINNT') {
    $file_location = 'C:\svn\tmp\\';
}

$from_date = $_GET['from_date'].' 00:00:00';;
$to_date = $_GET['to_date'].' 23:59:59';
$Ucode = $_GET['Ucode'];
$sql = "SELECT
        @a:=@a+1 serial_number,
        txid,
        order_id,
        bank_tx_id,
        gateway,        
        method,
        gw_time,
        amount        
FROM
        sp_epay,
        (SELECT @a:= 0) AS a
WHERE
 
    txid LIKE '".$Ucode."%'
AND
        bank_status='SUCCESS'
AND        
        gw_time >= '".$from_date."' and gw_time <= '".$to_date."'";
 

$query = mysqli_query($GLOBALS["___mysqli_sm"], $sql);

$Ucode = $_SESSION['ORDER_DETAILS']['Ucode'];
$count = 0;
$total_amount = 0;
$total_commission_amount = 0;
$total_paid_amount = 0;
if(isset($Ucode) && ($Ucode == 'RCT') )
{
    $csv_export_data[] = array('#SN','Transaction ID','Order ID','Bank Ref ID','Bank Name','Method','Transaction Time','Amount','Commission%','Commission (BDT)','Net Payable');
}
else
{
    $csv_export_data[] = array('#SN','Transaction ID','Order ID','Bank Ref ID','Bank Name','Method','Transaction Time','Amount');
}    
while($row = mysqli_fetch_assoc($query)) {        
    $csv_export_data[$row['serial_number']]['serial_number'] = $row['serial_number'];
    $csv_export_data[$row['serial_number']]['txid'] = $row['txid'];
    $csv_export_data[$row['serial_number']]['order_id'] = $row['order_id'];
    $csv_export_data[$row['serial_number']]['bank_tx_id'] = $row['bank_tx_id'];
    $csv_export_data[$row['serial_number']]['gateway'] = $method_details[$row['method']][1];
    $csv_export_data[$row['serial_number']]['method'] = $method_details[$row['method']][0];
    $csv_export_data[$row['serial_number']]['gw_time'] = $row['gw_time'];
    $csv_export_data[$row['serial_number']]['amount'] = $row['amount'];
    // Robi Commission setting
    if(isset($Ucode) && ($Ucode == 'RCT') )
    {
        $csv_export_data[$row['serial_number']]['commsssion'] = $commission =  $method_details[$row['method']][2];
        $csv_export_data[$row['serial_number']]['commission_amount'] = $commission_amount = ($row['amount']*$commission)/100;
        $csv_export_data[$row['serial_number']]['paid_amount'] = $paid_amount = $row['amount'] - $commission_amount;
        $total_commission_amount += $commission_amount;
        $total_paid_amount += $paid_amount;        
    }
    
    $total_amount += $row['amount'];        
}

if(isset($Ucode) && ($Ucode == 'RCT') )
{
    $csv_export_data['total'] = array('','','','','','','Total:',$total_amount,'',$total_commission_amount,$total_paid_amount);
}
else
{
    $csv_export_data['total'] = array('','','','','','','Total:',$total_amount);    
}    


//echo "<pre>";
//print_r($csv_export_data);
//exit();

$outputcsvfile = "sales_history.csv";
//$fp = fopen($outputcsvfile, 'w');
header('Content-disposition: attachment; filename='.$outputcsvfile);
header('Content-type: application/CSV');
$fp = fopen('php://output', 'w');
foreach ($csv_export_data as $key=>$val) {
    @fputcsv($fp, $val);
}
fclose($fp);

?>
