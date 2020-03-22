<?php 
session_start();
if( $_SESSION['ORDER_DETAILS']['loggedIN'] != true)
{
    header( 'Location: login.php' );
}
require_once '../includes/configure.php';

        /* Get data. */
	 
        $sql = "select *from sp_epay where  id = {$_REQUEST['id']}";
        $sql1=mysqli_query($GLOBALS["___mysqli_sm"],$sql);
        $Ucode = $_SESSION['ORDER_DETAILS']['Ucode']; 

 $method_details = array(
  'bkash'=>array('bKash','bKash'),
  'dbbl_mobile'=>array('Rocekt','DBBL'),
  'dbbl_nexus'=>array('Nexus','DBBL'),
  'ebl_master'=>array('Mastercard','EBL'),
  'ebl_visa'=>array('Visa','EBL'),
  'ibbl'=>array('iBanking','IBBL'),
  'mCash_iBank'=>array('Mcash','IBBL'),
  'mx'=>array('American Express','CBL'),
  'mx_master_card'=>array('Mastercard','CBL'),
  'mx_visa'=>array('Visa','CBL'),
  'tbl'=>array('TBL','TBL'),
  'tbl-ITCL'=>array('Visa','TBL ITCL'),
  'tbl-MB'=>array('t-cash','t-cash'),
  'bkash_api'=>array('bKash','bKash'),
  'upay'=>array('Upay','Upay')
  );
  
  $title = array(
    'id'=>'#SN',
    'uid'=>'User ID',
    'order_id'=>'Order ID',
    'txid'=>'Transaction ID',
    'amount'=>'Amount',
    'method'=>'Instrument',
    'clientip'=>'Client IP',
    'intime'=>'Transaction in time',
    'fwdtime'=>'Transaction Forward time',
    'returnurl'=>'Return Url',
    'bank_tx_id'=>'Bank Trnasaction ID',
    'gw_time'=>'Gateway time',
    'return_code'=>'Return Code',
    'remarks'=>'Remarks',
    'reconciliation'=>'Reconcilliation',
    'client_error'=>'Eroor',
    'bank_status'=>'Status',
    'card_holder_name'=>'Card holder name',
    'card_number'=>'Card number',
    'outtime'=>'Out time',
    'ip_status'=>'IP Status',
    'smUid'=>'Sm User ID',
    'sp_epaycol'=>''

  );       

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>..::Shurjopay: Merchant Admin Panel::..</title>
<meta name="keywords" content="Brac Bank, payPoint, Brac Bank @ payPoint" />
<meta name="description" content="Brac Bank, payPoint, Brac Bank @ payPoint" />
<link href="css/templatemo_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="js/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.9.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>

<script type="text/javascript">
$(function(){
	$('#todate').datetimepicker({
		showSecond: false,
		showMinute: false,
		showHour: false,
		dateFormat: 'yy-mm-dd'
		
	});	
	$('#fromdate').datetimepicker({
		showSecond: false,
		showMinute: false,
		showHour: false,
		dateFormat: 'yy-mm-dd'
		
	});		
});
function frmSubmit(act){
	document.getElementById("myForm").action="search-adm.php?orderfield="+act;
	document.getElementById("myForm").submit();
}
</script>

</head>

