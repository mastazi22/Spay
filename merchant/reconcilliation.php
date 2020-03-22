<?php 
/*
##### Author : A.K.M. Nazmul Hasan
##### Email : nazmul.hasan@shurjomukhi.com.bd

*/

session_start();
if(!$_SESSION['ORDER_DETAILS']['loggedIN'])
{
    
 header('Location: login.php');
}
?>
<?php
require_once '../includes/configure.php';
$userid=$_SESSION['ORDER_DETAILS']['userID'];
if(isset($_REQUEST['txnID']) and $_POST['txnID']!="" and $userid!="" and $_POST['bankRefNum']!="" and  $_POST['txnAmount']!="" ){
    
    if($userid!="")
    {
    $query_sql="select * from sp_epay where uid='".$userid."' and txid='".$_POST['txnID']."' and bank_tx_id='".$_POST['bankRefNum']."' and amount='".$_POST['txnAmount']."'";

  $res=mysqli_query($GLOBALS["___mysqli_sm"],$query_sql);
  $data=mysqli_fetch_object($res);
 
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('Asia/Dhaka'));
    $current_time = $date->format("Y-m-d H:i:s");
    

  if(mysqli_num_rows($res)==1)
  {
       $timediff=  round((strtotime($current_time)-strtotime($data->gw_time))/3600);
       
        if(($timediff)<24)
       {
        $sql="update sp_epay set Reconciliation='OPEN' where id='".$data->id."'";
        $flag=mysqli_query($GLOBALS["___mysqli_sm"],$sql);
        if($flag)
        {
        $error_flag="Your Request has been Saved.";
        }
        else
        {
            $error_flag="There is no data.";
            
        }
       }
       else
       {
           
           $error_flag="Sorry.Your time limit of 24 hrs is over.";
       }
   
  }
  else
  {
      $error_flag="There is no data.";
  }
 
    }

}
else
{
  $error_flag="Please fill the required information";
    
}

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
	<div style='float:left;'><b>Merchant Admin Panel  (Logged in as : <span style="color:#ff0000;"> <?php echo $_SESSION['ORDER_DETAILS']['userName']; ?> </span> )</b>
    	   </div>
	<a href="index.php" style="font-weight:bold">Home </a>|| 
	<a href="reconcilliation.php" style="font-weight:bold">Reconcilliation </a>||
	<a href="sales_history.php" style="font-weight:bold">Sales History </a>|| 
	<a href="withdraw_history.php" style="font-weight:bold">Withdraw History </a>|| 
	
       <a href="logout.php" style="font-weight:bold">Logout </a>
    </div> <!-- end of templatemo_menu -->
    <!-- END of middle -->
<div id="templatemo_main_top"></div>
    <div id="templatemo_main">
    	<div id="product_slider">
    	  <div class="cleaner">
    	
        <div class="cleaner h20"></div>
        <h3>Reconcilliation:</h3>
			<form id="form1" name="form1" method="post" action="" autocomplete="off">
    
               <b><span style='color:red;'> <?php if($error_flag!=""){echo $error_flag;} ?></span></b>
               <table name="myTable" id="myTable" border="0" width="100%" >
                       <tr class="row" >
                           <td>Txn. ID</td>
						   <td> <input name="txnID" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
							</td>
                             </tr>
							 <tr class="row" >
                           <td>Bank Ref. Number</td>
						   <td><input name="bankRefNum" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
							</td>
                             </tr>
							  <tr class="row" >
                           <td>Txn. Amount</td>
						   <td><input name="txnAmount" type="text" class="controls" value="" size="20" maxlength="40"><span class="RequiredFieldValidator1">*</span>
							</td>
                             </tr>
							 <tr class="row" >
                           <td></td>
<td><input name="submit" type="submit" class="controls" value="Proceed" >
                              <input name="action" type="hidden" value="">
                              <input name="sequence" type="hidden" value="15">
           </td>
                             </tr>
					</table>
               
               
                              
       		
				
 
    </form>
			
				
			</div> 
		
    	  </div>
      
        
        <!-- END of content -->
    </div> <!-- END of main -->
    
    <div id="templatemo_footer">
        <center>
			Copyright &copy 2018 payPoint | Developed by <a href="http://www.shurjomukhi.com.bd" target="_parent">shurjoMukhi</a>
		</center>
    </div> <!-- END of footer -->   
   
</div>

</body>
</html>