<body id="home">
<div id="templatemo_wrapper">
	<div id="templatemo_header">
    	<div id="site_title"><h1><a href="http://shurjopay.com.bd/"></a></h1></div>
        
        <div id="header_right">
             <h1><a href="http://<?php echo $logo->merchant_domain; ?>"  style="height: 50px; float: right;  margin-top: 30px;  width: 200px; padding: 22px 0 0 89px; background: url(../img/merchant_logo/<?php echo $logo->merchant_logo; ?>) no-repeat top right"></a></h1>
      </div> <!-- END -->
    </div> <!-- END of header -->
    
    <div style="padding:15px; text-align:right;">
	<div style='float:left;'><b>Merchant Admin Panel  (Logged in as : <span style="color:#ff0000;"> <?php echo $_SESSION['ORDER_DETAILS']['userName']; ?></span>)</b>
    	   </div>
	<a href="index.php" style="font-weight:bold">Home </a>|| 
  <?php if($Ucode != 'RCT'):?>
  <a href="reconcilliation.php" style="font-weight:bold">Reconcilliation </a>|| 
  <a href="withdraw_history.php" style="font-weight:bold">Withdraw History </a>|| 
  <?php endif;?>  
  <a href="sales_history.php" style="font-weight:bold">Sales History </a>||   
       <a href="logout.php" style="font-weight:bold">Logout </a>
    </div> <!-- end of templatemo_menu -->
    <!-- END of middle -->
<div id="templatemo_main_top"></div>
    <div id="templatemo_main">
    	<div id="product_slider">
    	  <div class="cleaner">
    	        
        <div class="cleaner h20"></div>
        <h3>View Details:</h3>
		<p> <table name="myTable" id="myTable" border="0" width="100%" >            
                   <?php                         
                       $counter=0;                        
                       $sql2=  mysqli_fetch_array($sql1);

			// For Zenith.com Policy Number
			if( isset( $_SESSION['ORDER_DETAILS']['Ucode'] ) && ( $_SESSION['ORDER_DETAILS']['Ucode'] == 'ZEN') )                                     
			   {    
			      echo "<tr class='rowincode'>";
				$reurl = explode("=",$sql2['returnurl']);
			      if(isset($reurl[1]) && !empty($reurl[1]))                                         
			      {
			   	  echo "<td style='font-size:12px;font-weight:bold;height:25px'>Policy ID/Employee ID </td><td>:</td><td>".$reurl[1]."</td>";
			      } else {
				 echo "<td style='font-size:12px;font-weight:bold;height:25px'>Policy ID/Employee ID </td><td>:</td><td>".'N/A'."</td>";
			      }
			     echo "</tr>";
			}	
		
                         foreach($sql2 as $key => $rows) {

                            if(!is_digits($key) and $key!='gw_return_id' and $key!='gw_return_msg' and $key!='bank_response' and $key!='gateway')
			    {
                            if($counter%2 == 0){
                              echo "<tr class='rowincode'>";
                            }
                            else
                            {
                                echo "<tr>";
                            }
                            if($key == 'gw_time' || $key =='fwdtime' || $key =='intime' || $key =='outtime'){
                                $rows = date('d-m-Y h:i:s A', strtotime($rows) );
                            }
                            if($key == 'method')
                            {
                              echo "<td style='font-size:12px;font-weight:bold;height:25px'> {$title[$key]}</td><td>:</td><td>".$method_details[$rows][0]."</td>";
                            }
                            elseif($key == 'amount')
                            {
                                echo "<td style='font-size:12px;font-weight:bold;height:25px'> {$title[$key]}</td><td>:</td><td>".number_format($rows,2)."</td>";
                            }
                            else
                            {    
                            echo "<td style='font-size:12px;font-weight:bold;height:25px'> {$title[$key]}</td><td>:</td><td>".$rows."</td>";
                            }
                            echo "</tr>";                            
                            $counter+=1;
                            
                        } 
                         }
                        
                        function is_digits($element) {
                            return !preg_match ("/[^0-9]/", $element);
                        }
                   ?>
        </table>
		<div class="cleaner h20"></div>
		
        <div class="cleaner"></div>
    	  </div>
      </div>
        
        <!-- END of content -->
    </div> <!-- END of main -->
    
    <div id="templatemo_footer">
        <center>
			Copyright &copy 2018  payPoint | Developed by <a href="http://www.shurjomukhi.com.bd" target="_parent">shurjoMukhi</a>
		</center>
    </div> <!-- END of footer -->   
   
</div>

</body>
</html>
